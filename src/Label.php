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
 * Description of csl_label
 *
 * @author sebastian
 */

class Label extends Format {

    private $plural;

    function render($data, $mode = NULL) {
        $text = '';

        $variables = explode(' ', $this->variable);
        $form = (($form = $this->form)) ? $form : 'long';
        switch ($this->plural) {
            case 'never':
                $plural = 'single';
                break;
            case 'always':
                $plural = 'multiple';
                break;
            case 'contextual':
            default:
        }
        foreach ($variables as $variable) {
            if (isset($data->{$variable})) {
                if (!isset($this->plural) && empty($plural) && is_array($data->{$variable})) {
                    $count = count($data->{$variable});
                    if ($count == 1) {
                        $plural = 'single';
                    } elseif ($count > 1) {
                        $plural = 'multiple';
                    }
                } else {
                    $plural = $this->evaluateStringPluralism($data, $variable);
                }
                if (!empty($data->{$variable}) && ($term = $this->citeproc->get_locale('term', $variable, $form, $plural))) {
                    $text = $term;
                    break;
                }
            }
        }

        if (empty($text))
            return;
        if ($this->{'strip-periods'})
            $text = str_replace('.', '', $text);
        return $this->format($text);
    }

    function evaluateStringPluralism($data, $variable) {
        $str = $data->{$variable};
        $plural = 'single';

        if (!empty($str)) {
//      $regex = '/(?:[0-9],\s*[0-9]|\s+and\s+|&|([0-9]+)\s*[\-\x2013]\s*([0-9]+))/';
            switch ($variable) {
                case 'page':
                    $page_regex = "/([a-zA-Z]*)([0-9]+)\s*(?:–|-)\s*([a-zA-Z]*)([0-9]+)/";
                    $err = preg_match($page_regex, $str, $m);
                    if ($err !== FALSE && count($m) == 0) {
                        $plural = 'single';
                    } elseif ($err !== FALSE && count($m)) {
                        $plural = 'multiple';
                    }
                    break;
                default:
            }
        }
        return $plural;
    }

}
