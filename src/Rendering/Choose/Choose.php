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
use Seboettg\Collection\Map\MapInterface;
use SimpleXMLElement;
use function Seboettg\Collection\Lists\emptyList;
use function Seboettg\Collection\Map\emptyMap;

/**
 * Class Choose
 *
 * @package Seboettg\CiteProc\Node
 *
 * @author Sebastian Böttger <seboettg@gmail.com>
 */
class Choose implements Rendering, HasParent
{

    private const IF = "if";
    private const ELSE_IF = "elseif";
    private const ELSE = "else";

    private MapInterface $children;

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
        $this->children = emptyMap();
        $elseIf = emptyList();
        foreach ($node->children() as $child) {
            switch ($child->getName()) {
                case 'if':
                    $this->children->put(self::IF, new ChooseIf($child, $this));
                    break;
                case 'else-if':
                    $elseIf->add(new ChooseElseIf($child, $this));
                    break;
                case 'else':
                    $this->children->put(self::ELSE, new ChooseElse($child, $this));
                    break;
            }
        }
        if ($elseIf->count() > 0) {
            $this->children->put(self::ELSE_IF, $elseIf);
        }
    }

    /**
     * @param array|DataList $data
     * @param null|int $citationNumber
     * @return string
     */
    public function render($data, $citationNumber = null): string
    {
        $result = emptyList();
        $matchedIfs = false;

        $ifCondition = $this->children->get(self::IF);

        if ($ifCondition->match($data)) { //IF CONDITION
            $matchedIfs = true;
            $result->add($ifCondition->render($data));
        } elseif ($this->children->containsKey(self::ELSE_IF)) { // ELSEIF
            $elseIfs = $this->children
                ->get(self::ELSE_IF)
                ->map(fn (ChooseIf $elseIf) => new Tuple($elseIf, $elseIf->match($data)))
                ->filter(fn (Tuple $elseIfToMatch) => $elseIfToMatch->second === true);
            $matchedIfs = $elseIfs->count() > 0;
            if ($matchedIfs) {
                $result->add(
                    $elseIfs
                        ->first() //returns a Tuple
                        ->first
                        ->render($data)
                );
            }
        }

        // !$matchedIfs ensures that each previous condition has not been met
        if (!$matchedIfs && $this->children->containsKey(self::ELSE)) { //ELSE
            $result->add($this->children->get(self::ELSE)->render($data));
        }
        return $result->joinToString("");
    }

    /**
     * @return mixed
     */
    public function getParent()
    {
        return $this->parent;
    }
}
