<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace academicpuma\citeproc\php;

/**
 * Description of csl_et_al
 *
 * @author sebastian
 */


class EtAl extends Text {

    function __construct($dom_node = NULL, $citeproc = NULL) {
        $this->var = 'et-al';
        $this->source = 'term';
        parent::__construct($dom_node, $citeproc);
    }

}
