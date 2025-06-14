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

/**
 * Interface ConstraintInterface
 * @package Seboettg\CiteProc\Constraint
 *
 * @author Sebastian Böttger <seboettg@gmail.com>
 */
/** @noinspection PhpUnused */
interface Constraint
{
    const MATCH_NONE = "none";

    const MATCH_ANY = "any";

    const MATCH_ALL = "all";

    /**
     * @param stdClass $data
     * @param int|null $citationNumber
     * @return bool
     */
    public function validate(stdClass $data, ?int $citationNumber = null): bool;
}
