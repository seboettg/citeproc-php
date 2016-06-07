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

class Names extends Format implements Renderable
{

    private $substitutes;

    function initFormatting()
    {
        //   $this->span_class = 'authors';
        parent::initFormatting();
    }

    function init($domNode, $citeProc)
    {
        $etal = '';
        $tag = $domNode->getElementsByTagName('substitute')->item(0);
        if ($tag) {
            $this->substitutes = Factory::create($tag, $citeProc);
            $domNode->removeChild($tag);
        }

        $tag = $domNode->getElementsByTagName('et-al')->item(0);
        if ($tag) {
            $etal = Factory::create($tag, $citeProc);
            $domNode->removeChild($tag);
        }

        $var = $domNode->getAttribute('variable');
        foreach ($domNode->childNodes as $node) {
            if ($node->nodeType == 1) {
                $element = Factory::create($node, $citeProc);
                if (($element instanceof Label))
                    $element->variable = $var;
                if (($element instanceof Name) && $etal) {
                    $element->etal = $etal;
                }
                $this->addElement($element);
            }
        }
    }

    public function render($data, $mode = null)
    {
        $matches = array();
        $variable_parts = array();

        if (!isset($this->delimiter)) {
            $style_delimiter = $this->citeProc->style->{'names-delimiter'};
            $mode_delimiter = $this->citeProc->{$mode}->{'names-delimiter'};
            $this->delimiter = (isset($mode_delimiter)) ? $mode_delimiter : (isset($style_delimiter) ? $style_delimiter : '');
        }

        $variables = explode(' ', $this->variable);

        foreach ($variables as $var) {
            if (in_array($var, $this->citeProc->quash))
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
                                $this->citeProc->quash[] = $var;
                            }
                        }
                    } else { // if it's not a "names" element, just render it
                        $text = $element->render($data, $mode);
                        $this->citeProc->quash[] = isset($element->variable) ? $element->variable : $element->var;
                        if (!empty($text))
                            $variable_parts[] = $text;
                    }
                    if (!empty($matches))
                        break;
                }
            }
        }

        foreach ($matches as $var) {
            if (in_array($var, $this->citeProc->quash) && in_array($var, $variables))
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
