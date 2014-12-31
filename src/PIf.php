<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace academicpuma\citeproc\php;

/**
 * Description of csl_if
 *
 * @author sebastian
 */

class PIf extends RenderingElement {

    function evaluate($data) {
        $match = (($match = $this->match)) ? $match : 'all';
        if (($types = $this->type)) {
            $types = explode(' ', $types);
            $matches = 0;
            foreach ($types as $type) {
                if (isset($data->type)) {
                    if ($data->type == $type && $match == 'any')
                        return TRUE;
                    if ($data->type != $type && $match == 'all')
                        return FALSE;
                    if ($data->type == $type)
                        $matches++;
                }
            }
            if ($match == 'all' && $matches == count($types))
                return TRUE;
            if ($match == 'none' && $matches == 0)
                return TRUE;
            return FALSE;
        }
        if (($variables = $this->variable)) {
            $variables = explode(' ', $variables);
            $matches = 0;
            foreach ($variables as $var) {
                if (isset($data->$var) && !empty($data->$var) && $match == 'any')
                    return TRUE;
                if ((!isset($data->$var) || empty($data->$var)) && $match == 'all')
                    return FALSE;
                if (isset($data->$var) && !empty($data->$var))
                    $matches++;
            }
            if ($match == 'all' && $matches == count($variables))
                return TRUE;
            if ($match == 'none' && $matches == 0)
                return TRUE;
            return FALSE;
        }
        if (($is_numeric = $this->{'is-numeric'})) {
            $variables = explode(' ', $is_numeric);
            $matches = 0;
            foreach ($variables as $var) {
                if (isset($data->$var)) {
                    if (is_numeric($data->$var) && $match == 'any')
                        return TRUE;
                    if (!is_numeric($data->$var)) {
                        if (preg_match('/(?:^\d+|\d+$)/', $data->$var)) {
                            $matches++;
                        } elseif ($match == 'all') {
                            return FALSE;
                        }
                    }
                    if (is_numeric($data->$var))
                        $matches++;
                }
            }
            if ($match == 'all' && $matches == count($variables))
                return TRUE;
            if ($match == 'none' && $matches == 0)
                return TRUE;
            return FALSE;
        }
        if (isset($this->locator))
            $test = explode(' ', $this->type);

        return FALSE;
    }

}
