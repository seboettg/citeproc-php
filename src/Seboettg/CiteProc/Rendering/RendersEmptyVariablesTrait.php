<?php
/*
 * citeproc-php
 *
 * @link        http://github.com/seboettg/citeproc-php for the source repository
 * @copyright   Copyright (c) 2017 Sebastian Böttger.
 * @license     https://opensource.org/licenses/MIT
 */

namespace Seboettg\CiteProc\Rendering;


trait RendersEmptyVariablesTrait
{

    public function rendersEmptyVariables($data)
    {
        $allEmpty = 1;
        foreach ($this->children as $child) {
            if ($child instanceof RendersEmptyVariables) {
                $allEmpty &= $child->rendersEmptyVariables($data);
            }
        }
        return boolval($allEmpty);
    }
}