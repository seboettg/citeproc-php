<?php
/**
 * citeproc-php
 *
 * @link        http://github.com/seboettg/citeproc-php for the source repository
 * @copyright   Copyright (c) 2016 Sebastian Böttger.
 * @license     https://opensource.org/licenses/MIT
 */

namespace Seboettg\CiteProc\Rendering\Choose;
use Seboettg\CiteProc\Rendering\RenderingInterface;


/**
 * Class ChooseElse
 * @package Seboettg\CiteProc\Node\Choose
 *
 * @author Sebastian Böttger <seboettg@gmail.com>
 */
class ChooseElse extends ChooseIf implements RenderingInterface
{
    public function render($data)
    {
        $ret = "";
        /** @var RenderingInterface $child */
        foreach ($this->children as $child) {
            $ret .= $child->render($data);
        }
        return $ret;
    }
}