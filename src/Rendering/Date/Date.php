<?php
declare(strict_types=1);
/*
 * citeproc-php
 *
 * @link        http://github.com/seboettg/citeproc-php for the source repository
 * @copyright   Copyright (c) 2016 Sebastian Böttger.
 * @license     https://opensource.org/licenses/MIT
 */

namespace Seboettg\CiteProc\Rendering\Date;

use Exception;
use Seboettg\CiteProc\CiteProc;
use Seboettg\CiteProc\Exception\CiteProcException;
use Seboettg\CiteProc\Exception\InvalidStylesheetException;
use Seboettg\CiteProc\Rendering\Date\DateRange\DateRangeRenderer;
use Seboettg\CiteProc\Styles\AffixesTrait;
use Seboettg\CiteProc\Styles\DisplayTrait;
use Seboettg\CiteProc\Styles\FormattingTrait;
use Seboettg\CiteProc\Styles\TextCaseTrait;
use Seboettg\CiteProc\Util;
use Seboettg\Collection\Lists\ListInterface;
use Seboettg\Collection\Map\MapInterface;
use Seboettg\Collection\Map\Pair;
use SimpleXMLElement;
use function Seboettg\Collection\Lists\emptyList;
use function Seboettg\Collection\Lists\listOf;
use function Seboettg\Collection\Map\emptyMap;
use function Seboettg\Collection\Map\pair;

class Date
{
    use AffixesTrait,
        DisplayTrait,
        FormattingTrait,
        TextCaseTrait;

    // bitmask: ymd
    public const DATE_RANGE_STATE_NONE         = 0; // 000
    public const DATE_RANGE_STATE_DAY          = 1; // 001
    public const DATE_RANGE_STATE_MONTH        = 2; // 010
    public const DATE_RANGE_STATE_MONTHDAY     = 3; // 011
    public const DATE_RANGE_STATE_YEAR         = 4; // 100
    public const DATE_RANGE_STATE_YEARDAY      = 5; // 101
    public const DATE_RANGE_STATE_YEARMONTH    = 6; // 110
    public const DATE_RANGE_STATE_YEARMONTHDAY = 7; // 111

    private static $localizedDateFormats = [
        'numeric',
        'text'
    ];

    private ListInterface $dateParts;
    private string $form = "numeric";
    private string $variable = "";
    private string $datePartsAttribute = "";

    /**
     * Date constructor.
     * @param SimpleXMLElement $node
     * @throws InvalidStylesheetException
     */
    public function __construct(SimpleXMLElement $node)
    {
        $this->dateParts = emptyList();

        /** @var SimpleXMLElement $attribute */
        foreach ($node->attributes() as $attribute) {
            switch ($attribute->getName()) {
                case 'form':
                    $this->form = (string) $attribute;
                    break;
                case 'variable':
                    $this->variable = (string) $attribute;
                    break;
                case 'date-parts':
                    $this->datePartsAttribute = (string) $attribute;
            }
        }
        /** @var SimpleXMLElement $child */
        foreach ($node->children() as $child) {
            if ($child->getName() === "date-part") {
                $datePartName = (string) $child->attributes()["name"];
                $this->dateParts->add(pair($this->form . "-" . $datePartName, Util\Factory::create($child)));
            }
        }

        $this->initAffixesAttributes($node);
        $this->initDisplayAttributes($node);
        $this->initFormattingAttributes($node);
        $this->initTextCaseAttributes($node);
    }

    /**
     * @param $data
     * @return string
     * @throws InvalidStylesheetException
     * @throws Exception
     */
    public function render($data)
    {
        $ret = "";
        $var = null;
        if (isset($data->{$this->variable})) {
            $var = $data->{$this->variable};
        } else {
            return "";
        }

        try {
            $this->prepareDatePartsInVariable($data, $var);
        } catch (CiteProcException $e) {
            if (isset($data->{$this->variable}->{'raw'}) &&
                !preg_match("/(\p{L}+)\s?([\-\–&,])\s?(\p{L}+)/u", $data->{$this->variable}->{'raw'})) {
                return $this->addAffixes($this->format($this->applyTextCase($data->{$this->variable}->{'raw'})));
            } else {
                if (isset($data->{$this->variable}->{'string-literal'})) {
                    return $this->addAffixes(
                        $this->format($this->applyTextCase($data->{$this->variable}->{'string-literal'}))
                    );
                }
            }
        }

        $form = $this->form;
        $dateParts = !empty($this->datePartsAttribute) ?
            listOf(...explode("-", $this->datePartsAttribute)) : emptyList();
        $this->prepareDatePartsChildren($dateParts, $form);

        // No date-parts in date-part attribute defined, take into account that the defined date-part children will
        // be used.
        if (empty($this->datePartsAttribute) && $this->dateParts->count() > 0) {
            /** @var DatePart $part */
            $dateParts = $this->dateParts->map(fn (Pair $pair) => $pair->getValue()->getName());
        }

        /* cs:date may have one or more cs:date-part child elements (see Date-part). The attributes set on
        these elements override those specified for the localized date formats (e.g. to get abbreviated months for all
        locales, the form attribute on the month-cs:date-part element can be set to “short”). These cs:date-part
        elements do not affect which, or in what order, date parts are rendered. Affixes, which are very
        locale-specific, are not allowed on these cs:date-part elements. */

        if ($this->dateParts->count() > 0) {
            if (!isset($var->{'date-parts'})) { // ignore empty date-parts
                return "";
            }

            if (count($data->{$this->variable}->{'date-parts'}) === 1) {
                list($from) = $this->createDateTime($data->{$this->variable}->{'date-parts'});
                $ret .= $this->iterateAndRenderDateParts($dateParts, $from);
            } elseif (count($var->{'date-parts'}) === 2) { //date range
                list($from, $to) = $this->createDateTime($var->{'date-parts'});


                $interval = $to->diff($from);
                $delimiter = "";
                $toRender = 0;
                if ($interval->y > 0 && $dateParts->contains("year")) {
                    $toRender |= self::DATE_RANGE_STATE_YEAR;
                    $delimiter = $this->dateParts
                        ->filter(fn (Pair $pair) => $pair->getKey() === $this->form . "-year")
                        ->first()
                        ->getValue()
                        ->getRangeDelimiter();
                }
                if ($interval->m > 0 && $from->getMonth() - $to->getMonth() !== 0 && $dateParts->contains("month")) {
                    $toRender |= self::DATE_RANGE_STATE_MONTH;
                    $delimiter = $this->dateParts
                        ->filter(fn (Pair $pair) => $pair->getKey() === $this->form . "-month")
                        ->first()
                        ->getValue()
                        ->getRangeDelimiter();
                }
                if ($interval->d > 0 && $from->getDay() - $to->getDay() !== 0 && $dateParts->contains("day")) {
                    $toRender |= self::DATE_RANGE_STATE_DAY;
                    $delimiter = $this->dateParts
                        ->filter(fn (Pair $pair) => $pair->getKey() === "$this->form-day")
                        ->first()
                        ->getValue()
                        ->getRangeDelimiter();
                }
                if ($toRender === self::DATE_RANGE_STATE_NONE) {
                    $ret .= $this->iterateAndRenderDateParts($dateParts, $from);
                } else {
                    $ret .= $this->renderDateRange($toRender, $from, $to, $delimiter);
                }
            }

            if (isset($var->raw) && preg_match("/(\p{L}+)\s?([\-–&,])\s?(\p{L}+)/u", $var->raw, $matches)) {
                return $matches[1].$matches[2].$matches[3];
            }
        } elseif (!empty($this->datePartsAttribute)) {
            // fallback:
            // When there are no dateParts children, but date-parts attribute in date
            // render numeric
            $data = $this->createDateTime($var->{'date-parts'});
            $ret = $this->renderNumeric($data[0]);
        }

        return !empty($ret) ? $this->addAffixes($this->format($this->applyTextCase($ret))) : "";
    }

    /**
     * @throws Exception
     */
    private function createDateTime(array $dates): array
    {
        $data = [];
        foreach ($dates as $date) {
            $date = $this->cleanDate($date);
            if ($date[0] < 1000) {
                $dateTime = new DateTime(0, 0, 0);
                $dateTime->setDay(0)->setMonth(0)->setYear(0);
                $data[] = $dateTime;
            }
            $dateTime = new DateTime(
                $date[0],
                array_key_exists(1, $date) ? $date[1] : 1,
                array_key_exists(2, $date) ? $date[2] : 1
            );
            if (!array_key_exists(1, $date)) {
                $dateTime->setMonth(0);
            }
            if (!array_key_exists(2, $date)) {
                $dateTime->setDay(0);
            }
            $data[] = $dateTime;
        }

        return $data;
    }

    private function renderDateRange(int $toRender, DateTime $from, DateTime $to, $delimiter): string
    {
        $datePartRenderer = DateRangeRenderer::factory($this, $toRender);
        return $datePartRenderer->parseDateRange($this->dateParts, $from, $to, $delimiter);
    }

    private function hasDatePartsFromLocales(string $format): bool
    {
        $dateXml = CiteProc::getContext()->getLocale()->getDateXml();
        return !empty($dateXml[$format]);
    }

    private function getDatePartsFromLocales($format): array
    {
        $ret = [];
        // date parts from locales
        $dateFromLocale_ = CiteProc::getContext()->getLocale()->getDateXml();
        $dateFromLocale = $dateFromLocale_[$format];

        // no custom date parts within the date element (this)?
        if (!empty($dateFromLocale)) {
            $dateForm = array_filter(
                is_array($dateFromLocale) ? $dateFromLocale : [$dateFromLocale],
                function ($element) use ($format) {
                    /** @var SimpleXMLElement $element */
                    $dateForm = (string) $element->attributes()["form"];
                    return $dateForm === $format;
                }
            );

            //has dateForm from locale children (date-part elements)?
            $localeDate = array_pop($dateForm);

            if ($localeDate instanceof SimpleXMLElement && $localeDate->count() > 0) {
                foreach ($localeDate as $child) {
                    $ret[] = $child;
                }
            }
        }
        return $ret;
    }

    /**
     * @return string
     */
    public function getVariable()
    {
        return $this->variable;
    }

    /**
     * @param $data
     * @param $var
     * @throws CiteProcException
     */
    private function prepareDatePartsInVariable($data, $var)
    {
        if (!isset($data->{$this->variable}->{'date-parts'}) || empty($data->{$this->variable}->{'date-parts'})) {
            if (isset($data->{$this->variable}->raw) && !empty($data->{$this->variable}->raw)) {
                // try to parse date parts from "raw" attribute
                $var->{'date-parts'} = Util\DateHelper::parseDateParts($data->{$this->variable});
            } else {
                throw new CiteProcException("No valid date format");
            }
        }
    }

    /**
     * @param ListInterface $dateParts
     * @param string $form
     * @throws InvalidStylesheetException
     */
    private function prepareDatePartsChildren($dateParts, $form)
    {
        /* Localized date formats are selected with the optional form attribute, which must set to either “numeric”
        (for fully numeric formats, e.g. “12-15-2005”), or “text” (for formats with a non-numeric month, e.g.
        “December 15, 2005”). Localized date formats can be customized in two ways. First, the date-parts attribute may
        be used to show fewer date parts. The possible values are:
            - “year-month-day” - (default), renders the year, month and day
            - “year-month” - renders the year and month
            - “year” - renders the year */

        if ($this->dateParts->count() < 1 && in_array($form, self::$localizedDateFormats)) {
            if ($this->hasDatePartsFromLocales($form)) {
                $datePartsFromLocales = $this->getDatePartsFromLocales($form);
                array_filter($datePartsFromLocales, function (SimpleXMLElement $item) use ($dateParts) {
                    return $dateParts->contains($item["name"]);
                });

                foreach ($datePartsFromLocales as $datePartNode) {
                    $datePart = $datePartNode["name"];
                    $this->dateParts->add(pair("$form-$datePart", Util\Factory::create($datePartNode)));
                }
            } else { //otherwise create default date parts
                foreach ($dateParts as $datePart) {
                    $this->dateParts->add(
                        "$form-$datePart",
                        new DatePart(
                            new SimpleXMLElement('<date-part name="'.$datePart.'" form="'.$form.'" />')
                        )
                    );
                }
            }
        }
    }


    private function renderNumeric(DateTime $date)
    {
        return $date->renderNumeric();
    }

    public function getForm()
    {
        return $this->form;
    }

    private function cleanDate($date)
    {
        $ret = [];
        foreach ($date as $key => $datePart) {
            $ret[$key] = Util\NumberHelper::extractNumber(Util\StringHelper::removeBrackets($datePart));
        }
        return $ret;
    }

    /**
     * @param ListInterface $dateParts
     * @param DateTime $from
     * @return string
     */
    private function iterateAndRenderDateParts(ListInterface $dateParts, DateTime $from): string
    {
        $glue = $this->datePartsHaveAffixes() ? "" : " ";
        $result = $this->dateParts
            ->filter(function (Pair $datePartPair) use ($dateParts) {
                list($_, $p) = explode("-", $datePartPair->getKey());
                return $dateParts->contains($p);
            })
            ->map(fn (Pair $datePartPair) => $datePartPair->getValue()->render($from, $this))
            ->filter()
            ->joinToString($glue);
        return trim($result);
    }

    /**
     * @return bool
     */
    private function datePartsHaveAffixes(): bool
    {
        return $this->dateParts
            ->filter(fn (Pair $datePartPair) =>
                $datePartPair->getValue()->renderSuffix() !== "" || $datePartPair->getValue()->renderPrefix() !== "")
            ->count() > 0;
    }
}
