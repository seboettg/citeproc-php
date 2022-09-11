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
use Seboettg\CiteProc\Rendering\Date\Date;
use Seboettg\Collection\Lists\ListInterface;
use Seboettg\Collection\Map\MapInterface;
use Seboettg\Collection\Map\Pair;

abstract class DateRangeRenderer
{
    protected const YEAR = "year";
    protected const MONTH = "month";
    protected const DAY = "day";
    protected const YEARMONTHDAY = "yearmonthday";
    protected const YEARMONTH = "yearmonth";
    protected const YEARDAY = "yearday";
    protected const MONTHDAY = "monthday";


    /**
     * @var Date
     */
    protected Date $parentDateObject;

    /**
     * @param  Date $dateObject
     * @param int $toRender
     * @return DateRangeRenderer
     */
    public static function factory(Date $dateObject, int $toRender): DateRangeRenderer
    {
        $className = self::getRenderer($toRender);
        return new $className($dateObject);
    }

    /**
     * DatePartRenderer constructor.
     *
     * @param Date $parentDateObject
     */
    public function __construct(Date $parentDateObject)
    {
        $this->parentDateObject = $parentDateObject;
    }

    private static function getRenderer($toRender): string
    {
        $className = "";
        switch ($toRender) {
            case Date::DATE_RANGE_STATE_DAY:
                $className = "DayRenderer";
                break;
            case Date::DATE_RANGE_STATE_MONTH:
                $className = "MonthRenderer";
                break;
            case Date::DATE_RANGE_STATE_YEAR:
                $className = "YearRenderer";
                break;
            case Date::DATE_RANGE_STATE_MONTHDAY:
                $className = "MonthDayRenderer";
                break;
            case Date::DATE_RANGE_STATE_YEARDAY:
                $className = "YearDayRenderer";
                break;
            case Date::DATE_RANGE_STATE_YEARMONTH:
                $className = "YearMonthRenderer";
                break;
            case Date::DATE_RANGE_STATE_YEARMONTHDAY:
                $className = "YearMonthDayRenderer";
                break;
        }
        return __NAMESPACE__ . "\\" . $className;
    }

    abstract public function parseDateRange(
        ListInterface $datePartsList,
        DateTime      $from,
        DateTime      $to,
        string        $delimiter
    ): string;

    protected function renderOneRangePart(DatePart $datePart, DateTime $from, DateTime $to, string $delimiter): string
    {
        $prefix = $datePart->renderPrefix();
        $from = $datePart->renderWithoutAffixes($from, $this->parentDateObject);
        $to = $datePart->renderWithoutAffixes($to, $this->parentDateObject);
        $suffix = !empty($to) ? $datePart->renderSuffix() : "";
        return $prefix . $from . $delimiter . $to . $suffix;
    }

    protected function renderDateParts(
        ListInterface $dateParts,
        DateTime $from,
        DateTime $to,
        string $delimiter
    ): string {
        return $dateParts->map(function (Pair $datePartPair) use ($from, $to, $delimiter) {
            $datePart = $datePartPair->getValue();
            if ($datePart instanceof MapInterface || is_array($datePart)) {
                $renderedFrom  = $datePart[0]->render($from, $this->parentDateObject);
                $renderedFrom .= $datePart[1]->renderPrefix();
                $renderedFrom .= $datePart[1]->renderWithoutAffixes($from, $this->parentDateObject);
                $renderedTo  = $datePart[0]->renderWithoutAffixes($to, $this->parentDateObject);
                $renderedTo .= $datePart[0]->renderSuffix();
                $renderedTo .= $datePart[1]->render($to, $this->parentDateObject);
                return $renderedFrom . $delimiter . $renderedTo;
            } else {
                return $datePart->render($from, $this->parentDateObject);
            }
        })->joinToString("");
    }
}
