<?php


namespace Seboettg\CiteProc\Constraint;
use Seboettg\CiteProc\Exception\ClassNotFoundException;


/**
 * Class Factory
 * @package Seboettg\CiteProc\Node\Choose\Constraint
 *
 * @author Sebastian Böttger <boettger@hebis.uni-frankfurt.de>
 */
class Factory extends \Seboettg\CiteProc\Util\Factory
{

    const NAMESPACE_CONSTRAINTS = "Seboettg\\CiteProc\\Constraint\\";

    /**
     * @param string $name
     * @param string $value
     * @param string $match
     * @return mixed
     * @throws ClassNotFoundException
     */
    public static function createConstraint($name, $value, $match)
    {
        $className = "";
        $parts = explode("-", $name);
        array_walk($parts, function ($part) use (&$className) {
            $className .= ucfirst($part);
        });
        $className = self::NAMESPACE_CONSTRAINTS.$className;

        if (!class_exists($className)) {
            throw new ClassNotFoundException($className);
        }
        return new $className($value, $match);
    }
}