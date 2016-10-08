<?php

namespace Seboettg\CiteProc\Constraint;


/**
 * Class Variable
 * @package Seboettg\CiteProc\Node\Choose\Constraint
 *
 * @author Sebastian Böttger <boettger@hebis.uni-frankfurt.de>
 */
class Variable implements ConstraintInterface
{

    public function validate($data)
    {
        return false;
    }
}