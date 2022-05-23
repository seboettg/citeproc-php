<?php
declare(strict_types=1);
/*
 * citeproc-php
 *
 * @link        http://github.com/seboettg/citeproc-php for the source repository
 * @copyright   Copyright (c) 2016 Sebastian Böttger.
 * @license     https://opensource.org/licenses/MIT
 */

namespace Seboettg\CiteProc\Constraint;

use Seboettg\CiteProc\CiteProc;
use stdClass;

class Variable extends AbstractConstraint
{
    /**
     * @param string $variable
     * @param stdClass $data
     * @return bool
     */
    protected function matchForVariable(string $variable, stdClass $data): bool
    {
        $variableExistsInCitationItem = false;
        if (CiteProc::getContext()->isModeCitation() && isset($data->id)) {
            $citationItem = CiteProc::getContext()->getCitationItemById($data->id);
            if (!empty($citationItem)) {
                $variableExistsInCitationItem = !empty($citationItem->{$variable});
            }
        }
        return !empty($data->{$variable}) || $variableExistsInCitationItem;
    }
}
