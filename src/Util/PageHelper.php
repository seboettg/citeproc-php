<?php
/*
 * citeproc-php
 *
 * @link        http://github.com/seboettg/citeproc-php for the source repository
 * @copyright   Copyright (c) 2017 Sebastian Böttger.
 * @license     https://opensource.org/licenses/MIT
 */

namespace Seboettg\CiteProc\Util;

use Seboettg\CiteProc\Style\Options\PageRangeFormats;

/**
 * Class PageHelper
 * @package Seboettg\CiteProc\Util
 * @author Sebastian Böttger <seboettg@gmail.com>
 */
class PageHelper
{
    /**
     * @param array $ranges
     * @param string $pageRangeFormat
     * @return string
     */
    public static function processPageRangeFormats($ranges, $pageRangeFormat)
    {
        list($from, $to) = $ranges;

        // We check to see if the page values are numeric since it is now 
        // common to encounter electronic identifiers 'E123-45' which will
        // throw TypeErrors if we try and process them below.
        if (!empty($pageRangeFormat) && is_numeric($from) && is_numeric($to)) {
            switch ($pageRangeFormat) {
                case PageRangeFormats::MINIMAL:
                    $resTo = self::renderMinimal($from, $to, 0);
                    break;
                case PageRangeFormats::MINIMAL_TWO:
                    if (strlen($to) > 2) {
                        $resTo = self::renderMinimal($from, $to, strlen($to) - 2);
                    } else {
                        $resTo = $to;
                    }
                    break;
                case PageRangeFormats::CHICAGO:
                    $resTo = self::renderChicago($from, $to);
                    break;
                case PageRangeFormats::EXPANDED:
                default:
                    $resTo = $to;
            }
            return "$from-$resTo";
        }
        return "$from-$to";
    }

    /**
     *
     * @param $from
     * @param $to
     * @param int $limit
     * @return string
     */
    private static function renderMinimal($from, $to, $limit = 1)
    {
        $resTo = "";
        if (strlen($from) == strlen($to)) {
            for ($i = strlen($to) - 1; $i >= $limit; --$i) {
                $digitTo = $to[$i];

                $digitFrom = $from[$i];
                if ($digitTo !== $digitFrom) {
                    $resTo = $digitTo.$resTo;
                }
            }
            return $resTo;
        }
        return $to;
    }

    private static function renderChicago($from, $to)
    {
        if ($from > 100 && ($from % 100 > 0) && intval(($from / 100), 10) === intval(($to / 100), 10)) {
            return "".($to % 100);
        } elseif ($from >= 10000) {
            return "".($to % 1000);
        }
        return $to;
    }
}
