<?php
/*
 * citeproc-php: DateRangeMonthDayRenderer.php
 * User: Sebastian Böttger <sebastian.boettger@thomascook.de>
 * created at 03.11.19, 20:51
 */

namespace Seboettg\CiteProc\Rendering\Date\DateRange;

use Seboettg\CiteProc\Rendering\Date\DatePart;
use Seboettg\CiteProc\Rendering\Date\DateTime;
use Seboettg\Collection\ArrayList;

/**
 * Class MonthDayRenderer
 * @package Seboettg\CiteProc\Rendering\Date\DateRange
 */
class MonthDayRenderer extends DateRangeRenderer
{
    /**
     * @param ArrayList<DatePart> $dateParts
     * @param DateTime $from
     * @param DateTime $to
     * @param $delimiter
     * @return string
     */
    public function parseDateRange(ArrayList $dateParts, DateTime $from, DateTime $to, $delimiter)
    {
        $dp = $dateParts->toArray();
        $dateParts_ = [];
        array_walk($dp, function ($datePart, $key) use (&$dateParts_) {
            //$bit = sprintf("%03d", decbin($differentParts));
            if (strpos($key, "month") !== false || strpos($key, "day") !== false) {
                $dateParts_["monthday"][] = $datePart;
            }
            if (strpos($key, "year") !== false) {
                $dateParts_["year"] = $datePart;
            }
        });
        return $this->renderDateParts($dateParts_, $from, $to, $delimiter);
    }
}
