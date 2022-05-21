<?php
declare(strict_types=1);
/*
 * citeproc-php: AbstractConstraint.php
 * User: Sebastian Böttger <sebastian.boettger@thomascook.de>
 * created at 26.10.19, 14:12
 */

namespace Seboettg\CiteProc\Constraint;

use stdClass;

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
    abstract protected function matchForVariable(string $variable, stdClass $data): bool;

    /**
     * Variable constructor.
     * @param string $variableValues
     * @param string $match
     */
    public function __construct(string $variableValues, string $match = "any")
    {
        $this->conditionVariables = explode(" ", $variableValues);
        $this->match = $match;
    }

    /**
     * @param stdClass $data
     * @param int|null $citationNumber
     * @return bool
     */
    public function validate(stdClass $data, int $citationNumber = null): bool
    {
        switch ($this->match) {
            case Constraint::MATCH_ALL:
                return $this->matchAll($data);
            case Constraint::MATCH_NONE:
                return !$this->matchAny($data); //no match for any value
            case Constraint::MATCH_ANY:
            default:
                return $this->matchAny($data);
        }
    }

    private function matchAny($data): bool
    {
        $conditionMatched = false;
        foreach ($this->conditionVariables as $variable) {
            $conditionMatched |= $this->matchForVariable($variable, $data);
        }
        return $conditionMatched;
    }

    private function matchAll($value): bool
    {
        $conditionMatched = true;
        foreach ($this->conditionVariables as $variable) {
            $conditionMatched &= $this->matchForVariable($variable, $value);
        }
        return $conditionMatched;
    }
}
