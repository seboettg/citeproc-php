<?php

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


    protected function initLocaleXmlParser()
    {
        $this->options = new ArrayList();
        $this->date = new ArrayList();
        $this->terms = new ArrayList();
    }

    private function parseXml(\SimpleXMLElement $locale)
    {
        foreach ($locale as $node) {
            switch($node->getName()) {
                case 'style-options':
                    foreach ($node->attributes() as $name => $value) {

                        if ((string) $value == 'true') {
                            $this->options->add($name, [true]);
                        } else {
                            $this->options->add($name, [false]);
                        }
                    }
                    break;
                case 'terms':
                    foreach ($node->children() as $child) {
                        $term = new Term();

                        foreach ($child->attributes() as $key => $value) {
                            $term->__set($key, (string) $value);
                        }

                        foreach ($child->children() as $subChildren) {
                            $term->__set($subChildren->getName(), (string) $subChildren);
                        }

                        if (!$this->terms->hasKey($term->name)) {
                            $this->terms->add($term->name, []);
                        }

                        $this->terms->add($term->name, $term);
                    }
                    break;
                case 'date':
                    foreach ($node->children() as $child) {
                        $date = new \stdClass();
                        $name = "";
                        foreach ($child->attributes() as $key => $value) {
                            if ("name" === $key) {
                                $name = (string) $value;
                            }
                            $date->{$key} = (string) $value;
                        }
                        if (!$this->terms->hasKey($name)) {
                            $this->terms->add($name, []);
                        }
                        $this->date->add($name, $date);
                    }

                    break;
            }
        }
    }
}