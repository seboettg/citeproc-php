<?php
declare(strict_types=1);
/*
 * citeproc-php
 *
 * @link        http://github.com/seboettg/citeproc-php for the source repository
 * @copyright   Copyright (c) 2016 Sebastian Böttger.
 * @license     https://opensource.org/licenses/MIT
 */

namespace Seboettg\CiteProc\Locale;

use Seboettg\Collection\ArrayList;
use Seboettg\Collection\Map\MapInterface;
use SimpleXMLElement;
use stdClass;
use function Seboettg\Collection\Lists\emptyList;
use function Seboettg\Collection\Map\emptyMap;

/**
 * Trait LocaleXmlParserTrait
 * @package Seboettg\CiteProc\Locale
 * @author Sebastian Böttger <seboettg@gmail.com>
 */
trait LocaleXmlParserTrait
{
    private MapInterface $options;
    private MapInterface $date;
    private MapInterface $terms;
    private MapInterface $optionsXml;
    private MapInterface $dateXml;
    private MapInterface $termsXml;

    /**
     * init parser
     */
    protected function initLocaleXmlParser()
    {
        $this->options = emptyMap();
        $this->optionsXml = emptyMap();
        $this->date = emptyMap();
        $this->dateXml = emptyMap();
        $this->terms = emptyMap();
        $this->termsXml = emptyMap();
    }

    /**
     * @param SimpleXMLElement $locale
     */
    private function parseXml(SimpleXMLElement $locale)
    {
        /** @var SimpleXMLElement $node */
        foreach ($locale as $node) {
            switch ($node->getName()) {
                case 'style-options':
                    $this->optionsXml->put('options', $node);
                    foreach ($node->attributes() as $name => $value) {
                        if ((string) $value == 'true') {
                            $this->options->put($name, [true]);
                        } else {
                            $this->options->put($name, [false]);
                        }
                    }
                    break;
                case 'terms':
                    $this->termsXml->put('terms', emptyList());
                    $this->termsXml["terms"]->add($node);
                    $plural = ['single', 'multiple'];

                    /** @var SimpleXMLElement $child */
                    foreach ($node->children() as $child) {
                        $term = new Term();

                        foreach ($child->attributes() as $key => $value) {
                            $term->{$key} = (string) $value;
                        }

                        $subChildren = $child->children();
                        $count = $subChildren->count();
                        if ($count > 0) {
                            /** @var SimpleXMLElement $subChild */
                            foreach ($subChildren as $subChild) {
                                $name = $subChild->getName();
                                $value = (string) $subChild;
                                if (in_array($subChild->getName(), $plural)) {
                                    $term->{$name} = $value;
                                }
                            }
                        } else {
                            $value = (string) $child;
                            $term->{'single'} = $value;
                            $term->{'multiple'} = $value;
                        }
                        if (!$this->terms->containsKey($term->getName())) {
                            $this->terms->put($term->getName(), emptyList());
                        }

                        $this->terms[$term->getName()]->add($term);
                    }
                    break;
                case 'date':
                    $form = (string) $node["form"];
                    $this->dateXml->put($form, $node);
                    foreach ($node->children() as $child) {
                        $date = new stdClass();
                        $name = "";
                        foreach ($child->attributes() as $key => $value) {
                            if ("name" === $key) {
                                $name = (string) $value;
                            }
                            $date->{$key} = (string) $value;
                        }
                        if ($child->getName() !== "name-part" && !$this->terms->containsKey($name)) {
                            $this->terms->put($name, []);
                        }
                        $this->date->put($form, $date);
                    }

                    break;
            }
        }
    }

    /**
     * @return SimpleXMLElement
     */
    public function getLatestOptionsXml()
    {
        $arr = $this->optionsXml->toArray();
        return array_pop($arr);
    }

    /**
     * @return array
     */
    public function getDateXml()
    {
        return $this->dateXml->toArray();
    }

    /**
     * @return SimpleXMLElement
     */
    public function getLatestDateXml()
    {
        $arr = $this->dateXml->toArray();
        return array_pop($arr['date']);
    }

    /**
     * @return SimpleXMLElement
     */
    public function getTermsXml()
    {
        $arr = $this->termsXml->toArray();
        return array_pop($arr);
    }
}
