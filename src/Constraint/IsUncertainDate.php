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
     * @param stdClass $data ;
     * @return bool
     */
    protected function matchForVariable($variable, $data)
    {
        if (!empty($data->{$variable})) {
            if (isset($data->{$variable}->{'circa'})) {
                return true;
            }
        }
        return false;
    }
}
