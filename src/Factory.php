<?php

namespace academicpuma\citeproc\php;
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
        
        $className = 'academicpuma\\citeproc\\php\\'.$className;
        
        if(class_exists($className)) {
            return new $className($dom_node, $citeproc);
        }
        return null;
        
    }

}