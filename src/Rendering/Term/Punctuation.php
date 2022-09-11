<?php

namespace Seboettg\CiteProc\Rendering\Term;

use MyCLabs\Enum\Enum;
use Seboettg\CiteProc\CiteProc;
use Seboettg\Collection\ArrayList;
use function Seboettg\Collection\Lists\listOf;

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
        return listOf(...Punctuation::toArray())
            ->map(fn (string $punctuation) =>
                CiteProc::getContext()->getLocale()->filter("terms", $punctuation)->single)
            ->collect(fn ($items) => array_values($items));
    }
}
