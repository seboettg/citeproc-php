<?php
/**
 * citeproc-php
 *
 * @link        http://github.com/seboettg/citeproc-php for the source repository
 * @copyright   Copyright (c) 2016 Sebastian Böttger.
 * @license     https://opensource.org/licenses/MIT
 */

namespace Seboettg\CiteProc\Locale;


use Seboettg\Collection\ArrayList;

trait LocaleXmlParserTrait
{

    /**
     * @var ArrayList
     */
    private $options;

    /**
     * @var ArrayList
     */
    private $date;

    /**
     * @var ArrayList
     */
    private $terms;

    /**
     * @var ArrayList
     */
    private $optionsXml;

    /**
     * @var ArrayList
     */
    private $dateXml;

    /**
     * @var ArrayList
     */
    private $termsXml;

    protected function initLocaleXmlParser()
    {
        $this->options = new ArrayList();
        $this->optionsXml = new ArrayList();
        $this->date = new ArrayList();
        $this->dateXml = new ArrayList();
        $this->terms = new ArrayList();
        $this->termsXml = new ArrayList();

    }

    private function parseXml(\SimpleXMLElement $locale)
    {
        /** @var \SimpleXMLElement $node */
        foreach ($locale as $node) {
            switch ($node->getName()) {
                case 'style-options':
                    $this->optionsXml->add('options', $node);
                    foreach ($node->attributes() as $name => $value) {

                        if ((string) $value == 'true') {
                            $this->options->add($name, [true]);
                        } else {
                            $this->options->add($name, [false]);
                        }
                    }
                    break;
                case 'terms':
                    $this->termsXml->add('terms', $node);
                    $plural = ['single', 'multiple'];

                    /** @var \SimpleXMLElement $child */
                    foreach ($node->children() as $child) {
                        $term = new Term();

                        foreach ($child->attributes() as $key => $value) {
                            $term->{$key} = (string) $value;
                        }

                        $subChildren = $child->children();
                        $count = $subChildren->count();
                        if ($count > 0) {
                            /** @var \SimpleXMLElement $subChild */
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
                        if (!$this->terms->hasKey($term->name)) {
                            $this->terms->add($term->name, []);
                        }

                        $this->terms->add($term->name, $term);
                    }
                    break;
                case 'date':
                    $form = (string) $node["form"];
                    $this->dateXml->add($form, $node);
                    foreach ($node->children() as $child) {
                        $date = new \stdClass();
                        $name = "";
                        foreach ($child->attributes() as $key => $value) {
                            if ("name" === $key) {
                                $name = (string) $value;
                            }
                            $date->{$key} = (string) $value;
                        }
                        if ($child->getName() !== "name-part" && !$this->terms->hasKey($name)) {
                            $this->terms->add($name, []);
                        }
                        $this->date->add($form, $date);
                    }

                    break;
            }
        }
    }

    /**
     * @return \SimpleXMLElement
     */
    public function getLatestOptionsXml()
    {
        $arr = $this->optionsXml->toArray();
        return array_pop($arr);
    }


    public function getDateXml()
    {
        return $this->dateXml->toArray();
    }

    /**
     * @return \SimpleXMLElement
     */
    public function getLatestDateXml()
    {
        $arr = $this->dateXml->toArray();
        return array_pop($arr['date']);
    }

    /**
     * @return \SimpleXMLElement
     */
    public function getTermsXml()
    {
        $arr = $this->termsXml->toArray();
        return array_pop($arr);
    }
}