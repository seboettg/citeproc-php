<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace academicpuma\citeproc\php;

/**
 * Description of csl_info
 *
 * @author sebastian
 */

class Info {

    public $title;
    public $id;
    public $authors = array();
    public $links = array();

    function __construct($dom_node) {
        $name = array();
        foreach ($dom_node->childNodes as $node) {
            if ($node->nodeType == 1) {
                switch ($node->nodeName) {
                    case 'author':
                    case 'contributor':
                        foreach ($node->childNodes as $authnode) {
                            if ($node->nodeType == 1) {
                                $name[$authnode->nodeName] = $authnode->nodeValue;
                            }
                        }
                        $this->authors[] = $name;
                        break;
                    case 'link':
                        foreach ($node->attributes as $attribute) {
                            $this->links[] = $attribute->value;
                        }
                        break;
                    default:
                        $this->{$node->nodeName} = $node->nodeValue;
                }
            }
        }
    }

}