<?php
/*
 * citeproc-php: DateRangeYearDayRenderer.php
 * User: Sebastian Böttger <sebastian.boettger@thomascook.de>
 * created at 03.11.19, 20:47
 */

namespace Seboettg\CiteProc\Rendering\Date\DateRange;

use Seboettg\CiteProc\Rendering\Date\DatePart;
use Seboettg\CiteProc\Rendering\Date\DateTime;
use Seboettg\Collection\ArrayList;

/**
 * Class YearDayRenderer
 * @package Seboettg\CiteProc\Rendering\Date\DateRange
 */
class YearDayRenderer extends DateRangeRenderer
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
            if (strpos($key, "year") !== false || strpos($key, "day") !== false) {
                $dateParts_["yearday"][] = $datePart;
            }
            if (strpos($key, "month") !== false) {
                $dateParts_["month"] = $datePart;
            }
        });
        return $this->renderDateParts($dateParts_, $from, $to, $delimiter);
    }
}
