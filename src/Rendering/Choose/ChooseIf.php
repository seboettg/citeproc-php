<?php
declare(strict_types=1);
/*
 * citeproc-php
 *
 * @link        http://github.com/seboettg/citeproc-php for the source repository
 * @copyright   Copyright (c) 2016 Sebastian Böttger.
 * @license     https://opensource.org/licenses/MIT
 */

namespace Seboettg\CiteProc\Rendering\Choose;

use Seboettg\CiteProc\Constraint\Constraint;
use Seboettg\CiteProc\Constraint\Factory;
use Seboettg\CiteProc\Data\DataList;
use Seboettg\CiteProc\Exception\ClassNotFoundException;
use Seboettg\CiteProc\Exception\InvalidStylesheetException;
use Seboettg\CiteProc\Rendering\Group;
use Seboettg\CiteProc\Rendering\HasParent;
use Seboettg\CiteProc\Rendering\Rendering;
use Seboettg\Collection\ArrayList;
use SimpleXMLElement;
use function Seboettg\Collection\Lists\emptyList;

class ChooseIf implements Rendering, HasParent
{
    /**
     * @var ArrayList<Constraint>|Constraint[]
     */
    private $constraints;

    /**
     * @var ArrayList
     */
    protected $children;

    /**
     * @var string
     */
    private $match;

    /**
     * @var
     */
    protected $parent;
    /**
     * @param SimpleXMLElement $node
     * @param Choose $parent
     * @throws InvalidStylesheetException
     * @throws ClassNotFoundException
     */
    public function __construct(SimpleXMLElement $node, Choose $parent)
    {
        $this->parent = $parent;
        $this->constraints = emptyList();
        $this->children = emptyList();
        $this->match = (string) $node["match"];
        if (empty($this->match)) {
            $this->match = Constraint::MATCH_ALL;
        }
        foreach ($node->attributes() as $name => $value) {
            if ("match" !== $name) {
                $this->constraints->add(Factory::createConstraint((string) $name, (string) $value, $this->match));
            }
        }
        foreach ($node->children() as $child) {
            $this->children->add(Factory::create($child, $this));
        }
    }
    /**
     * @param array|DataList $data
     * @param null|int $citationNumber
     * @return string
     */
    public function render($data, $citationNumber = null): string
    {
        $ret = [];
        /** @var Rendering $child */
        foreach ($this->children as $child) {
            $ret[] = $child->render($data, $citationNumber);
        }
        $glue = "";
        $parent = $this->parent->getParent();
        if ($parent instanceof Group && $parent->hasDelimiter()) {
            $glue = $parent->getDelimiter();
        }
        return implode($glue, array_filter($ret));
    }
    /**
     * @param $data
     * @param null|int $citationNumber
     * @return bool
     */
    public function match($data, int $citationNumber = null): bool
    {
        if ($this->constraints->count() === 1) {
            return $this->constraints->current()->validate($data);
        }

        switch ($this->match) {
            case Constraint::MATCH_ANY:
                return $this->constraints
                    ->map(fn (Constraint $constraint) => $constraint->validate($data))
                    ->filter(fn (bool $match) => $match == true)
                    ->count() > 0;
            case Constraint::MATCH_ALL:
                return $this->constraints
                    ->map(fn (Constraint $constraint) => $constraint->validate($data))
                    ->filter(fn (bool $match) => $match == true)
                    ->count() == $this->constraints->count();
            case Constraint::MATCH_NONE:
                return !$this->constraints
                    ->map(fn (Constraint $constraint) => $constraint->validate($data))
                    ->filter(fn (bool $match) => $match == false)
                    ->count() == $this->constraints->count();
        }
        return false;
    }


    /**
     * @noinspection PhpUnused
     * @return Choose
     */
    public function getParent(): Choose
    {
        return $this->parent;
    }
}
