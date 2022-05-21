<?php
declare(strict_types=1);
/*
 * citeproc-php
 *
 * @link        http://github.com/seboettg/citeproc-php for the source repository
 * @copyright   Copyright (c) 2016 Sebastian Böttger.
 * @license     https://opensource.org/licenses/MIT
 */

namespace Seboettg\CiteProc\Constraint;

use Seboettg\CiteProc\Exception\ClassNotFoundException;
use function Seboettg\CiteProc\ucfirst;

class Factory extends \Seboettg\CiteProc\Util\Factory
{
    const NAMESPACE_CONSTRAINTS = "Seboettg\\CiteProc\\Constraint\\";

    /**
     * @throws ClassNotFoundException
     */
    public static function createConstraint(string $name, string $value, string $match): Constraint
    {
        $parts = explode("-", $name);
        $className = implode("", array_map(function ($part) {
            return ucfirst($part);//overridden function
        }, $parts));
        $className = self::NAMESPACE_CONSTRAINTS . $className;

        if (!class_exists($className)) {
            throw new ClassNotFoundException($className);
        }
        return new $className($value, $match);
    }
}
