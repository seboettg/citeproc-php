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
 * In the mapField and mapType function below, the array keys hold the "CSL" variable and type names
 * and the array values contain the variable and type names of the incomming data object.  If the naming
 * convention of your incomming data object differs from the CSL standard
 * (http://citationstyles.org/downloads/specification.html#id78) you should adjust the array values
 * accordingly.
 *
 * @author sebastian
 */

class Mapper {

    private static $typeMap = array(
        'article' => 'article',
        'article-magazine' => 'article-magazine',
        'article-newspaper' => 'article-newspaper',
        'article-journal' => 'article-journal',
        'bill' => 'bill',
        'book' => 'book',
        'broadcast' => 'broadcast',
        'chapter' => 'chapter',
        'dataset' => 'dataset',
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

    private static $fieldMap = array(
        'abstract' => 'abstract',
        'annote' => 'annote',
        'archive' => 'archive',
        'archive_location' => 'archive_location',
        'archive-place' => 'archive-place',
        'authority' => 'authority',
        'call-number' => 'call-number',
        'citation-label' => 'citation-label',
        'citation-number' => 'citation-number',
        'collection-title' => 'collection-title',
        'container-title' => 'container-title',
        'container-title-short' => 'container-title-short',
        'dimensions' => 'dimensions',
        'DOI' => 'DOI',
        'event' => 'event',
        'event-place' => 'event-place',
        'first-reference-note-number' => 'first-reference-note-number',
        'genre' => 'genre',
        'ISBN' => 'ISBN',
        'ISSN' => 'ISSN',
        'jurisdiction' => 'jurisdiction',
        'keyword' => 'keyword',
        'locator' => 'locator',
        'medium' => 'medium',
        'note' => 'note',
        'original-publisher' => 'original-publisher',
        'original-publisher-place' => 'original-publisher-place',
        'original-title' => 'original-title',
        'page' => 'page',
        'page-first' => 'page',
        'PMCID' => 'PMCID',
        'PMID' => 'PMID',
        'publisher' => 'publisher',
        'publisher-place' => 'publisher-place',
        'references' => 'references',
        'reviewed-title' => 'reviewed-title',
        'scale' => 'scale',
        'section' => 'section',
        'source' => 'source',
        'status' => 'status',
        'title' => 'title',
        'title-short' => 'title-short',
        'URL' => 'URL',
        'version' => 'version',
        'year-suffix' => 'year-suffix',
        // Number Variables
        'chapter-number' => 'chapter-number',
        'collection-number' => 'collection-number',
        'edition' => 'edition',
        'issue' => 'issue',
        'number' => 'number',
        'number-of-pages' => 'number-of-pages',
        'number-of-volumes' => 'number-of-volumes',
        'volume' => 'volume',
        //Date Variables'
        'accessed' => 'accessed',
        'container' => 'container',
        'event-date' => 'event-date',
        'issued' => 'issued',
        'original-date' => 'original-date',
        'submitted' => 'submitted',
        //Name Variables'
        'author' => 'author',
        'collection-editor' => 'collection-editor',
        'composer' => 'composer',
        'container-author' => 'container-author',
        'director' => 'director',
        'editor' => 'editor',
        'editorial-director' => 'editorial-director',
        'illustrator' => 'illustrator',
        'interviewer' => 'interviewer',
        'original-author' => 'original-author',
        'recipient' => 'recipient',
        'reviewed-author' => 'reviewed-author',
        'translator' => 'translator',
    );

    public function mapField($fields) {
        $vars = explode(' ', $fields);
        foreach ($vars as $key => $value) {
            $vars[$key] = array_key_exists($value, self::$fieldMap) ? self::$fieldMap[$value] : '';
        }
        return implode(' ', $vars);
    }

    public function mapType($types) {

        $vars = explode(' ', $types);
        foreach ($vars as $key => $value) {
            $vars[$key] = array_key_exists($value, self::$typeMap) ? self::$typeMap[$value] : '';
        }
        return implode(' ', $vars);
    }

}