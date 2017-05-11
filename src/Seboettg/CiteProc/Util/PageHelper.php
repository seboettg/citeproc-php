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

    public static function processPageRangeFormats($ranges, $pageRangeFormat)
    {
        list($from, $to) = $ranges;

        if (!empty($pageRangeFormat)) {

            switch ($pageRangeFormat) {
                case PageRangeFormats::MINIMAL:
                    $resTo = self::renderMinimal($from, $to);
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
        for ($i = strlen($to) - 1; $i >= $limit; --$i) {
            $digitTo = $to{$i};
            $digitFrom = $from{$i};
            if ($digitTo !== $digitFrom) {
                $resTo = $digitTo . $resTo;
            }
        }
        return $resTo;
    }

    private static function renderChicago($from, $to)
    {

        if ($from > 100 && ($from % 100 > 0) && intval(($from / 100), 10) === intval(($to / 100), 10)) {
            return "" . ($to % 100);
        } else if ($from >= 10000) {
            return "" . ($to % 1000);
        }
        return $to;
    }
}