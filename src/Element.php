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
 * Description of csl_element
 *
 * @author sebastian
 */

class Element extends Collection {

    /**
     * @var array
     */
    protected $attributes = array();

    /**
     * @var CiteProc
     */
    protected $citeProc;

    /**
     * @var \DOMNode
     */
    protected $domNode;

    public function __construct($domNode = NULL, $citeProc = NULL) {
        $this->domNode = $domNode;
        $this->citeProc = &$citeProc;
        $this->setAttributes($domNode);
        $this->init($domNode, $citeProc);
    }

    /**
     * @param \DOMNode $domNode
     * @param CiteProc $citeProc
     */
    protected function init($domNode, $citeProc) {
        if (!$domNode) {
            return;
        }

        foreach ($domNode->childNodes as $node) {
            if ($node->nodeType == 1) {
                $this->addElement(Factory::create($node, $citeProc));
            }
        }
    }


    public function setAttributes($domNode) {
        $att = array();
        $element_name = $domNode->nodeName;
        if (isset($domNode->attributes->length)) {
            for ($i = 0; $i < $domNode->attributes->length; $i++) {
                $value = $domNode->attributes->item($i)->value;
                $name = str_replace(' ', '_', $domNode->attributes->item($i)->name);
                if ($name == 'type') {
                    $value = $this->citeProc->getMapper()->mapType($value);
                }

                if (($name == 'variable' || $name == 'is-numeric') && $element_name != 'label') {
                    $value = $this->citeProc->getMapper()->mapField($value);
                }
                $this->{$name} = $value;
            }
        }
    }

    public function getAttributes() {
        return $this->attributes;
    }

    public function getHierAttributes() {
        $hierAttr = array();
        $hierNames = HierAttributes::getAllAttributes();

            /* array('and', 'delimiter-precedes-last', 'et-al-min', 'et-al-use-first',
            'et-al-subsequent-min', 'et-al-subsequent-use-first', 'initialize-with',
            'name-as-sort-order', 'sort-separator', 'name-form', 'name-delimiter',
            'names-delimiter');*/
        foreach ($hierNames as $name) {
            if (isset($this->attributes[$name])) {
                $hierAttr[$name] = $this->attributes[$name];
            }
        }
        return $hierAttr;
    }

    public function setName($name)
    {
        $this->name = $name;
    }
    
    public function getName()
    {
        return str_replace(' ', '_', $this->name);
    }
    

    public function getDomNode() {
        return $this->domNode;
    }

    public function __set($name, $value) {
        $this->attributes[$name] = $value;
    }

    public function __isset($name) {
        return isset($this->attributes[$name]);
    }

    public function __unset($name) {
        unset($this->attributes[$name]);
    }

    public function __get($name = null) {

        if ($name == null) {
            return $name;
        }

        if (array_key_exists($name, $this->attributes)) {
            return $this->attributes[$name];
        }

        if (isset($this->{$name})) {
            return $this->{$name};
        }

        return null;
    }

}
