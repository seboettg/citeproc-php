<?php


namespace academicpuma\citeproc;

/**
 * Description of csl_collection
 *
 * @author sebastian
 */
class Collection {

    protected $elements = array();

    function addElement($elem) {
        if (isset($elem))
            $this->elements[] = $elem;
    }

    function render($data, $mode = NULL) {
        
    }

    function format($text) {
        return $text;
    }

}