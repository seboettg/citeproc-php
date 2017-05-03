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
 * Class isUncertainDate
 * Tests whether the given date variables contain approximate dates.
 *
 * @package Seboettg\CiteProc\Choose\Constraint
 *
 * @author Sebastian Böttger <seboettg@gmail.com>
 */
class IsUncertainDate implements ConstraintInterface
{
    /**
     * @var string
     */
    private $varName;


    private $match;

    public function __construct($value, $match = "all")
    {
        $this->varName = $value;
        $this->match = $match;
    }

    /**
     * @param $value
     * @param int|null $citationNumber
     * @return bool
     */
    public function validate($value, $citationNumber = null)
    {
        $value = $value->{$this->varName};

        if (is_array($value) && array_key_exists('circa', $value)) {
            return true;
        }

        return false;
    }
}