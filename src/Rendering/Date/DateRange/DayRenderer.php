<?php
/*
 * citeproc-php: DateRangeDayRenderer.php
 * User: Sebastian Böttger <sebastian.boettger@thomascook.de>
 * created at 03.11.19, 20:09
 */

namespace Seboettg\CiteProc\Rendering\Date\DateRange;

use Seboettg\CiteProc\Rendering\Date\DatePart;
use Seboettg\CiteProc\Rendering\Date\DateTime;
use Seboettg\Collection\ArrayList;

/**
 * Class DayRenderer
 * @package Seboettg\CiteProc\Rendering\Date\DateRange
 */
class DayRenderer extends DateRangeRenderer
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
            if (strpos($key, "month") !== false) {
                $ret .= $datePart->render($from, $this->parentDateObject);
            }
            if (strpos($key, "day")) {
                $ret .= $this->renderOneRangePart($datePart, $from, $to, $delimiter);
            }
        }
        return $ret;
    }
}
