<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace academicpuma\citeproc;

/**
 * Description of csl_macros
 *
 * @author sebastian
 */

class Macros extends Collection {

    function __construct($macro_nodes, $citeproc) {
        foreach ($macro_nodes as $macro) {
            $macro = Factory::create($macro, $citeproc);
            $this->elements[$macro->name()] = $macro;
        }
    }

    function render_macro($name, $data, $mode) {
        return $this->elements[$name]->render($data, $mode);
    }

}
