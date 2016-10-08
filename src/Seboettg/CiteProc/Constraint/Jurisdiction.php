<?php

namespace Seboettg\CiteProc\Constraint;


/**
 * Class Jurisdiction
 * @package Seboettg\CiteProc\Constraint
 *
 * @author Sebastian Böttger <boettger@hebis.uni-frankfurt.de>
 */
class Jurisdiction implements ConstraintInterface
{

    public function validate($value)
    {
        return false;
    }
}