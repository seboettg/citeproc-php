<?php
/*
 * citeproc-php
 *
 * @link        http://github.com/seboettg/citeproc-php for the source repository
 * @copyright   Copyright (c) 2016 Sebastian Böttger.
 * @license     https://opensource.org/licenses/MIT
 */

namespace Seboettg\CiteProc\Rendering;

use Seboettg\CiteProc\Data\DataList;
use stdClass;

/**
 * Interface RenderingInterface
 *
 * Defines "render" function.
 *
 * @package Seboettg\CiteProc\Rendering
 */
interface Rendering
{
    public function render(DataList $data, $citationNumber);
}
