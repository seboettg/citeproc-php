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
use Seboettg\CiteProc\Rendering\Layout;
use Seboettg\CiteProc\Rendering\Number;
use Seboettg\CiteProc\Styles\AffixesTrait;
use Seboettg\CiteProc\Styles\FormattingTrait;
use Seboettg\CiteProc\Styles\RangeDelimiterTrait;
use Seboettg\CiteProc\Styles\TextCaseTrait;
use SimpleXMLElement;

/**
 * Class DatePart
 * @package Seboettg\CiteProc\Rendering\Date
 *
 * @author Sebastian Böttger <seboettg@gmail.com>
 */
class DatePart
{

    const DEFAULT_RANGE_DELIMITER = "–";

    use FormattingTrait,
        AffixesTrait,
        TextCaseTrait,
        RangeDelimiterTrait;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $form;

    /**
     * @var string
     */
    private $rangeDelimiter;

    /**
     * @var Date
     */
    private $parent;

    public function __construct(SimpleXMLElement $node)
    {
        foreach ($node->attributes() as $attribute) {
            if ("name" === $attribute->getName()) {
                $this->name = (string) $attribute;
            }
            if ("form" === $attribute->getName()) {
                $this->form = (string) $attribute;
            }
            if ("range-delimiter" === $attribute->getName()) {
                $this->rangeDelimiter = (string) $attribute;
            }
        }

        if (empty($this->rangeDelimiter)) {
            $this->rangeDelimiter = self::DEFAULT_RANGE_DELIMITER;
        }

        $this->initFormattingAttributes($node);
        $this->initAffixesAttributes($node);
        $this->initTextCaseAttributes($node);
    }


    /**
     * @param DateTime $date
     * @param Date $parent
     * @return string
     */
    public function render(DateTime $date, Date $parent)
    {
        $this->parent = $parent; //set parent
        $text = $this->renderWithoutAffixes($date);
        return !empty($text) ? $this->addAffixes($text) : "";
    }

    /**
     * @param DateTime $date
     * @param Date|null $parent
     * @return string
     */
    public function renderWithoutAffixes(DateTime $date, Date $parent = null)
    {
        if (!is_null($parent)) {
            $this->parent = $parent;
        }
        $text = "";
        switch ($this->name) {
            case 'year':
                $text = $this->renderYear($date);
                break;
            case 'month':
                $text = $this->renderMonth($date);
                break;
            case 'day':
                $text = $this->renderDay($date);
        }

        return !empty($text) ? $this->format($this->applyTextCase($text)) : "";
    }

    /**
     * @return string
     */
    public function getForm()
    {
        return $this->form;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getRangeDelimiter()
    {
        return $this->rangeDelimiter;
    }

    /**
     * @param DateTime $date
     * @return string|int
     */
    protected function renderYear(DateTime $date)
    {
        $text = $date->getYear();
        if ($text > 0 && $text < 1000) {
            $text = $text . CiteProc::getContext()->getLocale()->filter("terms", "ad")->single;
            return $text;
        } elseif ($text < 0) {
            $text = $text * -1;
            $text = $text . CiteProc::getContext()->getLocale()->filter("terms", "bc")->single;
            return $text;
        }
        return $text;
    }

    /**
     * @param DateTime $date
     * @return string
     */
    protected function renderMonth(DateTime $date)
    {
        if ($date->getMonth() < 1 || $date->getMonth() > 12) {
            return "";
        }

        $text = $date->getMonth();

        $form = !empty($this->form) ? $this->form : "long";
        switch ($form) {
            case 'numeric':
                break;
            case 'numeric-leading-zeros':
                $text = sprintf("%02d", $text);
                break;
            case 'short':
            case 'long':
            default:
                $text = $this->monthFromLocale($text, $form);
                break;
        }
        return $text;
    }

    /**
     * @param DateTime $date
     * @return int|string
     */
    protected function renderDay(DateTime $date)
    {
        if ($date->getDay() < 1 || $date->getDay() > 31) {
            return "";
        }

        $text = $date->getDay();
        $form = !empty($this->form) ? $this->form : $this->parent->getForm();
        switch ($form) {
            case 'numeric':
                break;
            case 'numeric-leading-zeros':
                $text = sprintf("%02d", $text);
                break;
            case 'ordinal':
                $limitDayOrdinals =
                    CiteProc::getContext()->getLocale()->filter("options", "limit-day-ordinals-to-day-1");
                if (!$limitDayOrdinals || Layout::getNumberOfCitedItems() <= 1) {
                    $text = Number::ordinal($text);
                }
        }
        return $text;
    }

    /**
     * @param $text
     * @param $form
     * @return mixed
     */
    protected function monthFromLocale($text, $form)
    {
        if (empty($form)) {
            $form = "long";
        }
        $month = 'month-' . sprintf('%02d', $text);
        $text = CiteProc::getContext()->getLocale()->filter('terms', $month, $form)->single;
        return $text;
    }
}
