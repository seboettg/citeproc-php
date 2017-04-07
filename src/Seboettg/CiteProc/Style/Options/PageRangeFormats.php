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
 * Class PageRangeFormats
 *
 * Activates expansion or collapsing of page ranges: “chicago” (“321–28”), “expanded” (e.g. “321–328”),
 * “minimal” (“321–8”), or “minimal-two” (“321–28”) (see also Appendix V - Page Range Formats). Delimits page ranges
 * with the “page-range-delimiter” term (introduced with CSL 1.0.1, and defaults to an en-dash). If the attribute is
 * not set, page ranges are rendered without reformatting.
 *
 * @package Seboettg\CiteProc\Style
 * @author Sebastian Böttger <seboettg@gmail.com>
 */
class PageRangeFormats extends Enum
{

    /**
     * Page ranges are abbreviated according to the Chicago Manual of Style-rules:
     *
     * First number	                            Second number	                                Examples
     * =================================================================================================================
     * Less than 100	                        Use all digits	                                3–10; 71–72
     * -----------------------------------------------------------------------------------------------------------------
     * 100 or multiple of 100	                Use all digits	                                100–104; 600–613;
     *                                                                                          1100–1123
     * -----------------------------------------------------------------------------------------------------------------
     * 101 through 109 (in multiples of 100)	Use changed part only, omitting unneeded zeros	107–8; 505–17; 1002–6
     * -----------------------------------------------------------------------------------------------------------------
     * 110 through 199 (in multiples of 100)	Use two digits, or more as needed	            321–25; 415–532;
     *                                                                                          11564–68; 13792–803
     * -----------------------------------------------------------------------------------------------------------------
     * 4 digits	                                If numbers are four digits long and three
     *                                          digits change, use all digits
     */
    const CHICAGO = "chicago";

    /**
     * Abbreviated page ranges are expanded to their non-abbreviated form: 42–45, 321–328, 2787–2816
     */
    const EXPANDED = "expanded";

    /**
     * All digits repeated in the second number are left out: 42–5, 321–8, 2787–816
     */
    const MINIMAL = "minimal";

    /**
     * As “minimal”, but at least two digits are kept in the second number when it has two or more digits long.
     */
    const MINIMAL_TWO = "minimal-two";
}