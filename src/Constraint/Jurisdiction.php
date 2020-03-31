<?php
/*
 * citeproc-php
 *
 * @link        http://github.com/seboettg/citeproc-php for the source repository
 * @copyright   Copyright (c) 2016 Sebastian Böttger.
 * @license     https://opensource.org/licenses/MIT
 */

namespace Seboettg\CiteProc\Constraint;

/**
 * Class Jurisdiction
 * @package Seboettg\CiteProc\Constraint
 *
 * @author Sebastian Böttger <seboettg@gmail.com>
 */
/** @noinspection PhpUnused */
class Jurisdiction implements Constraint
{
    /**
     * @codeCoverageIgnore
     * @param $value
     * @param int|null $citationNumber
     * @return bool
     */
    public function validate($value, $citationNumber = null)
    {
        return false;
    }
}
