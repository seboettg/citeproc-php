<?php
/*
 * citeproc-php
 *
 * @link        http://github.com/seboettg/citeproc-php for the source repository
 * @copyright   Copyright (c) 2017 Sebastian Böttger.
 * @license     https://opensource.org/licenses/MIT
 */

namespace Seboettg\CiteProc\Style\Options;

use MyCLabs\Enum\Enum;

/**
 * Class SubsequentAuthorSubstituteRule
 *
 * Specifies when and how names are substituted as a result of subsequent-author-substitute.
 *
 * @package Seboettg\CiteProc\Style
 *
 * @author Sebastian Böttger <seboettg@gmail.com>
 */
class SubsequentAuthorSubstituteRule extends Enum
{

    /**
     * “complete-all” - (default), when all rendered names of the name variable match those in the preceding
     * bibliographic entry, the value of subsequent-author-substitute replaces the entire name list (including
     * punctuation and terms like “et al” and “and”), except for the affixes set on the cs:names element.
     */
    const COMPLETE_ALL = "complete-all";

    /**
     * “complete-each” - requires a complete match like “complete-all”, but now the value of
     * subsequent-author-substitute substitutes for each rendered name.
     */
    const COMPLETE_EACH = "complete-each";

    /**
     * “partial-each” - when one or more rendered names in the name variable match those in the preceding bibliographic
     * entry, the value of subsequent-author-substitute substitutes for each matching name. Matching starts with the
     * first name, and continues up to the first mismatch.
     */
    const PARTIAL_EACH = "partial-each";

    /**
     * “partial-first” - as “partial-each”, but substitution is limited to the first name of the name variable.
     */
    const PARTIAL_FIRST = "partial-first";
}
