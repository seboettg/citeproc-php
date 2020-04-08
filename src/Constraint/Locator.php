<?php
/*
 * citeproc-php
 *
 * @link        http://github.com/seboettg/citeproc-php for the source repository
 * @copyright   Copyright (c) 2016 Sebastian Böttger.
 * @license     https://opensource.org/licenses/MIT
 */

namespace Seboettg\CiteProc\Constraint;

use Seboettg\CiteProc\CiteProc;

/**
 * Class Locator
 *
 * Tests whether the locator matches the given locator types (see Locators). Use “sub-verbo” to test for the
 * “sub verbo” locator type.
 *
 * @codeCoverageIgnore
 *
 * @package Seboettg\CiteProc\Constraint
 *
 * @author Sebastian Böttger <seboettg@gmail.com>
 */
/** @noinspection PhpUnused */
class Locator extends AbstractConstraint
{
    /**
     * @inheritDoc
     */
    protected function matchForVariable($variable, $data)
    {
        if (!empty($data->id)) {
            $citationItem = CiteProc::getContext()->getCitationItemById($data->id);
            return !empty($citationItem) && !empty($citationItem->label) && $citationItem->label === $variable;
        }
        return false;
    }
}
