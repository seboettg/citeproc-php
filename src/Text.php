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

class Text extends Format implements Renderable
{
    public $source;

    protected $var;

    public function render($data, $mode = null)
    {
        $text = '';
        if (in_array($this->var, $this->citeProc->quash)) {
            return;
        }
        
        switch ($this->source) {
            case 'variable':
                if (!isset($data->{$this->variable}) || empty($data->{$this->variable})) {
                    break;
                }
                if ($this->var == "URL") {
                    $url = $data->{$this->variable};
                    if (preg_match('/^(http|https|ftp):\/\/([A-Z0-9][A-Z0-9_-]*(?:.[A-Z0-9][A-Z0-9_-]*)+):?(d+)?\/?/i', $url)) {
                        $text = '<a href="' . $url . '" rel="nofollow">' . htmlspecialchars($url, ENT_QUOTES, "UTF-8") . '</a>';
                        break;
                    }
                }
                $text = htmlspecialchars($data->{$this->variable}, ENT_QUOTES, "UTF-8"); //$this->data[$this->var];  // include the contents of a variable
                break;
            case 'macro':
                $macro = $this->var;
                $text = $this->citeProc->getMarcos()->renderMacro($macro, $data, $mode); //trigger the macro process
                break;
            case 'term':
                $form = (($form = $this->form)) ? $form : '';
                $text = $this->citeProc->getLocale()->locale('term', $this->var, $form);
                break;
            case 'value':
                $text = $this->var; //$this->var;  // dump the text verbatim
                break;
        }

        if (empty($text)) {
            return;
        }
        return $this->format($text);
    }

    public function getVar()
    {
        return $this->var;
    }

    /**
     * @param \DOMElement $domNode
     * @param CiteProc $citeProc
     */
    protected function init($domNode, $citeProc)
    {
        foreach (array('variable', 'macro', 'term', 'value') as $attr) {
            if ($domNode->hasAttribute($attr)) {
                $this->source = $attr;
                if ($this->source == 'macro') {
                    $this->var = str_replace(' ', '_', $domNode->getAttribute($attr));
                } else {
                    $this->var = $domNode->getAttribute($attr);
                }
            }
        }
    }

}
