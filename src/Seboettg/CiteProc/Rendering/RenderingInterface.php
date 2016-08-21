<?php

namespace Seboettg\CiteProc\Rendering;


interface RenderingInterface
{

    /**
     * @param $data
     * @return string
     */
    public function render($data);
}