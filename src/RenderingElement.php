<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace academicpuma\citeproc;

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

