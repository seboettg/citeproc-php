<?php

namespace Seboettg\CiteProc\Constraint;


/**
 * Class Type
 * @package Seboettg\CiteProc\Node\Choose\Constraint
 *
 * @author Sebastian Böttger <boettger@hebis.uni-frankfurt.de>
 */
class Type implements ConstraintInterface
{

    private $typeValue;

    public function __construct($value, $match)
    {
        $this->typeValue = $value;
    }

    /**
     * @param \stdClass $value
     * @return bool
     */
    public function validate($value)
    {
        if (isset($value->type)) {
            return ($value->type == $this->typeValue);
        }
        return false;
    }
}