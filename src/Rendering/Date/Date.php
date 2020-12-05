<?php
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
use Seboettg\Collection\ArrayList;
use SimpleXMLElement;

/**
 * Class Date
 * @package Seboettg\CiteProc\Rendering
 *
 * @author Sebastian Böttger <seboettg@gmail.com>
 */
class Date
{
    use AffixesTrait,
        DisplayTrait,
        FormattingTrait,
        TextCaseTrait;

    // bitmask: ymd
    const DATE_RANGE_STATE_NONE         = 0; // 000
    const DATE_RANGE_STATE_DAY          = 1; // 001
    const DATE_RANGE_STATE_MONTH        = 2; // 010
    const DATE_RANGE_STATE_MONTHDAY     = 3; // 011
    const DATE_RANGE_STATE_YEAR         = 4; // 100
    const DATE_RANGE_STATE_YEARDAY      = 5; // 101
    const DATE_RANGE_STATE_YEARMONTH    = 6; // 110
    const DATE_RANGE_STATE_YEARMONTHDAY = 7; // 111

    private static $localizedDateFormats = [
        'numeric',
        'text'
    ];

    /**
     * @var ArrayList
     */
    private $dateParts;

    /**
     * @var string
     */
    private $form = "";

    /**
     * @var string
     */
    private $variable = "";

    /**
     * @var string
     */
    private $datePartsAttribute = "";

    /**
     * Date constructor.
     * @param SimpleXMLElement $node
     * @throws InvalidStylesheetException
     */
    public function __construct(SimpleXMLElement $node)
    {
        $this->dateParts = new ArrayList();

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
                $this->dateParts->set($this->form . "-" . $datePartName, Util\Factory::create($child));
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
        $dateParts = !empty($this->datePartsAttribute) ? explode("-", $this->datePartsAttribute) : [];
        $this->prepareDatePartsChildren($dateParts, $form);

        // No date-parts in date-part attribute defined, take into account that the defined date-part children will
        // be used.
        if (empty($this->datePartsAttribute) && $this->dateParts->count() > 0) {
            /** @var DatePart $part */
            foreach ($this->dateParts as $part) {
                $dateParts[] = $part->getName();
            }
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
                $data_ = $this->createDateTime($data->{$this->variable}->{'date-parts'});
                $ret .= $this->iterateAndRenderDateParts($dateParts, $data_);
            } elseif (count($var->{'date-parts'}) === 2) { //date range
                $data_ = $this->createDateTime($var->{'date-parts'});
                $from = $data_[0];
                $to = $data_[1];
                $interval = $to->diff($from);
                $delimiter = "";
                $toRender = 0;
                if ($interval->y > 0 && in_array('year', $dateParts)) {
                    $toRender |= self::DATE_RANGE_STATE_YEAR;
                    $delimiter = $this->dateParts->get($this->form."-year")->getRangeDelimiter();
                }
                if ($interval->m > 0 && $from->getMonth() - $to->getMonth() !== 0 && in_array('month', $dateParts)) {
                    $toRender |= self::DATE_RANGE_STATE_MONTH;
                    $delimiter = $this->dateParts->get($this->form."-month")->getRangeDelimiter();
                }
                if ($interval->d > 0 && $from->getDay() - $to->getDay() !== 0 && in_array('day', $dateParts)) {
                    $toRender |= self::DATE_RANGE_STATE_DAY;
                    $delimiter = $this->dateParts->get($this->form."-day")->getRangeDelimiter();
                }
                if ($toRender === self::DATE_RANGE_STATE_NONE) {
                    $ret .= $this->iterateAndRenderDateParts($dateParts, $data_);
                } else {
                    $ret .= $this->renderDateRange($toRender, $from, $to, $delimiter);
                }
            }

            if (isset($var->raw) && preg_match("/(\p{L}+)\s?([\-\–&,])\s?(\p{L}+)/u", $var->raw, $matches)) {
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
     * @param array $dates
     * @return array
     * @throws Exception
     */
    private function createDateTime($dates)
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

    /**
     * @param int $toRender
     * @param DateTime $from
     * @param DateTime $to
     * @param $delimiter
     * @return string
     */
    private function renderDateRange($toRender, DateTime $from, DateTime $to, $delimiter)
    {
        $datePartRenderer = DateRangeRenderer::factory($this, $toRender);
        return $datePartRenderer->parseDateRange($this->dateParts, $from, $to, $delimiter);
    }

    /**
     * @param string $format
     * @return bool
     */
    private function hasDatePartsFromLocales($format)
    {
        $dateXml = CiteProc::getContext()->getLocale()->getDateXml();
        return !empty($dateXml[$format]);
    }

    /**
     * @param string $format
     * @return array
     */
    private function getDatePartsFromLocales($format)
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
     * @param $dateParts
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
                    return in_array($item["name"], $dateParts);
                });

                foreach ($datePartsFromLocales as $datePartNode) {
                    $datePart = $datePartNode["name"];
                    $this->dateParts->set("$form-$datePart", Util\Factory::create($datePartNode));
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
     * @param array $dateParts
     * @param array $data_
     * @return string
     */
    private function iterateAndRenderDateParts(array $dateParts, array $data_)
    {
        $result = [];
        /** @var DatePart $datePart */
        foreach ($this->dateParts as $key => $datePart) {
            /** @noinspection PhpUnusedLocalVariableInspection */
            list($f, $p) = explode("-", $key);
            if (in_array($p, $dateParts)) {
                $result[] = $datePart->render($data_[0], $this);
            }
        }
        $result = array_filter($result);
        $glue = $this->datePartsHaveAffixes() ? "" : " ";
        $return = implode($glue, $result);
        return trim($return);
    }

    /**
     * @return bool
     */
    private function datePartsHaveAffixes()
    {
        $result = $this->dateParts->filter(function (DatePart $datePart) {
            return $datePart->renderSuffix() !== "" || $datePart->renderPrefix() !== "";
        });
        return $result->count() > 0;
    }
}
