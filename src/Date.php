<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace academicpuma\citeproc\php;

/**
 * Description of csl_date
 *
 * @author sebastian
 */


class Date extends Format {

    function init($dom_node, $citeproc) {
        $locale_elements = array();

        if ($form = $this->form) {
            $local_date = $this->citeproc->get_locale('date_options', $form);
            $dom_elem = dom_import_simplexml($local_date[0]);
            if ($dom_elem) {
                foreach ($dom_elem->childNodes as $node) {
                    if ($node->nodeType == 1) {
                        $locale_elements[] = Factory::create($node, $citeproc);
                    }
                }
            }

            //debug($dom_node->childNodes);

            foreach ($dom_node->childNodes as $node) {
                if ($node->nodeType == 1) {
                    $element = Factory::create($node, $citeproc);

                    foreach ($locale_elements as $key => $locale_element) {
                        if ($locale_element->name == $element->name) {
                            $locale_elements[$key]->attributes = array_merge($locale_element->attributes, $element->attributes);
                            $locale_elements[$key]->format = $element->format;
                            break;
                        } else {
                            $locale_elements[] = $element;
                        }
                    }
                }
            }
            if ($date_parts = $this->{'date-parts'}) {
                $parts = explode('-', $date_parts);
                foreach ($locale_elements as $key => $element) {
                    if (array_search($element->name, $parts) === FALSE) {
                        unset($locale_elements[$key]);
                    }
                }
                if (count($locale_elements) != count($parts)) {
                    foreach ($parts as $part) {
                        $element = new csl_date_part();
                        $element->name = $part;
                        $locale_elements[] = $element;
                    }
                }
                // now re-order the elements
                foreach ($parts as $part) {
                    foreach ($locale_elements as $key => $element)
                        if ($element->name == $part) {
                            $this->elements[] = $element;
                            unset($locale_elements[$key]);
                        }
                }
            }
            //Changes @ 2012-06-23 from Sebastian Böttger
            /*
            else if (isset($this->{'literal'})) {
                $element = new csl_date_part();
                $element->name = $this->{'literal'};
                $this->elements[] = $element;
            }
             * 
             */
            //END Changes      
            else {
                $this->elements = $locale_elements;
            }
        } else {
            parent::init($dom_node, $citeproc);
        }
    }

    function render($data, $mode = NULL) {
        $date_parts = array();
        $date = '';
        $text = '';

        if (($var = $this->variable) && isset($data->{$var})) {
            $date = $data->{$var}->{'date-parts'}[0];
            foreach ($this->elements as $element) {
                $date_parts[] = $element->render($date, $mode);
            }
            $text = implode('', $date_parts);
        }

        return $this->format($text);
    }

}
