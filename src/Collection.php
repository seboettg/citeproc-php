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
 * Description of csl_collection
 *
 * @author sebastian
 */
class Collection implements Renderable {

    protected $elements = array();

    protected $delimiter;

    protected function addElement($elem)
    {
        if (isset($elem)) {
            $this->elements[] = $elem;
        }
    }

    public function render($data, $mode = null)
    {
        $textParts = array();
        $delimiter = $this->delimiter;
        foreach ($this->elements as $element) {
            $textParts[] = $element->render($data, $mode);
        }
        $text = implode($delimiter, $textParts); // insert the delimiter if supplied.

        return $this->format($text);
    }

    protected function format($text)
    {
        return $text;
    }

}