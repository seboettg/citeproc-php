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
 * Class isUncertainDate
 * Tests whether the given date variables contain approximate dates.
 *
 * @package Seboettg\CiteProc\Constraint
 *
 * @author Sebastian Böttger <seboettg@gmail.com>
 */
/** @noinspection PhpUnused */
class IsUncertainDate extends AbstractConstraint
{

    /**
     * @param string $variable
     * @param stdClass $value ;
     * @return bool
     */
    protected function matchesForVariable($variable, $value)
    {
        if (!empty($value->{$variable})) {
            if (isset($value->{$variable}->{'circa'})) {
                return true;
            }
        }
        return false;
    }
}
