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

    private $form = "long";

    private $single = "";

    private $multiple = "";

    private $match = "";

    private $genderForm = "";

    private $gender = "";

    public function __set($name, $value)
    {
        $nameParts = explode("-", $name);
        $attr = "";
        for ($i = count($nameParts)-1; $i >= 0; --$i) {
            if ($i > 0) {
                $attr = ucfirst($nameParts[$i]) . $attr;
            } else {
                $attr = $nameParts[$i].$attr;
            }
        }
        if (!isset($this->{$attr})) {
            throw new \InvalidArgumentException("Attribute \"$attr\" ($name) does not exist in " . __CLASS__);
        }
        $this->{$attr} = $value;
    }

    public function __get($name)
    {
        return $this->{$name};
    }

}