<?php
/**
 * citeproc-php
 *
 * @link        http://github.com/seboettg/citeproc-php for the source repository
 * @copyright   Copyright (c) 2016 Sebastian Böttger.
 * @license     https://opensource.org/licenses/MIT
 */

namespace Seboettg\CiteProc\Constraint;


/**
 * Class Type
 * @package Seboettg\CiteProc\Choose\Constraint
 *
 * @author Sebastian Böttger <seboettg@gmail.com>
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