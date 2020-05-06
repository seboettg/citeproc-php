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
use stdClass;

/**
 * Class Variable
 * @package Seboettg\CiteProc\Choose\Constraint
 *
 * @author Sebastian Böttger <seboettg@gmail.com>
 */
/** @noinspection PhpUnused */
class Variable extends AbstractConstraint
{
    /**
     * @param string $variable
     * @param stdClass $data
     * @return bool
     */
    protected function matchForVariable($variable, $data)
    {
        $variableExistInCitationItem = false;
        if (CiteProc::getContext()->isModeCitation() && isset($data->id)) {
            $citationItem = CiteProc::getContext()->getCitationItemById($data->id);
            if (!empty($citationItem)) {
                $variableExistInCitationItem = !empty($citationItem->{$variable});
            }
        }
        return !empty($data->{$variable}) || $variableExistInCitationItem;
    }
}
