<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace academicpuma\citeproc\php;

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
