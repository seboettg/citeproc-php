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
 * Class Variable
 * @package Seboettg\CiteProc\Choose\Constraint
 *
 * @author Sebastian Böttger <seboettg@gmail.com>
 */
class Variable implements ConstraintInterface
{
    /**
     * @var string
     */
    private $name;

    /**
     * Variable constructor.
     * @param $name
     */
    public function __construct($name)
    {
        $this->name = $name;
    }

    /**
     * @param $value
     * @param int|null $citationNumber
     * @return bool
     */
    public function validate($value, $citationNumber = null)
    {
        return !empty($value->{$this->name});
    }
}
