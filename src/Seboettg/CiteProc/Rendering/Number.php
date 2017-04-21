<?php
/*
 * citeproc-php
 *
 * @link        http://github.com/seboettg/citeproc-php for the source repository
 * @copyright   Copyright (c) 2016 Sebastian Böttger.
 * @license     https://opensource.org/licenses/MIT
 */

namespace Seboettg\CiteProc\Rendering;
use Seboettg\CiteProc\CiteProc;
use Seboettg\CiteProc\Util;
use Seboettg\CiteProc\Styles\AffixesTrait;
use Seboettg\CiteProc\Styles\DisplayTrait;
use Seboettg\CiteProc\Styles\FormattingTrait;
use Seboettg\CiteProc\Styles\TextCaseTrait;


/**
 * Class Number
 * @package Seboettg\CiteProc\Rendering
 *
 * @author Sebastian Böttger <seboettg@gmail.com>
 */
class Number implements Rendering
{

    const RANGE_DELIMITER_HYPHEN = "-";

    const RANGE_DELIMITER_AMPERSAND = "&";

    const RANGE_DELIMITER_COMMA = ",";

    use FormattingTrait,
        AffixesTrait,
        TextCaseTrait,
        DisplayTrait;

    /**
     * @var string
     */
    private $variable;

    /**
     * @var string
     */
    private $form;

    public function __construct(\SimpleXMLElement $node)
    {
        //<number variable="edition" form="ordinal"/>
        /** @var \SimpleXMLElement $attribute */
        foreach ($node->attributes() as $attribute) {
            switch ($attribute->getName()) {
                case 'variable':
                    $this->variable = (string) $attribute;
                    break;
                case 'form':
                    $this->form = (string) $attribute;
            }
        }

        $this->initFormattingAttributes($node);
        $this->initAffixesAttributes($node);
        $this->initTextCaseAttributes($node);
    }

    /**
     * @param \stdClass $data
     * @param int|null $citationNumber
     * @return string
     */
    public function render($data, $citationNumber = null)
    {
        $lang = (isset($data->language) && $data->language != 'en') ? $data->language : 'en';

        if (empty($this->variable) || empty($data->{$this->variable})) {
            return "";
        }
        switch ($this->form) {
            case 'ordinal':
                $var = $data->{$this->variable};
                if (preg_match("/\s*(\d+)\s*([\-\-\&,])\s*(\d+)\s*/", $var, $matches)) {
                    $num1 = self::ordinal($matches[1]);
                    $num2 = self::ordinal($matches[3]);
                    $text = $this->buildNumberRangeString($num1, $num2, $matches[2]);
                } else {
                    $text = self::ordinal($var);
                }
                break;
            case 'long-ordinal':
                $var = $data->{$this->variable};
                if (preg_match("/\s*(\d+)\s*([\-\-\&,])\s*(\d+)\s*/", $var, $matches)) {
                    if ($this->textCase === "capitalize-first" || $this->textCase === "sentence") {
                        $num1 = self::longOrdinal($matches[1]);
                        $num2 = self::longOrdinal($matches[3]);
                    } else {
                        $num1 = $this->applyTextCase(self::longOrdinal($matches[1]));
                        $num2 = $this->applyTextCase(self::longOrdinal($matches[3]));
                    }
                    $text = $this->buildNumberRangeString($num1, $num2, $matches[2]);
                } else {
                    $text = self::longOrdinal($var);
                }
                break;
            case 'roman':
                $var = $data->{$this->variable};
                if (preg_match("/\s*(\d+)\s*([\-\-\&,])\s*(\d+)\s*/", $var, $matches)) {
                    $num1 = Util\NumberHelper::dec2roman($matches[1]);
                    $num2 = Util\NumberHelper::dec2roman($matches[3]);
                    $text = $this->buildNumberRangeString($num1, $num2, $matches[2]);
                } else {
                    $text = Util\NumberHelper::dec2roman($var);
                }
                break;
            case 'numeric':
            default:
                /*
                 During the extraction, numbers separated by a hyphen are stripped of intervening spaces (“2 - 4”
                 becomes “2-4”). Numbers separated by a comma receive one space after the comma (“2,3” and “2 , 3”
                 become “2, 3”), while numbers separated by an ampersand receive one space before and one after the
                 ampersand (“2&3” becomes “2 & 3”).
                 */
                $var = $data->{$this->variable};
                if (preg_match("/\s*(\d+)\s*([\-\-\&,])\s*(\d+)\s*/", $var, $matches)) {
                    $text = $this->buildNumberRangeString($matches[1], $matches[3], $matches[2]);
                } else {
                    $text = $var;
                }
                break;
        }
        return $this->wrapDisplayBlock($this->addAffixes($this->format($this->applyTextCase($text, $lang))));
    }

    /**
     * @param $num
     * @return string
     */
    public static function ordinal($num) {
        if (($num / 10) % 10 == 1) {
            $ordinalSuffix = CiteProc::getContext()->getLocale()->filter('terms', 'ordinal')->single;
        } elseif ($num % 10 == 1) {
            $ordinalSuffix = CiteProc::getContext()->getLocale()->filter('terms', 'ordinal-01')->single;
        } elseif ($num % 10 == 2) {
            $ordinalSuffix = CiteProc::getContext()->getLocale()->filter('terms', 'ordinal-02')->single;
        } elseif ($num % 10 == 3) {
            $ordinalSuffix = CiteProc::getContext()->getLocale()->filter('terms', 'ordinal-03')->single;
        } else {
            $ordinalSuffix = CiteProc::getContext()->getLocale()->filter('terms', 'ordinal-04')->single;
        }
        if (empty($ordinalSuffix)) {
            $ordinalSuffix = CiteProc::getContext()->getLocale()->filter('terms', 'ordinal')->single;
        }
        return $num . $ordinalSuffix;
    }

    /**
     * @param $num
     * @return string
     */
    public static function longOrdinal($num) {
        $num = sprintf("%02d", $num);
        $ret = CiteProc::getContext()->getLocale()->filter('terms', 'long-ordinal-' . $num)->single;
        if (!$ret) {
            return self::ordinal($num);
        }
        return $ret;
    }

    /**
     * @param string|int $num1
     * @param string|int $num2
     * @param string $delim
     * @return string
     */
    public function buildNumberRangeString($num1, $num2, $delim) {

        if (self::RANGE_DELIMITER_AMPERSAND === $delim) {
            $numRange = "$num1 " . htmlentities(self::RANGE_DELIMITER_AMPERSAND) . " $num2";
        } else if (self::RANGE_DELIMITER_COMMA === $delim) {
            $numRange = $num1 . htmlentities(self::RANGE_DELIMITER_COMMA) . " $num2";
        } else {
            $numRange = $num1 . self::RANGE_DELIMITER_HYPHEN . $num2;
        }
        return $numRange;
    }
}