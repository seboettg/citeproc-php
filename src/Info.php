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
 * Description of csl_info
 *
 * @author sebastian
 */

class Info {

    public $title;
    public $id;
    public $authors = array();
    public $links = array();

    function __construct($dom_node) {
        $name = array();
        foreach ($dom_node->childNodes as $node) {
            if ($node->nodeType == 1) {
                switch ($node->nodeName) {
                    case 'author':
                    case 'contributor':
                        foreach ($node->childNodes as $authnode) {
                            if ($node->nodeType == 1) {
                                $name[$authnode->nodeName] = $authnode->nodeValue;
                            }
                        }
                        $this->authors[] = $name;
                        break;
                    case 'link':
                        foreach ($node->attributes as $attribute) {
                            $this->links[] = $attribute->value;
                        }
                        break;
                    default:
                        $this->{$node->nodeName} = $node->nodeValue;
                }
            }
        }
    }

}