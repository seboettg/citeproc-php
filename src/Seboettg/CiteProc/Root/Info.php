<?php
/**
 * citeproc-php
 *
 * @link        http://github.com/seboettg/citeproc-php for the source repository
 * @copyright   Copyright (c) 2017 Sebastian Böttger.
 * @license     https://opensource.org/licenses/MIT
 */

/**
 * Created by PhpStorm.
 * User: sebastian
 * Date: 05.05.17
 * Time: 08:28
 */

namespace Seboettg\CiteProc\Root;


class Info
{
    /**
     * @var string
     */
    private $title;

    /**
     * @var string
     */
    private $id;

    /**
     * @var array
     */
    private $authors;

    /**
     * @var array
     */
    private $links;

    public function __construct(\SimpleXMLElement $node)
    {
        $this->authors = [];
        $this->links = [];

        /** @var \SimpleXMLElement $child */
        foreach ($node->children() as $child) {
            switch ($child->getName()) {
                case 'author':
                case 'contributor':
                    $author = new \stdClass();
                    /** @var \SimpleXMLElement $authorNode */
                    foreach ($child->children() as $authorNode) {
                        $author->{$authorNode->getName()} = (string) $authorNode;
                    }
                    $this->authors[] = $author;
                    break;
                case 'link':
                    foreach ($child->attributes() as $attribute) {
                        if ($attribute->getName() === "value") {
                            $this->links[] = (string) $attribute;
                        }
                    }
                    break;
                default:
                    $this->{$child->getName()} = (string) $child;
            }
        }
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return array
     */
    public function getAuthors()
    {
        return $this->authors;
    }

    /**
     * @return array
     */
    public function getLinks()
    {
        return $this->links;
    }
}