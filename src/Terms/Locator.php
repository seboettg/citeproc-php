<?php
/*
 * citeproc-php: Locator.php
 * User: Sebastian Böttger <seboettg@gmail.com>
 * created at 08.04.20, 15:21
 */

namespace Seboettg\CiteProc\Terms;

use MyCLabs\Enum\Enum;

class Locator extends Enum
{
    const BOOK = "book";
    const CHAPTER = "chapter";
    const COLUMN = "column";
    const FIGURE = "figure";
    const FOLIO = "folio";
    const ISSUE = "issue";
    const LINE = "line";
    const NOTE = "note";
    const OPUS = "opus";
    const PAGE = "page";
    const PARAGRAPH = "paragraph";
    const PART = "part";
    const SECTION = "section";
    const SUB_VERBO = "sub-verbo";
    const VERSE = "verse";
    const VOLUME = "volume";

    private const LABEL_TO_VARIABLE_MAP = [
        "chapter" => "chapter-number",
    ];

    /**
     * @param string|Locator $locatorTerm
     * @return string
     */
    public static function mapLocatorLabelToRenderVariable($locatorTerm)
    {
        if ($locatorTerm instanceof Locator) {
            $locatorTerm = (string)$locatorTerm;
        }
        return
            array_key_exists($locatorTerm, self::LABEL_TO_VARIABLE_MAP) ?
                self::LABEL_TO_VARIABLE_MAP[$locatorTerm] : $locatorTerm;
    }
}
