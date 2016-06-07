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

    /**
     * @var CiteProc
     */
    private static $instance;

    /**
     * @var Bibliography
     */
    public $bibliography;

    /**
     * @var Citation
     */
    public $citation;

    /**
     * @var Style
     */
    public $style;

    /**
     * @var Macros
     */
    protected $macros;

    /**
     * @var Info
     */
    private $info;

    /**
     * @var Locale
     */
    protected $locale;
    
    /**
     * @var Mapper
     */
    private $mapper;

    /**
     * @var array
     */
    public $quash;

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

    /**
     * CiteProc constructor.
     * @param string $csl xml formatted csl stylesheet
     * @param string $lang
     */
    function __construct($csl = NULL, $lang = 'en') {
        if ($csl) {
	        $this->init($csl, $lang);
        }
    }

    private function init($csl, $lang) {
        // define field values appropriate to your data in the csl_mapper class and un-comment the next line.        
        $this->mapper = new Mapper();
        $this->quash = array();

        $cslDoc = new \DOMDocument();

        if ($cslDoc->loadXML($csl)) {

            $styleNodes = $cslDoc->getElementsByTagName('style');
            if ($styleNodes) {
                foreach ($styleNodes as $style) {
                    $this->style = new Style($style);
                }
            }

            $infoNodes = $cslDoc->getElementsByTagName('info');
            if ($infoNodes) {
                foreach ($infoNodes as $info) {
                    $this->info = new Info($info);
                }
            }

            $this->locale = new Locale($lang);
            $this->locale->setStyleLocale($cslDoc);

            $macroNodes = $cslDoc->getElementsByTagName('macro');
            if ($macroNodes) {
                $this->macros = new Macros($macroNodes, $this);
            }

            $citationNodes = $cslDoc->getElementsByTagName('citation');
            foreach ($citationNodes as $citation) {
                $this->citation = new Citation($citation, $this);
            }

            $bibliographyNodes = $cslDoc->getElementsByTagName('bibliography');
            foreach ($bibliographyNodes as $bibliography) {
                $this->bibliography = new Bibliography($bibliography, $this);
            }
        }
    }

    function render($data, $mode = null) {
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

    public function getMarcos()
    {
        return $this->macros;
    }

    public function getLocale()
    {
        return $this->locale;
    }

    public function getMapper()
    {
        return $this->mapper;
    }
}
