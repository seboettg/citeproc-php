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
 * Description of csl_text
 *
 * @author sebastian
 */

class Text extends Format {

    public $source;
    protected $var;

    function init($dom_node, $citeproc) {
        foreach (array('variable', 'macro', 'term', 'value') as $attr) {
            if ($dom_node->hasAttribute($attr)) {
                $this->source = $attr;
                if ($this->source == 'macro') {
                    $this->var = str_replace(' ', '_', $dom_node->getAttribute($attr));
                } else {
                    $this->var = $dom_node->getAttribute($attr);
                }
            }
        }
    }

    function init_formatting() {
//    if ($this->variable == 'title') {
//      $this->span_class = 'title';
//    }
        parent::init_formatting();
    }

    function render($data = NULL, $mode = NULL) {
        $text = '';
        if (in_array($this->var, $this->citeproc->quash))
            return;

        switch ($this->source) {
            case 'variable':
                if (!isset($data->{$this->variable}) || empty($data->{$this->variable}))
                    return;
                $text = $data->{$this->variable}; //$this->data[$this->var];  // include the contents of a variable
                break;
            case 'macro':
                $macro = $this->var;
                $text = $this->citeproc->render_macro($macro, $data, $mode); //trigger the macro process
                break;
            case 'term':
                $form = (($form = $this->form)) ? $form : '';
                $text = $this->citeproc->get_locale('term', $this->var, $form);
                break;
            case 'value':
                $text = $this->var; //$this->var;  // dump the text verbatim
                break;
        }

        if (empty($text))
            return;
        return $this->format($text);
    }
    
    public function getVar() {
        return $this->var;
    }

}
