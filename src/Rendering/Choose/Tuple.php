<?php

namespace Seboettg\CiteProc\Rendering\Choose;

class Tuple
{
    public $first;
    public $second;

    public function __construct($first, $second)
    {
        $this->first = $first;
        $this->second = $second;
    }
}
