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

class YearMonthDayRenderer extends DateRangeRenderer
{
    public function parseDateRange(ListInterface $datePartsList, DateTime $from, DateTime $to, string $delimiter): string
    {
        $ret = "";
        $i = 0;
        foreach ($datePartsList as $datePartPair) {
            $datePart = $datePartPair->getValue();
            if ($i === $datePartsList->count() - 1) {
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
        foreach ($datePartsList as $datePartPair) {
            $datePart = $datePartPair->getValue();
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
