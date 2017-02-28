<?php
/**
 * citeproc-php
 *
 * @link        http://github.com/seboettg/citeproc-php for the source repository
 * @copyright   Copyright (c) 2016 Sebastian Böttger.
 * @license     https://opensource.org/licenses/MIT
 */

namespace Seboettg\CiteProc\Rendering\Date;

use Seboettg\CiteProc\CiteProc;
use Seboettg\CiteProc\Exception\CiteProcException;
use Seboettg\CiteProc\Styles\AffixesTrait;
use Seboettg\CiteProc\Styles\DisplayTrait;
use Seboettg\CiteProc\Styles\FormattingTrait;
use Seboettg\CiteProc\Styles\TextCaseTrait;
use Seboettg\CiteProc\Util;
use Seboettg\Collection\ArrayList;


/**
 * Class Date
 * @package Seboettg\CiteProc\Rendering
 *
 * @author Sebastian Böttger <seboettg@gmail.com>
 */
class Date
{
                                             // ymd
    const DATE_RANGE_STATE_NONE         = 0; // 000
    const DATE_RANGE_STATE_DAY          = 1; // 001
    const DATE_RANGE_STATE_MONTH        = 2; // 010
    const DATE_RANGE_STATE_MONTHDAY     = 3; // 011
    const DATE_RANGE_STATE_YEAR         = 4; // 100
    const DATE_RANGE_STATE_YEARDAY      = 5; // 101
    const DATE_RANGE_STATE_YEARMONTH    = 6; // 110
    const DATE_RANGE_STATE_YEARMONTHDAY = 7; // 111

    use AffixesTrait,
        DisplayTrait,
        FormattingTrait,
        TextCaseTrait;

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

    private $datePartsAttribute = "";

    public function __construct(\SimpleXMLElement $node)
    {
        $this->dateParts = new ArrayList();

        /** @var \SimpleXMLElement $attribute */
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
        /** @var \SimpleXMLElement $child */
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

        if (!isset($data->{$this->variable}->{'date-parts'}) || empty($data->{$this->variable}->{'date-parts'})) {
            if (isset($data->{$this->variable}->raw) && !empty($data->{$this->variable}->raw)) {
                try {
                    $var->{'date-parts'} = Util\Date::parseDateParts($data->{$this->variable});
                } catch (CiteProcException $e) {
                    return "";
                }
            } else {
                return "";
            }
        }

        // date parts from locales
        $dateFromLocale = CiteProc::getContext()->getLocale()->getDateXml();

        // no custom date parts within the date element (this)?
        if ($this->dateParts->count() <= 0 && !empty($dateFromLocale["date"])) {
            //if exist, add date parts from locales

            $datePartsXml = $dateFromLocale["date"];

            //filter dateParts by form
            $form = $this->form;
            $dateForm = array_filter($datePartsXml, function($element) use ($form){
                /** @var \SimpleXMLElement $element */
                $dateForm = (string) $element->attributes()["form"];
                return  $dateForm === $form;
            });

            //has dateForm from locale children (date-part elements)?
            $localeDate = array_pop($dateForm);
            if ($localeDate instanceof \SimpleXMLElement && $localeDate->count() > 0) {
                //add only date parts defined in date-parts attribute of (this) date element
                $dateParts = explode("-", $this->datePartsAttribute);

                /** @var \SimpleXMLElement $child */
                foreach ($localeDate->children() as $child) {
                    if ($child->getName() === "date-part") {
                        $datePartName = (string) $child->attributes()["name"];
                        if (in_array($datePartName, $dateParts)) {
                            $this->dateParts->set("$form-$datePartName", Util\Factory::create($child));
                        }
                    }
                }
            }
        }


        if ($this->dateParts->count() > 0) {
            // ignore empty date-parts
            if (!isset($var->{'date-parts'})) {
                return "";
            }

            if (count($data->{$this->variable}->{'date-parts'}) === 1) {
                $data_ = $this->createDateTime($var->{'date-parts'});
                /** @var DatePart $datePart */
                foreach ($this->dateParts as $datePart) {
                    $ret .= $datePart->render($data_[0], $this);
                }
            } else if (count($data->{$this->variable}->{'date-parts'}) === 2) { //date range
                $data_ = $this->createDateTime($var->{'date-parts'});
                $from = $data_[0];
                $to = $data_[1];
                $interval = $to->diff($from);
                $delim = "";
                $toRender = 0;
                if ($interval->y > 0) {
                    $toRender |= self::DATE_RANGE_STATE_YEAR;
                    $delim = $this->dateParts->get($this->form . "-year")->getRangeDelimiter();
                }
                if ($interval->m > 0 && $from->getMonth() - $to->getMonth() !== 0) {
                    $toRender |= self::DATE_RANGE_STATE_MONTH;
                    $delim = $this->dateParts->get($this->form . "-month")->getRangeDelimiter();
                }
                if ($interval->d > 0 && $from->getDay() - $to->getDay() !== 0) {
                    $toRender |= self::DATE_RANGE_STATE_DAY;
                    $delim = $this->dateParts->get($this->form . "-day")->getRangeDelimiter();
                }

                $ret = $this->renderDateRange($toRender, $from, $to, $delim);
            } else {
                // throw
            }
        }
        // fallback:
        // When there are no dateParts children, but date-parts attribute in date
        // render numeric
        else if (!empty($this->datePartsAttribute)) {
            $data = $this->createDateTime($var->{'date-parts'});
            $ret = $this->renderNumeric($data);
        }

        return !empty($ret) ? $this->addAffixes($this->format($this->applyTextCase($ret))) : "";
    }

    private function renderNumeric(DateTime $date)
    {
        return $date->renderNumeric();
    }

    public function getForm()
    {
        return $this->form;
    }

    private function createDateTime($dates)
    {
        $data = [];
        foreach ($dates as $date) {
            if ($date[0] < 1000) {
                $dateTime = new DateTime(0,0,0);
                $dateTime->setDay(0)->setMonth(0)->setYear(0);
                $data[] = $dateTime;
            }
            $dateTime = new DateTime($date[0], array_key_exists(1, $date) ? $date[1] : 1, array_key_exists(2, $date) ? $date[2] : 1);
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

    private function renderDateRange($differentParts, DateTime $from, DateTime $to, $delim)
    {
        $ret = "";
        switch ($differentParts) {
            case Date::DATE_RANGE_STATE_YEAR:
                foreach ($this->dateParts as $key => $datePart) {
                    if (strpos($key, "year") !== false) {
                        $ret .= $this->renderOneRangePart($datePart, $from, $to, $delim);
                    }
                    if (strpos($key, "month") !== false) {
                        $day = !empty($d = $from->getMonth()) ? $d : "";
                        $ret .= $day;
                    }
                    if (strpos($key, "day") !== false) {
                        $day = !empty($d = $from->getDay()) ? $datePart->render($from, $this) : "";
                        $ret .= $day;
                    }
                }
                break;
            case Date::DATE_RANGE_STATE_MONTH:
                /**
                 * @var string $key
                 * @var DatePart $datePart
                 */
                foreach ($this->dateParts as $key => $datePart) {
                    if (strpos($key, "year") !== false) {
                        $ret .= $datePart->render($from, $this);
                    }
                    if (strpos($key, "month")) {
                        $ret .= $this->renderOneRangePart($datePart, $from, $to, $delim);
                    }
                    if (strpos($key, "day") !== false) {
                        $day = !empty($d = $from->getDay()) ? $datePart->render($from, $this) : "";
                        $ret .= $day;
                    }
                }
                break;
            case Date::DATE_RANGE_STATE_DAY:
                /**
                 * @var string $key
                 * @var DatePart $datePart
                 */
                foreach ($this->dateParts as $key => $datePart) {
                    if (strpos($key, "year") !== false) {
                        $ret .= $datePart->render($from, $this);
                    }
                    if (strpos($key, "month") !== false) {
                        $ret .= $datePart->render($from, $this);
                    }
                    if (strpos($key, "day")) {
                        $ret .= $this->renderOneRangePart($datePart, $from, $to, $delim);
                    }
                }
                break;
            case Date::DATE_RANGE_STATE_YEARMONTHDAY:
                $i = 0;
                foreach ($this->dateParts as $datePart) {
                    if ($i === $this->dateParts->count() - 1) {
                        $ret .= $datePart->renderPrefix();
                        $ret .= $datePart->renderWithoutAffixes($from, $this);
                    } else {
                        $ret .= $datePart->render($from, $this);
                    }
                    ++$i;
                }
                $ret .= $delim;
                $i = 0;
                foreach ($this->dateParts as $datePart) {
                    if ($i == 0) {
                        $ret .= $datePart->renderWithoutAffixes($to, $this);
                        $ret .= $datePart->renderSuffix();
                    } else {
                        $ret .= $datePart->render($to, $this);
                    }
                    ++$i;
                }
                break;
            case Date::DATE_RANGE_STATE_YEARMONTH:
                $dp = $this->dateParts->toArray();
                $i = 0;
                $dateParts_ = [];
                array_walk($dp, function ($datePart, $key) use (&$i, &$dateParts_, $differentParts) {
                    if (strpos($key, "year") !== false || strpos($key, "month") !== false) {
                        $dateParts_["yearmonth"][] = $datePart;
                    }
                    if (strpos($key, "day") !== false) {
                        $dateParts_["day"] = $datePart;
                    }
                });
                break;
            case Date::DATE_RANGE_STATE_YEARDAY:
                $dp = $this->dateParts->toArray();
                $i = 0;
                $dateParts_ = [];
                array_walk($dp, function ($datePart, $key) use (&$i, &$dateParts_, $differentParts) {
                    if (strpos($key, "year") !== false || strpos($key, "day") !== false) {
                        $dateParts_["yearday"][] = $datePart;
                    }
                    if (strpos($key, "month") !== false) {
                        $dateParts_["month"] = $datePart;
                    }
                });
                break;
            case Date::DATE_RANGE_STATE_MONTHDAY:
                $dp = $this->dateParts->toArray();
                $i = 0;
                $dateParts_ = [];
                array_walk($dp, function ($datePart, $key) use (&$i, &$dateParts_, $differentParts) {
                    $bit = sprintf("%03d", decbin($differentParts));
                    if (strpos($key, "month") !== false || strpos($key, "day") !== false) {
                        $dateParts_["monthday"][] = $datePart;
                    }
                    if (strpos($key, "year") !== false) {
                        $dateParts_["year"] = $datePart;
                    }
                });
                break;
        }
        switch ($differentParts) {
            case Date::DATE_RANGE_STATE_YEARMONTH:
            case Date::DATE_RANGE_STATE_YEARDAY:
            case Date::DATE_RANGE_STATE_MONTHDAY:
                /**
                 * @var $key
                 * @var DatePart $datePart */
                foreach ($dateParts_ as $key => $datePart) {
                    if (is_array($datePart)) {

                        $f  = $datePart[0]->render($from, $this);
                        $f .= $datePart[1]->renderPrefix();
                        $f .= $datePart[1]->renderWithoutAffixes($from, $this);
                        $t  = $datePart[0]->renderWithoutAffixes($to, $this);
                        $t .= $datePart[0]->renderSuffix();
                        $t .= $datePart[1]->render($to, $this);
                        $ret .= $f . $delim . $t;
                    } else {
                        $ret .= $datePart->render($from, $this);
                    }
                }
                break;
        }
        return $ret;
    }

    /**
     * @param $datePart
     * @param $from
     * @param $to
     * @param $delim
     * @return string
     */
    protected function renderOneRangePart(DatePart $datePart, $from, $to, $delim)
    {
        $prefix = $datePart->renderPrefix();
        $from = $datePart->renderWithoutAffixes($from, $this);
        $to = $datePart->renderWithoutAffixes($to, $this);
        $suffix = !empty($to) ? $datePart->renderSuffix() : "";
        return $prefix.$from.$delim.$to.$suffix;
    }

}