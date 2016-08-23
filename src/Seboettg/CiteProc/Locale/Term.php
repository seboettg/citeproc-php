<?php

namespace Seboettg\CiteProc\Locale;


/**
 * Class Term
 * @package Seboettg\CiteProc\Locale
 *
 * @author Sebastian Böttger <boettger@hebis.uni-frankfurt.de>
 */
class Term
{

    private $name = "";

    private $form = "";

    private $single = "";

    private $multiple = "";


    public function __set($name, $value)
    {
        if (!isset($this->{$name})) {
            throw new \InvalidArgumentException("Attribute \"$name\" does not exist in " . __CLASS__);
        }
        $this->{$name} = $value;
    }

    public function __get($name)
    {
        return $this->{$name};
    }

}