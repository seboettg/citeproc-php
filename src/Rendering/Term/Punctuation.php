<?php

namespace Seboettg\CiteProc\Rendering\Term;

use MyCLabs\Enum\Enum;
use Seboettg\CiteProc\CiteProc;
use Seboettg\Collection\ArrayList;

class Punctuation extends Enum
{
    public const OPEN_QUOTE = "open-quote";
    public const CLOSE_QUOTE = "close-quote";
    public const OPEN_INNER_QUOTE = "open-inner-quote";
    public const CLOSE_INNER_QUOTE = "close-inner-quote";
    public const PAGE_RANGE_DELIMITER = "page-range-delimiter";
    public const COLON = "colon";
    public const COMMA = "comma";
    public const SEMICOLON = "semicolon";

    public static function getAllPunctuations(): array
    {
        $values = new ArrayList();
        return $values
            ->setArray(Punctuation::toArray())
            ->map(function (string $punctuation) {
                return CiteProc::getContext()->getLocale()->filter("terms", $punctuation)->single;
            })
            ->collect(function ($items) {
                return array_values($items);
            });
    }
}
