<?php
/*
 * citeproc-php
 *
 * @link        http://github.com/seboettg/citeproc-php for the source repository
 * @copyright   Copyright (c) 2016 Sebastian Böttger.
 * @license     https://opensource.org/licenses/MIT
 */

namespace Seboettg\CiteProc\Util;

use DateTime;
use Exception;
use Seboettg\CiteProc\Exception\CiteProcException;
use Seboettg\CiteProc\Style\Sort\Key;

/**
 * Class Date
 *
 * Just a helper class for date issues
 *
 * @package Seboettg\CiteProc\Util
 *
 * @author Sebastian Böttger <seboettg@gmail.com>
 */
class DateHelper
{

    /**
     * dates: Date variables called via the variable attribute are returned in the YYYYMMDD format, with zeros
     * substituted for any missing date-parts (e.g. 20001200 for December 2000). As a result, less specific dates
     * precede more specific dates in ascending sorts, e.g. “2000, May 2000, May 1st 2000”. Negative years are sorted
     * inversely, e.g. “100BC, 50BC, 50AD, 100AD”. Seasons are ignored for sorting, as the chronological order of the
     * seasons differs between the northern and southern hemispheres.
     *
     * @param array $dateParts
     * @return string
     */
    public static function serializeDate($dateParts)
    {
        $year  = isset($dateParts[0]) ? $dateParts[0] : "0000";
        $month = isset($dateParts[1]) ? $dateParts[1] : "00";
        $day = isset($dateParts[2]) ? $dateParts[2] : "00";

        return sprintf("%04d%02d%02d", $year, $month, $day);
    }

    /**
     * @param $date
     * @return array
     * @throws CiteProcException
     */
    public static function parseDateParts($date)
    {
        if (!isset($date->{'raw'})) {
            return [];
        }
        try {
            $dateTime = new DateTime($date->{'raw'});
            $arr = [[$dateTime->format("Y"), $dateTime->format("m"), $dateTime->format("d")]];
        } catch (Exception $e) {
            throw new CiteProcException("Could not parse date \"".$date->{'raw'}."\".", 0, $e);
        }

        return $arr;
    }

    /**
     * creates sort key for variables containing date and date ranges
     * @param array $dataItem
     * @param Key $key
     * @return string
     * @throws CiteProcException
     */
    public static function getSortKeyDate($dataItem, $key)
    {
        $variable = $variable = $key->getVariable();
        $part = $key->getRangePart();
        if (count($dataItem->{$variable}->{'date-parts'}) > 1) {
            //Date range
            $datePart = $dataItem->{$variable}->{'date-parts'}[$part];
            $sortKey = self::serializeDate($datePart);
            if ($key->getSort() === "descending" && $part === 0 ||
                $key->getSort() === "ascending" && $part === 1) {
                $sortKey .= "-";
            }
        } else {
            if (!isset($dataItem->{$variable}->{'date-parts'})) {
                $dateParts = self::parseDateParts($dataItem->{$variable});
            } else {
                $dateParts = $dataItem->{$variable}->{'date-parts'}[0];
            }
            $sortKey = self::serializeDate($dateParts);
        }
        return $sortKey;
    }

    /**
     * @param array $items
     * @param string $variable name of the date variable
     * @param string $match ("all"|"any") default "all"
     * @return bool
     */
    public static function hasDateRanges($items, $variable, $match = "all")
    {
        $ret = true;
        foreach ($items as $item) {
            $dateParts = $item->{$variable}->{"date-parts"};
            if ($match === "all" && count($dateParts) !== 2) {
                return false;
            } elseif ($match === "any" && count($dateParts) === 2) {
                return true;
            } else {
                $ret = ($match === "all") ? $ret&true : $ret|true;
            }
        }
        return (bool) $ret;
    }
}
