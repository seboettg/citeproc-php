<?php

namespace Seboettg\CiteProc\Constraint;


/**
 * Class IsNumeric
 * @package Seboettg\CiteProc\Node\Choose\Constraint
 *
 * @author Sebastian Böttger <boettger@hebis.uni-frankfurt.de>
 */
class IsNumeric implements ConstraintInterface
{

    private $isNumeric;

    private $match;

    public function __construct($value, $match)
    {
        $this->isNumeric = $value;
        $this->match = $match;
    }

    public function validate($value)
    {
        if (isset($value->{$this->isNumeric})) {
            return is_numeric($value->{$this->isNumeric});
        }

        return false;
    }
}