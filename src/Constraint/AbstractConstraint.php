<?php /** @noinspection PhpUnused */
/*
 * citeproc-php: AbstractConstraint.php
 * User: Sebastian Böttger <sebastian.boettger@thomascook.de>
 * created at 26.10.19, 14:12
 */

namespace Seboettg\CiteProc\Constraint;

use stdClass;

/**
 * Class AbstractConstraint
 * @package Seboettg\CiteProc\Constraint
 * @noinspection PhpUnused
 */
abstract class AbstractConstraint implements Constraint
{

    /**
     * @var string
     */
    protected $match;

    /**
     * @var array
     */
    protected $conditionVariables;

    /**
     * @param string $variable
     * @param stdClass $data;
     * @return bool
     */
    abstract protected function matchForVariable($variable, $data);

    /**
     * Variable constructor.
     * @param string $value
     * @param string $match
     */
    /** @noinspection PhpUnused */
    public function __construct($value, $match = "any")
    {
        $this->conditionVariables = explode(" ", $value);
        $this->match = $match;
    }

    /**
     * @param $value
     * @param int|null $citationNumber
     * @return bool
     */
    public function validate($value, $citationNumber = null)
    {
        switch ($this->match) {
            case Constraint::MATCH_ALL:
                return $this->matchAll($value);
            case Constraint::MATCH_NONE:
                return !$this->matchAny($value); //no match for any value
            case Constraint::MATCH_ANY:
            default:
                return $this->matchAny($value);
        }
    }

    private function matchAny($value)
    {
        $conditionMatched = false;
        foreach ($this->conditionVariables as $variable) {
            $conditionMatched |= $this->matchForVariable($variable, $value);
        }
        return (bool)$conditionMatched;
    }

    private function matchAll($value)
    {
        $conditionMatched = true;
        foreach ($this->conditionVariables as $variable) {
            $conditionMatched &= $this->matchForVariable($variable, $value);
        }
        return (bool)$conditionMatched;
    }
}
