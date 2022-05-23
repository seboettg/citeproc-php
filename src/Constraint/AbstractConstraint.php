<?php
declare(strict_types=1);
/*
 * @link        http://github.com/seboettg/citeproc-php for the source repository
 * @copyright   Copyright (c) 2019 Sebastian Böttger.
 * @license     https://opensource.org/licenses/MIT
 */

namespace Seboettg\CiteProc\Constraint;

use Seboettg\Collection\ArrayList;
use stdClass;

abstract class AbstractConstraint implements Constraint
{

    /**
     * @var string
     */
    protected $match;

    /**
     * @var ArrayList\ArrayListInterface
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
        $this->conditionVariables = new ArrayList(...explode(" ", $variableValues));
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
                return $this->matchNone($data); //no match for any value
            case Constraint::MATCH_ANY:
            default:
                return $this->matchAny($data);
        }
    }

    private function matchAny(stdClass $data): bool
    {
        return $this->conditionVariables
            ->map(function (string $conditionVariable) use ($data) {
                return $this->matchForVariable($conditionVariable, $data);
            })
            ->filter(function (bool $match) {
                return $match === true;
            })
            ->count() > 0;
    }

    private function matchAll(stdClass $data): bool
    {
        return $this->conditionVariables
            ->map(function (string $conditionVariable) use ($data) {
                return $this->matchForVariable($conditionVariable, $data);
            })
            ->filter(function (bool $match) {
                return $match === true;
            })
            ->count() === $this->conditionVariables->count();
    }

    private function matchNone(stdClass $data): bool
    {
        return $this->conditionVariables
            ->map(function (string $conditionVariable) use ($data) {
                return $this->matchForVariable($conditionVariable, $data);
            })
            ->filter(function (bool $match) {
                return $match === false;
            })
            ->count() === $this->conditionVariables->count();
    }
}
