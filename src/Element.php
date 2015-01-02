<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace academicpuma\citeproc;

/**
 * Description of csl_element
 *
 * @author sebastian
 */

class Element extends Collection {

    protected $attributes = array();
    protected $citeproc;
    protected $dom_node;

    function __construct($dom_node = NULL, $citeproc = NULL) {
        $this->dom_node = $dom_node;
        $this->citeproc = &$citeproc;
        $this->set_attributes($dom_node);
        $this->init($dom_node, $citeproc);
    }

    function init($dom_node, $citeproc) {
        if (!$dom_node)
            return;

        foreach ($dom_node->childNodes as $node) {
            if ($node->nodeType == 1) {
                $this->addElement(Factory::create($node, $citeproc));
            }
        }
    }

    function __set($name, $value) {
        $this->attributes[$name] = $value;
    }

    function __isset($name) {
        return isset($this->attributes[$name]);
    }

    function __unset($name) {
        unset($this->attributes[$name]);
    }

    function &__get($name = NULL) {
        $null = NULL;
        if (array_key_exists($name, $this->attributes)) {
            return $this->attributes[$name];
        }
        if (isset($this->{$name})) {
            return $this->{$name};
        }
        return $null;
    }

    function set_attributes($dom_node) {
        $att = array();
        $element_name = $dom_node->nodeName;
        if (isset($dom_node->attributes->length)) {
            for ($i = 0; $i < $dom_node->attributes->length; $i++) {
                $value = $dom_node->attributes->item($i)->value;
                $name = str_replace(' ', '_', $dom_node->attributes->item($i)->name);
                if ($name == 'type') {
                    $value = $this->citeproc->map_type($value);
                }

                if (($name == 'variable' || $name == 'is-numeric') && $element_name != 'label') {
                    $value = $this->citeproc->map_field($value);
                }
                $this->{$name} = $value;
            }
        }
    }

    function get_attributes() {
        return $this->attributes;
    }

    function get_hier_attributes() {
        $hier_attr = array();
        $hier_names = array('and', 'delimiter-precedes-last', 'et-al-min', 'et-al-use-first',
            'et-al-subsequent-min', 'et-al-subsequent-use-first', 'initialize-with',
            'name-as-sort-order', 'sort-separator', 'name-form', 'name-delimiter',
            'names-delimiter');
        foreach ($hier_names as $name) {
            if (isset($this->attributes[$name])) {
                $hier_attr[$name] = $this->attributes[$name];
            }
        }
        return $hier_attr;
    }

    function name($name = NULL) {
        if ($name) {
            $this->name = $name;
        } else {
            return str_replace(' ', '_', $this->name);
        }
    }

    function getDomNode() {
        return $this->dom_node;
    }

}
