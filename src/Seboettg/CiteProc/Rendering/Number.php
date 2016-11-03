<?php
/**
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
class Number
{

    use FormattingTrait,
        AffixesTrait,
        TextCaseTrait,
        DisplayTrait;

    private $variable;

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

    public function render($data)
    {
        $lang = (isset($data->language) && $data->language != 'en') ? $data->language : 'en';

        if (empty($this->variable) || empty($data->{$this->variable})) {
            return "";
        }
        switch ($this->form) {
            case 'ordinal':
                $text = self::ordinal($data->{$this->variable});
                break;
            case 'long-ordinal':
                $text = self::longOrdinal($data->{$this->variable});
                break;
            case 'roman':
                $text = Util\Number::dec2roman($data->{$this->variable});
                break;
            case 'numeric':
            default:
                $text = $data->{$this->variable};
                break;
        }
        return $this->wrapDisplayBlock($this->addAffixes($this->format($this->applyTextCase($text, $lang))));
    }

    public static function ordinal($num) {
        if (($num / 10) % 10 == 1) {
            $num .= CiteProc::getContext()->getLocale()->filter('terms', 'ordinal-04')->single;
        } elseif ($num % 10 == 1) {
            $num .= CiteProc::getContext()->getLocale()->filter('terms', 'ordinal-01')->single;
        } elseif ($num % 10 == 2) {
            $num .= CiteProc::getContext()->getLocale()->filter('terms', 'ordinal-02')->single;
        } elseif ($num % 10 == 3) {
            $num .= CiteProc::getContext()->getLocale()->filter('terms', 'ordinal-03')->single;
        } else {
            $num .= CiteProc::getContext()->getLocale()->filter('terms', 'ordinal-04')->single;
        }
        return $num;
    }


    public static function longOrdinal($num) {
        $num = sprintf("%02d", $num);
        $ret = CiteProc::getContext()->getLocale()->filter('terms', 'long-ordinal-' . $num)->single;
        if (!$ret) {
            return self::ordinal($num);
        }
        return $ret;
    }


}