<?php

namespace Seboettg\CiteProc\Rendering\Date;
use Seboettg\CiteProc\CiteProc;
use Seboettg\CiteProc\Rendering\Layout;
use Seboettg\CiteProc\Rendering\Number;
use Seboettg\CiteProc\Styles\AffixesTrait;
use Seboettg\CiteProc\Styles\DelimiterTrait;
use Seboettg\CiteProc\Styles\FormattingTrait;
use Seboettg\CiteProc\Styles\RangeDelimiterTrait;
use Seboettg\CiteProc\Styles\TextCaseTrait;


/**
 * Class DatePart
 * @package Seboettg\CiteProc\Rendering\Date
 *
 * @author Sebastian Böttger <boettger@hebis.uni-frankfurt.de>
 */
class DatePart
{
    use FormattingTrait,
        AffixesTrait,
        TextCaseTrait,
        RangeDelimiterTrait;

    private $name;

    private $form;

    public function __construct(\SimpleXMLElement $node)
    {
        foreach ($node->attributes() as $attribute) {
            if ("name" === $attribute->getName()) {
                $this->name = (string) $attribute;
            }
            if ("form" === $attribute->getName()) {
                $this->form = (string) $attribute;
            }
        }

        $this->initFormattingAttributes($node);
        $this->initAffixesAttributes($node);
        $this->initTextCaseAttributes($node);
    }


    /**
     * @param array $date
     * @param Date $parent
     * @return string
     */
    public function render($date, $parent)
    {
        $date = $date[0];
        $text = "";
        $form = !empty($this->form) ? $this->form : $parent->getForm();
        switch ($this->name) {
            case 'year':
                $text = (count($date) > 0) ? $date[0] : '';
                if ($text > 0 && $text < 1000) {
                    $text = $text . CiteProc::getContext()->getLocale()->filter("terms", "ad")->single;
                } elseif ($text < 0) {
                    $text = $text * -1;
                    $text = $text . CiteProc::getContext()->getLocale()->filter("terms", "bc")->single;
                }
                break;
            case 'month':
                $text = (isset($date[1])) ? $date[1] : '';
                if (empty($text) || $text < 1 || $text > 12) {
                    return "";
                }
                switch ($form) {
                    case 'numeric':
                        break;
                    case 'numeric-leading-zeros':
                        $text = sprintf("%02d", $text);
                        break;
                    case 'short':
                        $month = 'month-' . sprintf('%02d', $text);
                        $text = CiteProc::getContext()->getLocale()->filter('terms', $month, 'short')->single;
                        break;
                    case 'long':
                    default:
                        $month = 'month-' . sprintf('%02d', $text);
                        $text = CiteProc::getContext()->getLocale()->filter('terms', $month)->single;
                        break;
                }
                break;
            case 'day':
                $text = (isset($date[2])) ? $date[2] : '';
                switch ($form) {
                    case 'numeric':
                        break;
                    case 'numeric-leading-zeros':
                        $text = sprintf("%02d", $text);
                        break;
                    case 'ordinal':
                        $limitDayOrdinals = CiteProc::getContext()->getLocale()->filter("options", "limit-day-ordinals-to-day-1");
                        if (!$limitDayOrdinals || Layout::getNumberOfCitedItems() <= 1) {
                            $text = Number::ordinal($text);
                        }
                }
        }
        return !empty($text) ? $this->addAffixes($this->format($this->applyTextCase($text))) : "";
    }

    /**
     * @return string
     */
    public function getForm()
    {
        return $this->form;
    }
}