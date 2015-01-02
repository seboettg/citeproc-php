<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace academicpuma\citeproc;

/**
 * Description of csl_number
 *
 * @author sebastian
 */

class Number extends Format {

    function render($data, $mode = NULL) {
        $var = $this->variable;

        if (!$var || empty($data->$var))
            return;

        //   $form = $this->form;

        switch ($this->form) {
            case 'ordinal':
                $text = $this->ordinal($data->$var);
                break;
            case 'long-ordinal':
                $text = $this->long_ordinal($data->$var);
                break;
            case 'roman':
                $text = $this->roman($data->$var);
                break;
            case 'numeric':
            default:
                $text = $data->$var;
                break;
        }
        return $this->format($text);
    }

    function ordinal($num) {
        if (($num / 10) % 10 == 1) {
            $num .= $this->citeproc->get_locale('term', 'ordinal-04');
        } elseif ($num % 10 == 1) {
            $num .= $this->citeproc->get_locale('term', 'ordinal-01');
        } elseif ($num % 10 == 2) {
            $num .= $this->citeproc->get_locale('term', 'ordinal-02');
        } elseif ($num % 10 == 3) {
            $num .= $this->citeproc->get_locale('term', 'ordinal-03');
        } else {
            $num .= $this->citeproc->get_locale('term', 'ordinal-04');
        }
        return $num;
    }

    function long_ordinal($num) {
        $num = sprintf("%02d", $num);
        $ret = $this->citeproc->get_locale('term', 'long-ordinal-' . $num);
        if (!$ret) {
            return $this->ordinal($num);
        }
        return $ret;
    }

    function roman($num) {
        $ret = "";
        if ($num < 6000) {
            $ROMAN_NUMERALS = array(
                array("", "i", "ii", "iii", "iv", "v", "vi", "vii", "viii", "ix"),
                array("", "x", "xx", "xxx", "xl", "l", "lx", "lxx", "lxxx", "xc"),
                array("", "c", "cc", "ccc", "cd", "d", "dc", "dcc", "dccc", "cm"),
                array("", "m", "mm", "mmm", "mmmm", "mmmmm")
            );
            $numstr = strrev($num);
            $len = strlen($numstr);
            for ($pos = 0; $pos < $len; $pos++) {
                $n = $numstr[$pos];
                $ret = $ROMAN_NUMERALS[$pos][$n] . $ret;
            }
        }

        return $ret;
    }

}