<?php
/*
 * This file is a part of HDS (HeBIS Discovery System). HDS is an 
 * extension of the open source library search engine VuFind, that 
 * allows users to search and browse beyond resources. More 
 * Information about VuFind you will find on http://www.vufind.org
 * 
 * Copyright (C) 2016 
 * HeBIS Verbundzentrale des HeBIS-Verbundes 
 * Goethe-Universität Frankfurt / Goethe University of Frankfurt
 * http://www.hebis.de
 * 
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 */

namespace Seboettg\CiteProc\Rendering;
use Seboettg\CiteProc\CiteProc;
use Seboettg\CiteProc\Styles\AffixesTrait;
use Seboettg\CiteProc\Styles\DisplayTrait;
use Seboettg\CiteProc\Styles\FormattingTrait;
use Seboettg\CiteProc\Styles\TextCaseTrait;


/**
 * Class Number
 * @package Seboettg\CiteProc\Rendering
 *
 * @author Sebastian Böttger <boettger@hebis.uni-frankfurt.de>
 */
class Number
{

    use FormattingTrait,
        AffixesTrait,
        TextCaseTrait,
        DisplayTrait;

    private $variable;

    private $form;

    static $ROMAN_NUMERALS = [
        ["", "i", "ii", "iii", "iv", "v", "vi", "vii", "viii", "ix"],
        ["", "x", "xx", "xxx", "xl", "l", "lx", "lxx", "lxxx", "xc"],
        ["", "c", "cc", "ccc", "cd", "d", "dc", "dcc", "dccc", "cm"],
        ["", "m", "mm", "mmm", "mmmm", "mmmmm"]
    ];

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
                $text = self::roman($data->{$this->variable});
                break;
            case 'numeric':
            default:
                $text = $data->{$this->variable};
                break;
        }
        return $this->wrapDisplayBlock($this->addAffixes($this->format($this->applyTextCase($text))));
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

    /**
     * @param $num
     * @return string
     */
    public static function roman($num) {
        $ret = "";
        if ($num < 6000) {

            $numStr = strrev($num);
            $len = strlen($numStr);
            for ($pos = 0; $pos < $len; $pos++) {
                $n = $numStr[$pos];
                $ret = self::$ROMAN_NUMERALS[$pos][$n] . $ret;
            }
        }
        return $ret;
    }
}