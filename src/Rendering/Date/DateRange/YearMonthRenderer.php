<?php
declare(strict_types=1);
/*
 * @link        http://github.com/seboettg/citeproc-php for the source repository
 * @copyright   Copyright (c) 2019 Sebastian Böttger.
 * @license     https://opensource.org/licenses/MIT
 */

namespace Seboettg\CiteProc\Rendering\Date\DateRange;

use Seboettg\CiteProc\Rendering\Date\DatePart;
use Seboettg\CiteProc\Rendering\Date\DateTime;
use Seboettg\Collection\Lists\ListInterface;
use Seboettg\Collection\Map\Pair;
use function Seboettg\Collection\Map\emptyMap;
use function Seboettg\Collection\Map\mapOf;
use function Seboettg\Collection\Map\pair;

class YearMonthRenderer extends DateRangeRenderer
{
    public function parseDateRange(
        ListInterface $datePartsList,
        DateTime $from,
        DateTime $to,
        string $delimiter
    ): string {
        $dp = $datePartsList->toArray();
        $dateParts = [];
        array_walk($dp, function (Pair $datePartPair) use (&$dateParts) {
            $datePart = $datePartPair->getValue();
            $key = $datePartPair->getKey();
            if (strpos($key, "year") !== false || strpos($key, "month") !== false) {
                $dateParts["yearmonth"][] = $datePart;
            }
            if (strpos($key, "day") !== false) {
                $dateParts["day"] = $datePart;
            }
        });
        $map = emptyMap();
        $map->setArray($dateParts);
        return $this->renderDateParts($map->toList(), $from, $to, $delimiter);
    }
}
