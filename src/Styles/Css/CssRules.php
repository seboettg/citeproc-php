<?php
declare(strict_types=1);
/*
 * citeproc-php
 *
 * @link        http://github.com/seboettg/citeproc-php for the source repository
 * @copyright   Copyright (c) 2017 Sebastian Böttger.
 * @license     https://opensource.org/licenses/MIT
 */

namespace Seboettg\CiteProc\Styles\Css;

use Seboettg\Collection\Map;

class CssRules extends Map
{
    public function getRule(string $rule): CssRule
    {
        if (!$this->containsKey($rule)) {
            $this->put($rule, new CssRule(substr($rule, 1), substr($rule, 0, 1)));
        }
        return $this->get($rule);
    }
}
