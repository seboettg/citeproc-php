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
 * Description of csl_style
 *
 * @author sebastian
 */

class Style extends Element
{

    public function __construct($domNode = null, $citeProc = null)
    {
        //TODO: Constructor of an inherited class needs the call to the parent constructor, but doing so an error appears.
        // Removing inheritance does not work as well, because the setAttributes-Method is missing.

        if ($domNode) {
            $this->setAttributes($domNode);
        }
    }

}
