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
        $elseIf = new ArrayList();
        foreach ($node->children() as $child) {
            switch ($child->getName()) {
                case 'if':
                    $this->children->add("if", new ChooseIf($child, $this));
                    break;
                case 'else-if':
                    $elseIf->append(new ChooseElseIf($child, $this));
                    break;
                case 'else':
                    $this->children->add("else", new ChooseElse($child, $this));
                    break;
            }
        }
        if ($elseIf->count() > 0) {
            $this->children->add("elseif", $elseIf);
        }
    }

    /**
     * @param array|DataList $data
     * @param null|int $citationNumber
     * @return string
     * @throws ArrayList\NotConvertibleToStringException
     */
    public function render($data, $citationNumber = null)
    {
        $result = new ArrayList();
        $matchedIfs = false;

        $ifCondition = $this->children->get("if");

        if ($ifCondition->match($data)) { //IF CONDITION
            $matchedIfs = true;
            $result->append($ifCondition->render($data));
        } elseif ($this->children->hasKey("elseif")) { // ELSEIF
            $elseIfs = $this->children->get("elseif")
                ->map(function (ChooseIf $elseIf) use ($data) {
                    return new Tuple($elseIf, $elseIf->match($data));
                })
                ->filter(function (Tuple $elseIfToMatch) {
                    return $elseIfToMatch->second === true;
                });
            $matchedIfs = $elseIfs->count() > 0;
            if ($matchedIfs) {
                $result->append(
                    $elseIfs
                        ->first() //returns a Tuple
                        ->first
                        ->render($data)
                );
            }
        }

        // !$matchedIfs ensures that each previous condition has not been met
        if (!$matchedIfs && $this->children->hasKey("else")) { //ELSE
            $result->append($this->children->get("else")->render($data));
        }
        return $result->collectToString("");
    }

    /**
     * @return mixed
     */
    public function getParent()
    {
        return $this->parent;
    }
}
