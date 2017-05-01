<?php
/**
 * citeproc-php
 *
 * @link        http://github.com/seboettg/citeproc-php for the source repository
 * @copyright   Copyright (c) 2016 Sebastian Böttger.
 * @license     https://opensource.org/licenses/MIT
 */

namespace Seboettg\CiteProc\Exception;


/**
 * Class ClassNotFoundException
 * @package Seboettg\CiteProc\Exception
 *
 * @codeCoverageIgnore
 *
 * @author Sebastian Böttger <seboettg@gmail.com>
 */
class ClassNotFoundException extends CiteProcException
{

    public function __construct($class, $code = 0, \Exception $previous = null)
    {
        parent::__construct("Class \"$class\" could not be found.", $code, $previous);
    }
}