<?php

namespace Seboettg\CiteProc\Constraint;


/**
 * Class Disambiguate
 * When set to “true” (the only allowed value), the element content is only rendered if it disambiguates two otherwise
 * identical citations. This attempt at disambiguation is only made when all other disambiguation methods have failed
 * to uniquely identify the target source.
 *
 * @package Seboettg\CiteProc\Node\Choose\Constraint
 *
 * @author Sebastian Böttger <boettger@hebis.uni-frankfurt.de>
 */
class Disambiguate implements ConstraintInterface
{

    public function validate($value)
    {
        return false;
    }
}