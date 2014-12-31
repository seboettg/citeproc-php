<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace academicpuma\citeproc\php;

/**
 * Description of csl_group
 *
 * @author sebastian
 */
class Group extends Format {

    function render($data, $mode = NULL) {
        $text = '';
        $text_parts = array();
        $terms = $variables = $have_variables = $element_count = 0;
        foreach ($this->elements as $element) {
            $element_count++;
            if (($element instanceof csl_text) &&
                    ($element->source == 'term' ||
                    $element->source == 'value' )) {
                $terms++;
            }
            if (($element instanceof csl_label))
                $terms++;
            if ($element->source == 'variable' &&
                    isset($element->variable) &&
                    !empty($data->{$element->variable})
            ) {
                $variables++;
            }
            $text = $element->render($data, $mode);
            $delimiter = $this->delimiter;
            if (!empty($text)) {
                if ($delimiter && ($element_count < count($this->elements))) {
                    //check to see if the delimiter is already the last character of the text string
                    //if so, remove it so we don't have two of them when we paste together the group
                    $stext = strip_tags(trim($text));
                    if ((strrpos($stext, $delimiter[0]) + 1) == strlen($stext) && strlen($stext) > 1) {
                        $text = str_replace($stext, '----REPLACE----', $text);
                        $stext = substr($stext, 0, -1);
                        $text = str_replace('----REPLACE----', $stext, $text);
                    }
                }
                $text_parts[] = $text;
                if ($element->source == 'variable' || isset($element->variable))
                    $have_variables++;
                if ($element->source == 'macro')
                    $have_variables++;
            }
        }
        if (empty($text_parts))
            return;
        if ($variables && !$have_variables)
            return; // there has to be at least one other none empty value before the term is output
        if (count($text_parts) == $terms)
            return; // there has to be at least one other none empty value before the term is output
        $delimiter = $this->delimiter;
        $text = implode($delimiter, $text_parts); // insert the delimiter if supplied.
        return $this->format($text);
    }

    /**
      function render($data, $mode = NULL) {
      $text = '';
      $text_parts = array();

      $terms = $variables = $have_variables = 0;
      $i = 0;
      foreach ($this->elements as $element) {
      if (($element instanceof Text) &&
      ($element->source == 'term' ||
      $element->source == 'value' )) {
      $terms++;
      }
      if (($element instanceof Label))
      $terms++;
      if ($element->source == 'variable' &&
      isset($element->variable) &&
      !empty($data->{$element->variable})
      ) {
      $variables++;
      }

      $text = $element->render($data, $mode);



      if (!empty($text)) {
      if (isset($element->source)) {
      $text_parts[$element->getVar()] = $text;
      } else {
      $text_parts[$i] = $text;
      }

      if ($element->source == 'variable' || isset($element->variable))
      $have_variables++;
      if ($element->source == 'macro')
      $have_variables++;
      }
      $i++;
      }

      if (empty($text_parts))
      return;
      if ($variables && !$have_variables)
      return; // there has to be at least one other none empty value before the term is output
      if (count($text_parts) == $terms)
      return; // there has to be at least one other none empty value before the term is output

      $delimiter = $this->delimiter;

      //Changes @ 2012-06-23 from Sebastian Böttger
      //$text = implode($delimiter, $text_parts); // insert the delimiter if supplied.
      //own implode function for surrounding group with a span tag with class
      //attribute as identifier
      $text = $this->implodeGroup($delimiter, $text_parts);
      //End changes

      return $this->format($text);
      }
     * */
    /**
     * Function added by Sebastian Böttger <boettger@cs.uni-kassel.de>
     * 
     * Implodes array $text_parts and uses the $delimiter and surrounds every
     * part with a span tag.
     * 
     * @param string $delimiter
     * @param array $text_parts
     * @return string 
     *
      function implodeGroup($delimiter, $text_parts) {
      $text = '';
      $i = 0;
      foreach ($text_parts as $key => $val) {
      //if (!is_numeric($key)) {
      $text .= '<span class="' . $key . '">' . $val . '</span>';
      //} else {
      //    $text .= $val;
      //}
      if ($i < count($text_parts) - 1) {
      $text .= $delimiter;
      }
      }
      return $text;
      }
     */
}
