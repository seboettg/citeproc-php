<?php

namespace Seboettg\CiteProc\Constraint;


/**
 * Class isUncertainDate
 * Tests whether the given date variables contain approximate dates.
 *
 * @package Seboettg\CiteProc\Node\Choose\Constraint
 *
 * @author Sebastian Böttger <boettger@hebis.uni-frankfurt.de>
 */
class isUncertainDate implements ConstraintInterface
{

    private $varName;

    private $match;

    public function __construct($value, $match)
    {
        $this->varName = $value;
        $this->match = $match;
    }

    public function validate($value)
    {
        $value = $value->{$this->varName};

        if (is_array($value) && array_key_exists('circa', $value)) {
            return true;
        }

        return false;
    }
}