<?php

/*
 * Copyright (C) 2015 
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace AcademicPuma\CiteProc;

/**
 * Description of csl_format
 *
 * @author sebastian
 */

class Format extends RenderingElement {

    /**
     * @var bool
     */
    protected $noOp;

    /**
     * @var string
     */
    protected $format;

    /**
     * @var string
     */
    protected $divClass;

    /**
     * @var array
     */
    protected $quotes;

    /**
     * @var string
     */
    protected $prefix;

    /**
     * @var string
     */
    protected $suffix;

    /**
     * @var string
     */
    protected $display;


    public function __construct($domNode, $citeProc)
    {
        parent::__construct($domNode, $citeProc);
        $this->initFormatting();
    }

    protected function initFormatting() {
        $this->noOp = true;
        $this->format = '';
        if (isset($this->quotes) && strtolower($this->quotes) == "true") {
            $this->quotes = array();
            $this->quotes['punctuation-in-quote'] = $this->citeProc->getLocale()->locale('style_option', 'punctuation-in-quote');
            $this->quotes['open-quote'] = $this->citeProc->getLocale()->locale('term', 'open-quote');
            $this->quotes['close-quote'] = $this->citeProc->getLocale()->locale('term', 'close-quote');
            $this->quotes['open-inner-quote'] = $this->citeProc->getLocale()->locale('term', 'open-inner-quote');
            $this->quotes['close-inner-quote'] = $this->citeProc->getLocale()->locale('term', 'close-inner-quote');
            $this->noOp = false;
        }
        if (isset($this->{'prefix'})) {
            $this->noOp = false;
        }
        if (isset($this->{'suffix'})) {
            $this->noOp = false;
        }
        if (isset($this->{'display'})) {
            $this->noOp = false;
        }

        $this->format .= (isset($this->{'font-style'})) ? 'font-style: ' . $this->{'font-style'} . ';' : '';
        $this->format .= (isset($this->{'font-family'})) ? 'font-family: ' . $this->{'font-family'} . ';' : '';
        $this->format .= (isset($this->{'font-weight'})) ? 'font-weight: ' . $this->{'font-weight'} . ';' : '';
        $this->format .= (isset($this->{'font-variant'})) ? 'font-variant: ' . $this->{'font-variant'} . ';' : '';
        $this->format .= (isset($this->{'text-decoration'})) ? 'text-decoration: ' . $this->{'text-decoration'} . ';' : '';
        $this->format .= (isset($this->{'vertical-align'})) ? 'vertical-align: ' . $this->{'vertical-align'} . ';' : '';
        // $this->format .= (isset($this->{'display'})  && $this->{'display'}  == 'indent')  ? 'padding-left: 25px;' : '';

        if (isset($this->{'text-case'}) ||
                !empty($this->format) ||
                !empty($this->span_class) ||
                !empty($this->div_class)) {
            $this->noOp = false;
        }
    }

    protected function format($text) {

        if (empty($text) || $this->noOp) {
            return $text;
        }

        $quotes = $this->quotes;
        $quotes = is_array($quotes) ? $quotes : array();

        if (isset($this->{'text-case'})) {
            switch ($this->{'text-case'}) {
                case 'uppercase':
                    $text = mb_strtoupper($text);
                    break;
                case 'lowercase':
                    $text = mb_strtolower($text);
                    break;
                case 'capitalize-all':
                case 'title':
                    $text = mb_convert_case($text, MB_CASE_TITLE);
                    break;
                case 'capitalize-first':
                    $chr1 = mb_strtoupper(mb_substr($text, 0, 1));
                    $text = $chr1 . mb_substr($text, 1);
                    break;
            }
        }

        $prefix = $this->prefix;
        $prefix .= isset($quotes['open-quote']) ? $quotes['open-quote'] : '';

        $suffix = $this->suffix;

        if (isset($quotes['close-quote']) && !empty($suffix) && isset($quotes['punctuation-in-quote'])) {
            if (strpos($suffix, '.') !== FALSE || strpos($suffix, ',') !== FALSE) {
                $suffix = $suffix . $quotes['close-quote'];
            }
        } elseif (isset($quotes['close-quote'])) {
            $suffix = $quotes['close-quote'] . $suffix;
        }

        if (!empty($suffix)) { // gaurd against repeaded suffixes...
            $no_tags = strip_tags($text);
            if (strlen($no_tags) && ($no_tags[(strlen($no_tags) - 1)] == $suffix[0])) {
                $suffix = substr($suffix, 1);
            }
        }

        if (!empty($this->format) || !empty($this->span_class)) {
            $style = (!empty($this->format)) ? 'style="' . $this->format . '" ' : '';
            $class = (!empty($this->span_class)) ? 'class="' . $this->span_class . '"' : '';
            $text = '<span ' . $class . $style . '>' . $text . '</span>';
        }

        $divClass = $divStyle = '';

        if (!empty($this->div_class)) {
            $divClass = (!empty($this->divClass)) ? 'class="' . $this->divClass . '"' : '';
        }
        if ($this->display == 'indent') {
            $divStyle = 'style="text-indent: 0px; padding-left: 45px;"';
        }
        if ($divClass || $divStyle) {
            return '<div ' . $divClass . $divStyle . '>' . $prefix . $text . $suffix . '</div>';
        }

        return $prefix . $text . $suffix;
    }

}

