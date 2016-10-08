<?php
namespace Seboettg\CiteProc\Locale;
use Seboettg\CiteProc\Util\Factory;
use Seboettg\Collection\ArrayList;

/**
 */
class Locale
{
    use LocaleXmlParserTrait;

    const VENDOR = "vendor/academicpuma/locale";

    /**
     * @var string
     */
    private $localeXml;

    /**
     * @var string
     */
    private $language;

    public function __construct($lang = "en-US", $xmlString = null)
    {
        $this->language = $lang;

        if (!empty($xmlString)) {
            $this->localeXml = new \SimpleXMLElement($xmlString);
        } else {
            $this->localeXml = Factory::loadLocale($lang);
        }

        $this->initLocaleXmlParser();
        $this->parseXml($this->localeXml);
    }

    public function addXml(\SimpleXMLElement $xml)
    {
        $this->parseXml($xml);
        return $this;
    }

    public function getLanguage()
    {
        return $this->language;
    }

    public function filter($type, $name, $form = "long") {

        if (!isset($this->{$type})) {
            throw new \InvalidArgumentException("There is no locale of type \"$type\".");
        }

        $localeList = $this->{$type};

        if (is_null($name)) {
            $name = "";
        }

        //filter by name
        $array = $localeList->get($name);

        if (empty($array)) {
            $ret = new \stdClass();
            $ret->name = null;
            $ret->single = null;
            $ret->multiple = null;
            return $ret;
        }

        //filter by form

        if ($type !== "options") {
            /** @var Term $value */
            $array = array_filter($array, function($term) use($form) {
                return $term->form === $form;
            });
        }

        return array_pop($array);
    }
}
