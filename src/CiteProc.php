<?php

/**
 *   CiteProc-PHP
 *
 *   Copyright (C) 2010 - 2011  Ron Jerome, all rights reserved
 *
 *   This program is free software: you can redistribute it and/or modify
 *   it under the terms of the GNU General Public License as published by
 *   the Free Software Foundation, either version 3 of the License, or
 *   (at your option) any later version.
 *
 *   This program is distributed in the hope that it will be useful,
 *   but WITHOUT ANY WARRANTY; without even the implied warranty of
 *   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *   GNU General Public License for more details.
 *
 *   You should have received a copy of the GNU General Public License
 *   along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 */

namespace AcademicPuma\CiteProc;
use \DOMDocument;

class CiteProc {

    private static $instance;
    public $bibliography = null;
    public $citation = null;
    public $style = null;
    protected $macros = null;
    private $info = null;
    protected $locale = null;
    protected $style_locale = null;
    private $mapper = null;
    public $quash = null;
    /**
     * 
     * @return citeproc 
     */
    public static function getInstance($xml) {

        if (self::$instance == null) {

            self::$instance = new CiteProc($xml);
        }

        return self::$instance;
    }

    function __construct($csl = NULL, $lang = 'en') {
        if ($csl) {
	        $this->init($csl, $lang);
        }
    }

    function init($csl, $lang) {
        // define field values appropriate to your data in the csl_mapper class and un-comment the next line.        
        $this->mapper = new Mapper();
        $this->quash = array();

        $csl_doc = new DOMDocument();

        if ($csl_doc->loadXML($csl)) {

            $style_nodes = $csl_doc->getElementsByTagName('style');
            if ($style_nodes) {
                foreach ($style_nodes as $style) {
                    $this->style = new Style($style);
                }
            }

            $info_nodes = $csl_doc->getElementsByTagName('info');
            if ($info_nodes) {
                foreach ($info_nodes as $info) {
                    $this->info = new Info($info);
                }
            }

            $this->locale = new Locale($lang);
            $this->locale->set_style_locale($csl_doc);


            $macro_nodes = $csl_doc->getElementsByTagName('macro');
            if ($macro_nodes) {
                $this->macros = new Macros($macro_nodes, $this);
            }

            $citation_nodes = $csl_doc->getElementsByTagName('citation');
            foreach ($citation_nodes as $citation) {
                $this->citation = new Citation($citation, $this);
            }

            $bibliography_nodes = $csl_doc->getElementsByTagName('bibliography');
            foreach ($bibliography_nodes as $bibliography) {
                $this->bibliography = new Bibliography($bibliography, $this);
            }
        }
    }

    function render($data, $mode = NULL) {
        $text = '';
        switch ($mode) {
            case 'citation':
                $text .= (isset($this->citation)) ? $this->citation->render($data) : '';
                break;
            case 'bibliography':
            default:
                $text .= (isset($this->bibliography)) ? $this->bibliography->render($data) : '';
                break;
        }
        return $text;
    }

    function render_macro($name, $data, $mode) {
        return $this->macros->render_macro($name, $data, $mode);
    }

    function get_locale($type, $arg1, $arg2 = NULL, $arg3 = NULL) {
        return $this->locale->get_locale($type, $arg1, $arg2, $arg3);
    }

    function map_field($field) {
        if ($this->mapper) {
            return $this->mapper->map_field($field);
        }
        return ($field);
    }

    function map_type($field) {
        if ($this->mapper) {
            return $this->mapper->map_type($field);
        }
        return ($field);
    }

	/**
	 * @param $name
	 *
	 * @return string
	 * @throws \Exception
	 */
    public static function loadStyleSheet($name) {
	    include_once __DIR__.'/../vendorPath.php';

	    if (!($vendorPath = vendorPath())) {
		    throw new \Exception('Error: vendor path not found. Use composer to initialize your project');
	    }

        return file_get_contents($vendorPath.'/academicpuma/styles/'.$name.'.csl');
    }
}
