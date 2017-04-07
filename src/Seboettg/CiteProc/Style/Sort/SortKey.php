<?php
/*
 * citeproc-php
 *
 * @link        http://github.com/seboettg/citeproc-php for the source repository
 * @copyright   Copyright (c) 2016 Sebastian Böttger.
 * @license     https://opensource.org/licenses/MIT
 */

namespace Seboettg\CiteProc\Style\Sort;


interface SortKey
{
    public function getVariable();

    public function getSort();

    public function isNameVariable();

    public function isNumberVariable();

    public function isDateVariable();

    public function isMacro();
}