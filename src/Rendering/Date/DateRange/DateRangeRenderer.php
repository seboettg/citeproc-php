<?php
/*
 * citeproc-php: DateRangeParser.php
 * User: Sebastian Böttger <sebastian.boettger@thomascook.de>
 * created at 03.11.19, 20:00
 */

namespace Seboettg\CiteProc\Rendering\Date\DateRange;

use Seboettg\CiteProc\Rendering\Date\DatePart;
use Seboettg\CiteProc\Rendering\Date\DateTime;
use Seboettg\CiteProc\Rendering\Date\Date;
use Seboettg\Collection\ArrayList;

/**
 * Class DatePartRenderer
 *
 * @package Seboettg\CiteProc\Rendering\Date\DateRange
 */
abstract class DateRangeRenderer
{

    /**
     * @var Date
     */
    protected $parentDateObject;

    /**
     * @param  Date $dateObject
     * @param  int  $toRender
     * @return DateRangeRenderer
     */
    public static function factory(Date $dateObject, $toRender)
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

    private static function getRenderer($toRender)
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

    /**
     * @param  ArrayList<DatePart> $dateParts
     * @param  DateTime            $from
     * @param  DateTime            $to
     * @param  $delimiter
     * @return string
     */
    abstract public function parseDateRange(ArrayList $dateParts, DateTime $from, DateTime $to, $delimiter);

    /**
     * @param  DatePart $datePart
     * @param  DateTime $from
     * @param  DateTime $to
     * @param  $delimiter
     * @return string
     */
    protected function renderOneRangePart(DatePart $datePart, DateTime $from, DateTime $to, $delimiter)
    {
        $prefix = $datePart->renderPrefix();
        $from = $datePart->renderWithoutAffixes($from, $this->parentDateObject);
        $to = $datePart->renderWithoutAffixes($to, $this->parentDateObject);
        $suffix = !empty($to) ? $datePart->renderSuffix() : "";
        return $prefix . $from . $delimiter . $to . $suffix;
    }

    protected function renderDateParts($dateParts, $from, $to, $delimiter)
    {
        $ret = "";
        foreach ($dateParts as $datePart) {
            if (is_array($datePart)) {
                $renderedFrom  = $datePart[0]->render($from, $this->parentDateObject);
                $renderedFrom .= $datePart[1]->renderPrefix();
                $renderedFrom .= $datePart[1]->renderWithoutAffixes($from, $this->parentDateObject);
                $renderedTo  = $datePart[0]->renderWithoutAffixes($to, $this->parentDateObject);
                $renderedTo .= $datePart[0]->renderSuffix();
                $renderedTo .= $datePart[1]->render($to, $this->parentDateObject);
                $ret .= $renderedFrom . $delimiter . $renderedTo;
            } else {
                $ret .= $datePart->render($from, $this->parentDateObject);
            }
        }
        return $ret;
    }
}
