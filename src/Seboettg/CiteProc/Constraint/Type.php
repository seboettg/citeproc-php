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

    /**
     * @var array
     */
    private $typeValue;

    /**
     * Type constructor.
     * @param string $value
     * @param $match
     */
    public function __construct($value, $match)
    {
        $this->typeValue = explode(" ", $value);
    }

    /**
     * @param \stdClass $value
     * @return bool
     */
    public function validate($value)
    {
        if (isset($value->type)) {
            return in_array($value->type, $this->typeValue);
        }
        return false;
    }
}