<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace academicpuma\citeproc\php;

/**
 * Description of csl_mapper
 *
 * @author sebastian
 */

class Mapper {

    // In the map_field and map_type function below, the array keys hold the "CSL" variable and type names
    // and the array values contain the variable and type names of the incomming data object.  If the naming
    // convention of your incomming data object differs from the CSL standard (http://citationstyles.org/downloads/specification.html#id78)
    // you should adjust the array values accordingly.

    function map_field($field) {
        if (!isset($this->field_map)) {
            $this->field_map = array('title' => 'title',
                'container-title' => 'container-title',
                'collection-title' => 'collection-title',
                'original-title' => 'original-title',
                'publisher' => 'publisher',
                'publisher-place' => 'publisher-place',
                'original-publisher' => 'original-publisher',
                'original-publisher-place' => 'original-publisher-place',
                'archive' => 'archive',
                'archive-place' => 'archive-place',
                'authority' => 'authority',
                'archive_location' => 'authority',
                'event' => 'event',
                'event-place' => 'event-place',
                'page' => 'page',
                'page-first' => 'page',
                'locator' => 'locator',
                'version' => 'version',
                'volume' => 'volume',
                'number-of-volumes' => 'number-of-volumes',
                'number-of-pages' => 'number-of-pages',
                'issue' => 'issue',
                'chapter-number' => 'chapter-number',
                'medium' => 'medium',
                'status' => 'status',
                'edition' => 'edition',
                'section' => 'section',
                'genre' => 'genre',
                'note' => 'note',
                'annote' => 'annote',
                'abstract' => 'abstract',
                'keyword' => 'keyword',
                'number' => 'number',
                'references' => 'references',
                'URL' => 'URL',
                'DOI' => 'DOI',
                'ISBN' => 'ISBN',
                'call-number' => 'call-number',
                'citation-number' => 'citation-number',
                'citation-label' => 'citation-label',
                'first-reference-note-number' => 'first-reference-note-number',
                'year-suffix' => 'year-suffix',
                'jurisdiction' => 'jurisdiction',
                //Date Variables'
                'issued' => 'issued',
                'event' => 'event',
                'accessed' => 'accessed',
                'container' => 'container',
                'original-date' => 'original-date',
                //Name Variables'
                'author' => 'author',
                'editor' => 'editor',
                'translator' => 'translator',
                'recipient' => 'recipient',
                'interviewer' => 'interviewer',
                'publisher' => 'publisher',
                'composer' => 'composer',
                'original-publisher' => 'original-publisher',
                'original-author' => 'original-author',
                'container-author' => 'container-author',
                'collection-editor' => 'collection-editor',
            );
        }

        $vars = explode(' ', $field);
        foreach ($vars as $key => $value) {
            $vars[$key] = (!empty($this->field_map[$value])) ? $this->field_map[$value] : '';
        }

        return implode(' ', $vars);
    }

    function map_type($types) {
        if (!isset($this->type_map)) {
            $this->type_map = array(
                'article' => 'article',
                'article-magazine' => 'article-magazine',
                'article-newspaper' => 'article-newspaper',
                'article-journal' => 'article-journal',
                'bill' => 'bill',
                'book' => 'book',
                'broadcast' => 'broadcast',
                'chapter' => 'chapter',
                'entry' => 'entry',
                'entry-dictionary' => 'entry-dictionary',
                'entry-encyclopedia' => 'entry-encyclopedia',
                'figure' => 'figure',
                'graphic' => 'graphic',
                'interview' => 'interview',
                'legislation' => 'legislation',
                'legal_case' => 'legal_case',
                'manuscript' => 'manuscript',
                'map' => 'map',
                'motion_picture' => 'motion_picture',
                'musical_score' => 'musical_score',
                'pamphlet' => 'pamphlet',
                'paper-conference' => 'paper-conference',
                'patent' => 'patent',
                'post' => 'post',
                'post-weblog' => 'post-weblog',
                'personal_communication' => 'personal_communication',
                'report' => 'report',
                'review' => 'review',
                'review-book' => 'review-book',
                'song' => 'song',
                'speech' => 'speech',
                'thesis' => 'thesis',
                'treaty' => 'treaty',
                'webpage' => 'webpage',
            );
        }
        $vars = explode(' ', $types);
        foreach ($vars as $key => $value) {
            $vars[$key] = (!empty($this->type_map[$value])) ? $this->type_map[$value] : '';
        }

        return implode(' ', $vars);
    }

}