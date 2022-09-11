<?php
declare(strict_types=1);
/*
 * @link        http://github.com/seboettg/citeproc-php for the source repository
 * @copyright   Copyright (c) 2019 Sebastian Böttger.
 * @license     https://opensource.org/licenses/MIT
 */

namespace Seboettg\CiteProc\Rendering\Date\DateRange;

use Seboettg\CiteProc\Rendering\Date\DateTime;
use Seboettg\Collection\Lists\ListInterface;
use Seboettg\Collection\Map\MapInterface;

class MonthRenderer extends DateRangeRenderer
{
    public function parseDateRange(ListInterface $datePartsList, DateTime $from, DateTime $to, string $delimiter): string
    {
        $ret = "";

        foreach ($datePartsList as $datePartPair) {
            $key = $datePartPair->getKey();
            $datePart = $datePartPair->getValue();
            if (strpos($key, self::YEAR) !== false) {
                $ret .= $datePart->render($from, $this->parentDateObject);
            }
            if (strpos($key, self::MONTH)) {
                $ret .= $this->renderOneRangePart($datePart, $from, $to, $delimiter);
            }
            if (strpos($key, self::DAY) !== false) {
                $day = !empty($from->getDay()) ? $datePart->render($from, $this->parentDateObject) : "";
                $ret .= $day;
            }
        }
        return $ret;
    }
}
