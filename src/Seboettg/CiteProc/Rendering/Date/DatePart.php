<?php

namespace Seboettg\CiteProc\Rendering\Date;
use Seboettg\CiteProc\CiteProc;
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

    public function __construct(\SimpleXMLElement $node)
    {
        foreach ($node->attributes() as $attribute) {
            if ("name" === $attribute->getName()) {
                $this->name = (string) $attribute;
                break;
            }
        }

        $this->initFormattingAttributes($node);
        $this->initAffixesAttributes($node);
        $this->initTextCaseAttributes($node);
    }


    public function render($date, $form)
    {
        $text = '';
        switch ($this->name) {
            case 'year':
                $text = (count($date) > 0) ? $date[0] : '';
                if ($text > 0 && $text < 1000) {
                    $text = $text . CiteProc::getContext()->getLocale()->filter('terms', 'ad')->single;
                } elseif ($text < 0) {
                    $text = $text * -1;
                    $text = $text . CiteProc::getContext()->getLocale()->filter('terms', 'bc')->single;
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
                break;
        }
        return $this->addAffixes($this->format($this->applyTextCase($text)));
    }
}