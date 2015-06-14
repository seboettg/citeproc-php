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
use \Exception;

class Factory {

    public static function create($dom_node, $citeproc = NULL) {
        //$class_name = 'csl_' . str_replace('-', '_', $dom_node->nodeName);
        $className = ucfirst($dom_node->nodeName);
        
        while(true) {
            //find '-'
            $pos = strpos($className, '-');
            if($pos === false) {
                break;
            }
            //replace '-' and transform to camel case
            $className = substr($className, 0, $pos) . ucfirst(substr($className, $pos+1));
        }
        
        //
        switch($className) {
            case 'If':
            case 'Else':
            case 'ElseIf':
                $className = 'P'.$className;
        }
        
        $className = 'AcademicPuma\\CiteProc\\'.$className;
        
        if(class_exists($className)) {
            return new $className($dom_node, $citeproc);
        }
        return null;
        
    }

}