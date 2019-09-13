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
     * @param stdClass $value;
     * @return bool
     */
    protected abstract function matchesForVariable($variable, $value);

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
            case Constraint::MATCHES_ALL:
                return $this->matchesAll($value);
            case Constraint::MATCHES_NONE:
                return !$this->matchesAny($value); //not matches any
            case Constraint::MATCHES_ANY:
            default:
                return $this->matchesAny($value);
        }
    }

    private function matchesAny($value)
    {
        $conditionMatched = false;
        foreach ($this->conditionVariables as $variable) {
            $conditionMatched |= $this->matchesForVariable($variable, $value);
        }
        return (bool)$conditionMatched;
    }

    private function matchesAll($value)
    {
        $conditionMatched = true;
        foreach ($this->conditionVariables as $variable) {
            $conditionMatched &= $this->matchesForVariable($variable, $value);
        }
        return (bool)$conditionMatched;
    }
}
