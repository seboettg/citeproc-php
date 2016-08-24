<?php

namespace Seboettg\CiteProc\Exception;


/**
 * Class ClassNotFoundException
 * @package Seboettg\CiteProc\Exception
 *
 * @author Sebastian Böttger <boettger@hebis.uni-frankfurt.de>
 */
class ClassNotFoundException extends CiteProcException
{

    public function __construct($class, $code = 0, \Exception $previous = null)
    {
        parent::__construct("Class \"$class\" could not be found.", $code, $previous);
    }
}