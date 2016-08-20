<?php

namespace Seboettg\CiteProc\Constraint;


/**
 * Class Disambiguate
 * @package Seboettg\CiteProc\Node\Choose\Constraint
 *
 * @author Sebastian Böttger <boettger@hebis.uni-frankfurt.de>
 */
class Disambiguate implements ConstraintInterface
{

    public function validate()
    {
        return false;
    }
}