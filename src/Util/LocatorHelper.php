<?php
/*
 * citeproc-php: LocatorHelper.php
 * User: Sebastian Böttger <seboettg@gmail.com>
 * created at 07.04.20, 22:31
 */

namespace Seboettg\CiteProc\Util;

class LocatorHelper
{
    private const LABEL_TO_VARIABLE_MAP = [
        "page" => "page",
        "chapter" => "chapter-number",
        "issue" => "folio",
        "note" => "note",
        "section" => "section",
        "volume" => "volume"
    ];

    public static function mapLocatorLabelToRenderVariable($label)
    {
        return self::LABEL_TO_VARIABLE_MAP[$label];
    }
}
