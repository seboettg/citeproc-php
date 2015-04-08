<?php

/*
 * Copyright (C) 2015 
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace AcademicPuma\CiteProc;

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
