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

use stdClass;

class Type extends AbstractConstraint
{

    /**
     * @param string $variable
     * @param stdClass $data ;
     * @return bool
     */
    protected function matchForVariable(string $variable, stdClass $data): bool
    {
        return in_array($data->type, $this->conditionVariables->toArray());
    }
}
