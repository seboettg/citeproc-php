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
 * Description of cls_rendering_element
 *
 * @author sebastian
 */

class RenderingElement extends Element {

    function render($data, $mode = NULL) {
        $text_parts = array();

        $delim = $this->delimiter;
        foreach ($this->elements as $element) {
            $text_parts[] = $element->render($data, $mode);
        }
        $text = implode($delim, $text_parts); // insert the delimiter if supplied.

        return $this->format($text);
    }

}

