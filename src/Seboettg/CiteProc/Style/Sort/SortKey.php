<?php

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