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
use Seboettg\Collection\Map\Pair;

class DayRenderer extends DateRangeRenderer
{

    public function parseDateRange(ListInterface $datePartsList, DateTime $from, DateTime $to, string $delimiter): string
    {
        return $datePartsList->mapNotNull(function (Pair $pair) use ($from, $to, $delimiter) {
            $key = $pair->getKey();
            $datePart = $pair->getValue();
            if (strpos($key, "year") !== false) {
                return $datePart->render($from, $this->parentDateObject);
            }
            if (strpos($key, "month") !== false) {
                return $datePart->render($from, $this->parentDateObject);
            }
            if (strpos($key, "day")) {
                return $this->renderOneRangePart($datePart, $from, $to, $delimiter);
            }
            return null;
        })->joinToString("");
    }
}
