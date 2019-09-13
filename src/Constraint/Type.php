<?php
/*
 * citeproc-php
 *
 * @link        http://github.com/seboettg/citeproc-php for the source repository
 * @copyright   Copyright (c) 2016 Sebastian Böttger.
 * @license     https://opensource.org/licenses/MIT
 */

namespace Seboettg\CiteProc\Constraint;


use stdClass;

/**
 * Class Type
 * @package Seboettg\CiteProc\Choose\Constraint
 *
 * @author Sebastian Böttger <seboettg@gmail.com>
 */
/** @noinspection PhpUnused */
class Type extends AbstractConstraint
{

    /**
     * @param string $variable
     * @param stdClass $value ;
     * @return bool
     */
    protected function matchesForVariable($variable, $value)
    {
        return in_array($value->type, $this->conditionVariables);
    }
}
