<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace academicpuma\citeproc;

/**
 * Description of csl_bibliography
 *
 * @author sebastian
 */

class Bibliography extends Format {

    private $layout = NULL;

    function init($dom_node, $citeproc) {
        $hier_name_attr = $this->get_hier_attributes();
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

    function init_formatting() {
        $this->div_class = 'csl-bib-body';
        parent::init_formatting();
    }

    function render($data, $mode = NULL) {
        $this->citeproc->quash = array();
        $text = $this->layout->render($data, 'bibliography');
        if ($this->{'hanging-indent'} == 'true') {
            $text = '<div style="text-indent: -25px; padding-left: 25px;">' . $text . '</div>';
        }
        $text = str_replace('?.', '?', str_replace('..', '.', $text));
        return $this->format($text);
    }

}
