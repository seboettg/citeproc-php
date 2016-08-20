<?php

namespace Seboettg\CiteProc\Constraint;


/**
 * Class ConstraintInterface
 * @package Seboettg\CiteProc\Node\Choose\Constraint
 *
 * @author Sebastian Böttger <boettger@hebis.uni-frankfurt.de>
 */
interface ConstraintInterface
{
    public function validate($value);
}