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
use function Seboettg\Collection\Map\emptyMap;
use function Seboettg\Collection\Map\mapOf;
use function Seboettg\Collection\Map\pair;

class YearDayRenderer extends DateRangeRenderer
{
    public function parseDateRange(ListInterface $datePartsList, DateTime $from, DateTime $to, string $delimiter): string
    {
        $map = mapOf(pair(self::YEAR, emptyMap()), pair(self::MONTH, null));
        $i = 0;
        $datePartsList->forEach(function (string $key, DatePart $datePart) use ($map, &$i) {
            if (strpos($key, self::YEAR) !== false || strpos($key, self::DAY) !== false) {
                $map[self::YEARDAY][$i++] = $datePart;
            }
            if (strpos($key, self::MONTH) !== false) {
                $map[self::MONTH] = $datePart;
            }
        });
        return $this->renderDateParts($map, $from, $to, $delimiter);
    }
}
