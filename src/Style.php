<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace academicpuma\citeproc\php;

/**
 * Description of csl_style
 *
 * @author sebastian
 */

class Style extends Element {

    function __construct($dom_node = NULL, $citeproc = NULL) {
        if ($dom_node) {
            $this->set_attributes($dom_node);
        }
    }

}
