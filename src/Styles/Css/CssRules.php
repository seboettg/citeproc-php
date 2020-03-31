<?php
/*
 * citeproc-php
 *
 * @link        http://github.com/seboettg/citeproc-php for the source repository
 * @copyright   Copyright (c) 2017 Sebastian Böttger.
 * @license     https://opensource.org/licenses/MIT
 */

namespace Seboettg\CiteProc\Styles\Css;

use Seboettg\Collection\ArrayList;

/**
 * Class CssRules
 * @package Seboettg\CiteProc\Styles\Css
 * @author Sebastian Böttger <seboettg@gmail.com>
 */
class CssRules extends ArrayList
{
    /**
     * @param $rule
     * @return CssRule
     */
    public function getRule($rule)
    {
        if (!$this->hasKey($rule)) {
            $this->set($rule, new CssRule(substr($rule, 1), substr($rule, 0, 1)));
        }
        return $this->get($rule);
    }
}
