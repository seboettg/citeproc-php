<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace academicpuma\citeproc\php;

/**
 * Description of csl_else
 *
 * @author sebastian
 */

class PElse extends PIf {

    function evaluate($data = NULL) {
        return TRUE; // the last else always returns TRUE
    }

}
