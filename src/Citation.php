<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace academicpuma\citeproc;

/**
 * Description of csl_citation
 *
 * @author sebastian
 */

class Citation extends Format {

    private $layout = NULL;

    function init($dom_node, $citeproc) {
        $options = $dom_node->getElementsByTagName('option');
        foreach ($options as $option) {
            $value = $option->getAttribute('value');
            $name = $option->getAttribute('name');
            $this->attributes[$name] = $value;
        }

        $layouts = $dom_node->getElementsByTagName('layout');
        foreach ($layouts as $layout) {
            $this->layout = new Layout($layout, $citeproc);
        }
    }

    function render($data, $mode = NULL) {
        $this->citeproc->quash = array();

        $text = $this->layout->render($data, 'citation');

        return $this->format($text);
    }

}
