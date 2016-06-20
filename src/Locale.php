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
use \SimpleXMLElement;

/**
 *
 *
 * @author Sebastian Böttger <seboettg@gmail.com>
 */

class Locale {


    private static $langBase = array(
        "af" => "af-ZA",
        "ar" => "ar-AR",
        "bg" => "bg-BG",
        "ca" => "ca-AD",
        "cs" => "cs-CZ",
        "da" => "da-DK",
        "de" => "de-DE",
        "el" => "el-GR",
        "en" => "en-GB",
        "en" => "en-US",
        "es" => "es-ES",
        "et" => "et-EE",
        "fa" => "fa-IR",
        "fi" => "fi-FI",
        "fr" => "fr-FR",
        "he" => "he-IL",
        "hu" => "hu-HU",
        "is" => "is-IS",
        "it" => "it-IT",
        "ja" => "ja-JP",
        "km" => "km-KH",
        "ko" => "ko-KR",
        "mn" => "mn-MN",
        "nb" => "nb-NO",
        "nl" => "nl-NL",
        "nn" => "nn-NO",
        "pl" => "pl-PL",
        "pt" => "pt-PT",
        "ro" => "ro-RO",
        "ru" => "ru-RU",
        "sk" => "sk-SK",
        "sl" => "sl-SI",
        "sr" => "sr-RS",
        "sv" => "sv-SE",
        "th" => "th-TH",
        "tr" => "tr-TR",
        "uk" => "uk-UA",
        "vi" => "vi-VN",
        "zh" => "zh-CN",
    );

    /**
     * @var string
     */
    protected $localeXmlString;

    /**
     * @var string
     */
    protected $styleLocaleXmlString;

    /**
     * @var \SimpleXMLElement
     */
    protected $locale;

    /**
     * @var \SimpleXMLElement
     */
    protected $styleLocale;

    private static function getLocalesFileName($lang) {

        include_once __DIR__.'/../vendorPath.php';

        if (!($vendorPath = vendorPath())) {
            throw new \Exception('Error: vendor path not found. Use composer to initialize your project');
        }

        $localeFile = null;

        if(isset(self::$langBase[$lang])) {
            $localeFile = file_get_contents($vendorPath.'/academicpuma/locales/locales-' . self::$langBase[$lang] . '.xml');
        } else {
            $localeFile = file_get_contents($vendorPath.'/academicpuma/locales/locales-en-US.xml');
        }

        return $localeFile;
    }

    public function __construct($lang = 'en') {

        $this->module_path = dirname(__FILE__);
	    $this->locale = new \SimpleXMLElement(self::getLocalesFileName($lang));

        if ($this->locale) {
            $this->locale->registerXPathNamespace('cs', 'http://purl.org/net/xbiblio/csl');
        }
    }

    // SimpleXML objects cannot be serialized, so we must convert to an XML string prior to serialization
    public function __sleep() {
        $this->localeXmlString = ($this->locale) ? $this->locale->asXML() : '';
        $this->styleLocaleXmlString = ($this->styleLocale) ? $this->styleLocale->asXML() : '';
        return array('localeXmlString', 'styleLocaleXmlString');
    }

    // SimpleXML objects cannot be serialized, so when un-serializing them, they must rebuild from the serialized XML string.
    public function __wakeup() {
        $this->styleLocale = (!empty($this->styleLocaleXmlString)) ? new SimpleXMLElement($this->styleLocaleXmlString) : NULL;
        $this->locale = (!empty($this->localeXmlString)) ? new SimpleXMLElement($this->localeXmlString) : NULL;
        if ($this->locale) {
            $this->locale->registerXPathNamespace('cs', 'http://purl.org/net/xbiblio/csl');
        }
    }

    public function locale($type, $arg1, $arg2 = NULL, $arg3 = NULL) {

        switch ($type) {
            case 'term':

                $term = '';
                $form = $arg2 ? " and @form='$arg2'" : '';
                $plural = $arg3 ? "/cs:$arg3" : '';
                if ($this->styleLocale) {
                    $term = @$this->styleLocale->xpath("//locale[@xml:lang='en']/terms/term[@name='$arg1'$form]$plural");
                    if (!$term) {
                        $term = @$this->styleLocale->xpath("//locale/terms/term[@name='$arg1'$form]$plural");
                    }
                }
                if (!$term) {
                    $term = $this->locale->xpath("//cs:term[@name='$arg1'$form]$plural");
                }
                if (isset($term[0])) {
                    if (isset($arg3) && isset($term[0]->{$arg3})) {
                        return (string) $term[0]->{$arg3};
                    }
                    if (!isset($arg3) && isset($term[0]->single)) {
                        return (string) $term[0]->single;
                    }
                    return (string) $term[0];
                }
                break;

            case 'date_option':

                $attributes = array();
                $datePart = null;
                if ($this->styleLocale) {
                    $datePart = $this->styleLocale->xpath("//date[@form='$arg1']/date-part[@name='$arg2']");
                }
                if (!isset($datePart)) {
                    $datePart = $this->locale->xpath("//cs:date[@form='$arg1']/cs:date-part[@name='$arg2']");
                }
                if (isset($datePart)) {
                    foreach ($datePart->attributes() as $name => $value) {
                        $attributes[$name] = (string) $value;
                    }
                }
                return $attributes;

            case 'date_options':

                $options = array();

                if ($this->styleLocale) {
                    $options = $this->styleLocale->xpath("//locale[@xml:lang='en']/date[@form='$arg1']");
                    if (!$options) {
                        $options = $this->styleLocale->xpath("//locale/date[@form='$arg1']");
                    }
                }

                if (!$options) {
                    $options = $this->locale->xpath("//cs:date[@form='$arg1']");
                }

                if (isset($options[0])) {
                    return $options[0];
                }
                break;

            case 'style_option':

                $attributes = array();
                $option = array();
                if ($this->styleLocale) {
                    $option = $this->styleLocale->xpath("//locale[@xml:lang='en']/style-options[@$arg1]");
                    if (!$option) {
                        $option = $this->styleLocale->xpath("//locale/style-options[@$arg1]");
                    }
                }
                if (isset($option) && !empty($option)) {
                    $attributes = $option[0]->attributes();
                }
                if (empty($attributes)) {
                    $option = $this->locale->xpath("//cs:style-options[@$arg1]");
                }
                foreach ($option[0]->attributes() as $name => $value) {
                    if ($name == $arg1)
                        return (string) $value;
                }
                break;
        }
    }

    public function setStyleLocale(\DOMDocument $cslDoc) {

        $xml = '';

        $localeNodes = $cslDoc->getElementsByTagName('locale');

        if ($localeNodes) {

            $xmlOpen = '<style-locale>';
            $xmlClose = '</style-locale>';

            foreach ($localeNodes as $key => $locale_node) {
                $xml .= $cslDoc->saveXML($locale_node);
            }

            if (!empty($xml)) {
                $this->styleLocale = new SimpleXMLElement($xmlOpen . $xml . $xmlClose);
            }
        }
    }

}
