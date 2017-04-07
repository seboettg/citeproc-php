<?php
/*
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
     * @var string
     */
    private $match;

    /**
     * Type constructor.
     * @param string $value
     * @param $match
     */
    public function __construct($value, $match = "any")
    {
        $this->typeValue = explode(" ", $value);
        $this->match = $match;
    }

    /**
     * @param $value
     * @param int|null $citationNumber
     * @return bool
     */
    public function validate($value, $citationNumber = null)
    {
        return in_array($value->type, $this->typeValue);
    }
}