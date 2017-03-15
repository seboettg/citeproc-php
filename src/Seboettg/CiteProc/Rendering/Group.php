<?php
/*
 * citeproc-php
 *
 * @link        http://github.com/seboettg/citeproc-php for the source repository
 * @copyright   Copyright (c) 2016 Sebastian Böttger.
 * @license     https://opensource.org/licenses/MIT
 */

namespace Seboettg\CiteProc\Rendering;
use Seboettg\CiteProc\Styles\AffixesTrait;
use Seboettg\CiteProc\Styles\ConsecutivePunctuationCharacterTrait;
use Seboettg\CiteProc\Styles\DelimiterTrait;
use Seboettg\CiteProc\Styles\DisplayTrait;
use Seboettg\CiteProc\Util\Factory;
use Seboettg\Collection\ArrayList;


/**
 * Class Group
 * @package Seboettg\CiteProc\Rendering
 *
 * @author Sebastian Böttger <seboettg@gmail.com>
 */
class Group implements RenderingInterface, HasParent
{
    use DelimiterTrait,
        AffixesTrait,
        DisplayTrait,
        ConsecutivePunctuationCharacterTrait;

    private $children;

    /**
     * cs:group may carry the delimiter attribute to separate its child elements
     * @var
     */
    private $delimiter = "";

    private $parent;

    public function __construct(\SimpleXMLElement $node, $parent)
    {
        $this->parent = $parent;
        $this->children = new ArrayList();
        foreach ($node->children() as $child) {
            $this->children->append(Factory::create($child, $this));
        }
        $this->initDisplayAttributes($node);
        $this->initAffixesAttributes($node);
        $this->initDelimiterAttributes($node);
    }

    /**
     * @param $data
     * @param int|null $citationNumber
     * @return string
     */
    public function render($data, $citationNumber = null)
    {
        $arr = [];
        /** @var RenderingInterface $child */
        foreach ($this->children as $child) {
            $res = $child->render($data, $citationNumber);
            $this->getChildsAffixesAndDelimiter($child);
            if (!empty($res)) {
                $arr[] = $res;
            }
        }
        if (!empty($arr)) {
            $res = $this->wrapDisplayBlock($this->addAffixes(implode($this->delimiter, $arr)));
            $res = $this->removeConsecutiveChars($res);
            return $res;
        }
        return "";
    }

    /**
     * @return mixed
     */
    public function getParent()
    {
        return $this->parent;
    }


}