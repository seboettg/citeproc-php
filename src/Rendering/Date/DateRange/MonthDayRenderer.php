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
use Seboettg\Collection\Map\MapInterface;
use Seboettg\Collection\Map\Pair;
use function Seboettg\Collection\Lists\emptyList;
use function Seboettg\Collection\Lists\listOf;
use function Seboettg\Collection\Map\emptyMap;
use function Seboettg\Collection\Map\mapOf;
use function Seboettg\Collection\Map\pair;

class MonthDayRenderer extends DateRangeRenderer
{
    public function parseDateRange(ListInterface $datePartsList, DateTime $from, DateTime $to, string $delimiter): string
    {
        $dp = $datePartsList->toArray();
        $dateParts_ = [];
        array_walk($dp, function (Pair $datePartPair) use (&$dateParts_) {
            $datePart = $datePartPair->getValue();
            $key = $datePartPair->getKey();
            if (strpos($key, "month") !== false || strpos($key, "day") !== false) {
                $dateParts_["monthday"][] = $datePart;
            }
            if (strpos($key, "year") !== false) {
                $dateParts_["year"] = $datePart;
            }
        });
        $datePartsMap = emptyMap();
        $datePartsMap->setArray($dateParts_);
        return $this->renderDateParts($datePartsMap->toList(), $from, $to, $delimiter);
    }
}
