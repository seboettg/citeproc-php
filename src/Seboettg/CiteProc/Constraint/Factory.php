<?php
/*
 * citeproc-php
 *
 * @link        http://github.com/seboettg/citeproc-php for the source repository
 * @copyright   Copyright (c) 2016 Sebastian Böttger.
 * @license     https://opensource.org/licenses/MIT
 */

namespace Seboettg\CiteProc\Constraint;
use Seboettg\CiteProc\Exception\ClassNotFoundException;


/**
 * Class Factory
 * @package Seboettg\CiteProc\Constraint
 *
 * @author Sebastian Böttger <seboettg@gmail.com>
 */
class Factory extends \Seboettg\CiteProc\Util\Factory
{

    const NAMESPACE_CONSTRAINTS = "Seboettg\\CiteProc\\Constraint\\";

    /**
     * @param string $name
     * @param string $value
     * @return mixed
     * @throws ClassNotFoundException
     */
    public static function createConstraint($name, $value)
    {
        $className = "";
        $parts = explode("-", $name);
        array_walk($parts, function($part) use (&$className) {
            $className .= ucfirst($part);
        });
        $className = self::NAMESPACE_CONSTRAINTS . $className;

        if (!class_exists($className)) {
            throw new ClassNotFoundException($className);
        }
        return new $className($value);
    }
}