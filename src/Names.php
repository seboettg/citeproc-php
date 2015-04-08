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
 * Description of csl_names
 *
 * @author sebastian
 */

class Names extends Format {

    private $substitutes;

    function init_formatting() {
        //   $this->span_class = 'authors';
        parent::init_formatting();
    }

    function init($dom_node, $citeproc) {
        $etal = '';
        $tag = $dom_node->getElementsByTagName('substitute')->item(0);
        if ($tag) {
            $this->substitutes = Factory::create($tag, $citeproc);
            $dom_node->removeChild($tag);
        }

        $tag = $dom_node->getElementsByTagName('et-al')->item(0);
        if ($tag) {
            $etal = Factory::create($tag, $citeproc);
            $dom_node->removeChild($tag);
        }

        $var = $dom_node->getAttribute('variable');
        foreach ($dom_node->childNodes as $node) {
            if ($node->nodeType == 1) {
                $element = Factory::create($node, $citeproc);
                if (($element instanceof Label))
                    $element->variable = $var;
                if (($element instanceof Name) && $etal) {
                    $element->etal = $etal;
                }
                $this->addElement($element);
            }
        }
    }

    function render($data, $mode = NULL) {
        $matches = array();
        $variable_parts = array();

        if (!isset($this->delimiter)) {
            $style_delimiter = $this->citeproc->style->{'names-delimiter'};
            $mode_delimiter = $this->citeproc->{$mode}->{'names-delimiter'};
            $this->delimiter = (isset($mode_delimiter)) ? $mode_delimiter : (isset($style_delimiter) ? $style_delimiter : '');
        }

        $variables = explode(' ', $this->variable);

        foreach ($variables as $var) {
            if (in_array($var, $this->citeproc->quash))
                continue;
            if (isset($data->{$var}) && (!empty($data->{$var}))) {
                $matches[] = $var;
            }
        }

        if (empty($matches)) { // we don't have any primary suspects, so lets check the substitutes...
            if (isset($this->substitutes)) {
                foreach ($this->substitutes->elements as $element) {
                    if (($element instanceof Names)) { //test to see if any of the other names variables has content
                        $sub_variables = explode(' ', $element->variable);
                        foreach ($sub_variables as $var) {
                            if (isset($data->{$var})) {
                                $matches[] = $var;
                                $this->citeproc->quash[] = $var;
                            }
                        }
                    } else { // if it's not a "names" element, just render it
                        $text = $element->render($data, $mode);
                        $this->citeproc->quash[] = isset($element->variable) ? $element->variable : $element->var;
                        if (!empty($text))
                            $variable_parts[] = $text;
                    }
                    if (!empty($matches))
                        break;
                }
            }
        }

        foreach ($matches as $var) {
            if (in_array($var, $this->citeproc->quash) && in_array($var, $variables))
                continue;
            $text = '';
            if (!empty($data->{$var})) {
                foreach ($this->elements as $element) {
                    if ($element instanceof Label) {
                        $element->variable = $var;
                        $text .= $element->render($data, $mode);
                    } elseif ($element instanceof Name) {
                        $text .= $element->render($data->{$var}, $mode);
                    }
                }
            }
            if (!empty($text))
                $variable_parts[] = $text;
        }

        if (!empty($variable_parts)) {
            $text = implode($this->delimiter, $variable_parts);
            return $this->format($text);
        }

        return;
    }

}
