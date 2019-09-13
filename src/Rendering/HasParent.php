<?php
/*
 * citeproc-php
 *
 * @link        http://github.com/seboettg/citeproc-php for the source repository
 * @copyright   Copyright (c) 2017 Sebastian Böttger.
 * @license     https://opensource.org/licenses/MIT
 */

namespace Seboettg\CiteProc\Rendering;

/**
 * Interface HasParent
 * @package Seboettg\CiteProc\Rendering
 * @author Sebastian Böttger <seboettg@gmail.com>
 */
interface HasParent
{
    public function getParent();
}