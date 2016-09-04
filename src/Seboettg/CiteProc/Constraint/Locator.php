<?php

namespace Seboettg\CiteProc\Constraint;


/**
 * Class Locator
 *
 * Tests whether the locator matches the given locator types (see Locators). Use “sub-verbo” to test for the
 * “sub verbo” locator type.
 *
 * @package Seboettg\CiteProc\Node\Choose\Constraint
 *
 * @author Sebastian Böttger <boettger@hebis.uni-frankfurt.de>
 */
class Locator implements ConstraintInterface
{

    public function validate($value)
    {
        return false;
    }
}