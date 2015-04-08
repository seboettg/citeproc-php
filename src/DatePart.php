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
 * Description of csl_date_part
 *
 * @author sebastian
 */


class DatePart extends Format {

    function render($date, $mode = NULL) {
        $text = '';

        switch ($this->name) {
            case 'year':
                $text = (isset($date[0])) ? $date[0] : '';
                if ($text > 0 && $text < 500) {
                    $text = $text . $this->citeproc->get_locale('term', 'ad');
                } elseif ($text < 0) {
                    $text = $text * -1;
                    $text = $text . $this->citeproc->get_locale('term', 'bc');
                }
                //return ((isset($this->prefix))? $this->prefix : '') . $date[0] . ((isset($this->suffix))? $this->suffix : '');
                break;
            case 'month':
                $text = (isset($date[1])) ? $date[1] : '';
                if (empty($text) || $text < 1 || $text > 12)
                    return;
                // $form = $this->form;
                switch ($this->form) {
                    case 'numeric': break;
                    case 'numeric-leading-zeros':
                        if ($text < 10) {
                            $text = '0' . $text;
                            break;
                        }
                        break;
                    case 'short':
                        $month = 'month-' . sprintf('%02d', $text);
                        $text = $this->citeproc->get_locale('term', $month, 'short');
                        break;
                    default:
                        $month = 'month-' . sprintf('%02d', $text);
                        $text = $this->citeproc->get_locale('term', $month);
                        break;
                }
                break;
            case 'day':
                $text = (isset($date[2])) ? $date[2] : '';
                break;
        }

        return $this->format($text);
    }

}
