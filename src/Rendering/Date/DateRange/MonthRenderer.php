<?php
/*
 * citeproc-php: DateRangeMonthRenderer.php
 * User: Sebastian Böttger <sebastian.boettger@thomascook.de>
 * created at 03.11.19, 20:09
 */

namespace Seboettg\CiteProc\Rendering\Date\DateRange;

use Seboettg\CiteProc\Rendering\Date\DatePart;
use Seboettg\CiteProc\Rendering\Date\DateTime;
use Seboettg\Collection\ArrayList;

/**
 * Class DateRangeMonthRenderer
 * @package Seboettg\CiteProc\Rendering\Date
 */
class MonthRenderer extends DateRangeRenderer
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
        $ret = "";

        foreach ($dateParts as $key => $datePart) {
            if (strpos($key, "year") !== false) {
                $ret .= $datePart->render($from, $this->parentDateObject);
            }
            if (strpos($key, "month")) {
                $ret .= $this->renderOneRangePart($datePart, $from, $to, $delimiter);
            }
            if (strpos($key, "day") !== false) {
                $day = !empty($d = $from->getDay()) ? $datePart->render($from, $this->parentDateObject) : "";
                $ret .= $day;
            }
        }
        return $ret;
    }
}
