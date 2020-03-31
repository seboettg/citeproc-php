<?php
/*
 * citeproc-php
 *
 * @link        http://github.com/seboettg/citeproc-php for the source repository
 * @copyright   Copyright (c) 2016 Sebastian Böttger.
 * @license     https://opensource.org/licenses/MIT
 */

namespace Seboettg\CiteProc\Rendering\Choose;

use Seboettg\CiteProc\Data\DataList;
use Seboettg\CiteProc\Exception\ClassNotFoundException;
use Seboettg\CiteProc\Exception\InvalidStylesheetException;
use Seboettg\CiteProc\Rendering\HasParent;
use Seboettg\CiteProc\Rendering\Rendering;
use Seboettg\Collection\ArrayList;
use SimpleXMLElement;

/**
 * Class Choose
 *
 * @package Seboettg\CiteProc\Node
 *
 * @author Sebastian Böttger <seboettg@gmail.com>
 */
class Choose implements Rendering, HasParent
{

    /**
     * @var ArrayList
     */
    private $children;

    private $parent;

    /**
     * Choose constructor.
     *
     * @param  SimpleXMLElement $node
     * @param  $parent
     * @throws ClassNotFoundException
     * @throws InvalidStylesheetException
     */
    public function __construct(SimpleXMLElement $node, $parent)
    {
        $this->parent = $parent;
        $this->children = new ArrayList();
        $elseIf = [];
        foreach ($node->children() as $child) {
            switch ($child->getName()) {
                case 'if':
                    $this->children->add("if", new ChooseIf($child, $this));
                    break;
                case 'else-if':
                    $elseIf[] = new ChooseElseIf($child, $this);
                    break;
                case 'else':
                    $this->children->add("else", new ChooseElse($child, $this));
                    break;
            }
        }
        if (!empty($elseIf)) {
            $this->children->add("elseif", $elseIf);
        }
    }

    /**
     * @param  array|DataList $data
     * @param  null|int       $citationNumber
     * @return string
     */
    public function render($data, $citationNumber = null)
    {
        $arr = [];

        // IF
        if ($prevCondition = $this->children->get("if")->match($data)) {
            $arr[] = $this->children->get("if")->render($data);
        } elseif (!$prevCondition && $this->children->hasKey("elseif")) { // ELSEIF
            /**
             * @var ChooseElseIf $child
             */
            foreach ($this->children->get("elseif") as $child) {
                $condition = $child->match($data);
                if ($condition && !$prevCondition) {
                    $arr[] = $child->render($data);
                    $prevCondition = true;
                    break; //break loop as soon as condition matches
                }
                $prevCondition = $condition;
            }
        }

        //ELSE
        if (!$prevCondition && $this->children->hasKey("else")) {
            $arr[] = $this->children->get("else")->render($data);
        }
        return implode("", $arr);
    }

    /**
     * @return mixed
     */
    public function getParent()
    {
        return $this->parent;
    }
}
