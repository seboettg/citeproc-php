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
 * Description of csl_layout
 *
 * @author sebastian
 */


class Layout extends Format {

    function init_formatting() {
        $this->div_class = 'csl-entry';
        parent::init_formatting();
    }

    function render($data, $mode = NULL) {
        $text = '';
        $parts = array();
        // $delimiter = $this->delimiter;

        foreach ($this->elements as $element) {
            $parts[] = $element->render($data, $mode);
        }

        $text = implode($this->delimiter, $parts);

        if ($mode == 'bibliography' || $mode == 'citation') {
            return $this->format($text);
        } else {
            return $text;
        }
    }

}
