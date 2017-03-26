<?php
/*
 * citeproc-php
 *
 * @link        http://github.com/seboettg/citeproc-php for the source repository
 * @copyright   Copyright (c) 2017 Sebastian Böttger.
 * @license     https://opensource.org/licenses/MIT
 */

namespace Seboettg\CiteProc\Rendering\Date;


use DateTimeZone;

class DateTime extends \DateTime
{
    /**
     * @var int
     */
    private $year = 0;

    /**
     * @var int
     */
    private $month = 0;

    /**
     * @var int
     */
    private $day = 0;

    /**
     * DateTime constructor.
     * @param string $year
     * @param DateTimeZone $month
     * @param $day
     */
    public function __construct($year, $month, $day)
    {
        parent::__construct("$year-$month-$day", new DateTimeZone("Europe/Berlin"));
        $this->year = intval(self::format("Y"));
        $this->month = intval(self::format("n"));
        $this->day = intval(self::format("j"));
    }

    /**
     * @param int $year
     * @return $this
     */
    public function setYear($year)
    {
        $this->year = $year;
        return $this;
    }

    /**
     * @param int $month
     * @return $this
     */
    public function setMonth($month)
    {
        $this->month = $month;
        return $this;
    }

    /**
     * @param int $day
     * @return $this
     */
    public function setDay($day)
    {
        $this->day = $day;
        return $this;
    }

    /**
     * @param int $year
     * @param int $month
     * @param int $day
     * @return $this
     */
    public function setDate($year, $month, $day)
    {
        $this->year = $year;
        $this->month = $month;
        $this->day = $day;
        parent::setDate($year, $month, $day);
        return $this;
    }

    /**
     * @return array
     */
    public function getArray()
    {
        return [$this->year, $this->month, $this->day];
    }

    /**
     * @return int
     */
    public function getYear()
    {
        return $this->year;
    }

    /**
     * @return int
     */
    public function getMonth()
    {
        return $this->month;
    }

    /**
     * @return int
     */
    public function getDay()
    {
        return $this->day;
    }

    /**
     * @return string
     */
    public function renderNumeric()
    {
        $ret  = $this->year;
        $ret .= $this->month > 0 && $this->month < 13 ? "-" . sprintf("%02s", $this->month) : "";
        $ret .= $this->day > 0 && $this->day < 32 ? "-" . sprintf("%02s", $this->day) : "";
        return $ret;
    }
}