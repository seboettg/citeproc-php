<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace academicpuma\citeproc\php;

/**
 * Description of csl_choose
 *
 * @author sebastian
 */

class Choose extends Element {

    function render($data, $mode = NULL) {
        foreach ($this->elements as $choice) {
            if ($choice->evaluate($data)) {
                return $choice->render($data, $mode);
            }
        }
    }

}
