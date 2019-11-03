<?php
/*
 * citeproc-php: DateRangeYearMonthDayRenderer.php
 * User: Sebastian Böttger <sebastian.boettger@thomascook.de>
 * created at 03.11.19, 20:24
 */

namespace Seboettg\CiteProc\Rendering\Date\DateRange;

use Seboettg\CiteProc\Rendering\Date\DatePart;
use Seboettg\CiteProc\Rendering\Date\DateTime;
use Seboettg\Collection\ArrayList;

/**
 * Class YearMonthDayRenderer
 * @package Seboettg\CiteProc\Rendering\Date\DateRange
 */
class YearMonthDayRenderer extends DateRangeRenderer
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
        $i = 0;
        foreach ($dateParts as $datePart) {
            if ($i === $dateParts->count() - 1) {
                $ret .= $datePart->renderPrefix();
                $ret .= $datePart->renderWithoutAffixes($from, $this->parentDateObject);
            } else {
                $ret .= $datePart->render($from, $this->parentDateObject);
            }
            ++$i;
        }
        $ret .= $delimiter;
        $i = 0;
        /** @var DatePart $datePart */
        foreach ($dateParts as $datePart) {
            if ($i == 0) {
                $ret .= $datePart->renderWithoutAffixes($to, $this->parentDateObject);
                $ret .= $datePart->renderSuffix();
            } else {
                $ret .= $datePart->render($to, $this->parentDateObject);
            }
            ++$i;
        }
        return $ret;
    }
}
