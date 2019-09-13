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
 * Class Disambiguate
 * When set to “true” (the only allowed value), the element content is only rendered if it disambiguates two otherwise
 * identical citations. This attempt at disambiguation is only made when all other disambiguation methods have failed
 * to uniquely identify the target source.
 *
 * @codeCoverageIgnore
 *
 * @package Seboettg\CiteProc\Choose\Constraint
 *
 * @author Sebastian Böttger <seboettg@gmail.com>
 */
class Disambiguate implements ConstraintInterface
{
    /**
     * @param $value
     * @param int|null $citationNumber
     * @return bool
     */
    public function validate($value, $citationNumber = null)
    {
        return false;
    }
}