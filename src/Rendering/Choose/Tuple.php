<?php
declare(strict_types=1);
/*
 * citeproc-php
 *
 * @link        http://github.com/seboettg/citeproc-php for the source repository
 * @copyright   Copyright (c) 2022 Sebastian Böttger.
 * @license     https://opensource.org/licenses/MIT
 */

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
