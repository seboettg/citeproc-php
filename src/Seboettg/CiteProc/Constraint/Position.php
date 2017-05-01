<?php
/*
 * citeproc-php
 *
 * @link        http://github.com/seboettg/citeproc-php for the source repository
 * @copyright   Copyright (c) 2016 Sebastian Böttger.
 * @license     https://opensource.org/licenses/MIT
 */

namespace Seboettg\CiteProc\Constraint;


/**
 * Class Position
 *
 * Tests whether the cite position matches the given positions (terminology: citations consist of one or more cites to
 * individual items). When called within the scope of cs:bibliography, position tests “false”. The positions that can
 * be tested are:
 *   - “first”: position of cites that are the first to reference an item
 *   - “ibid”/”ibid-with-locator”/”subsequent”: cites referencing previously cited items have the “subsequent” position.
 *     Such cites may also have the “ibid” or “ibid-with-locator” position when:
 *     a) the current cite immediately follows on another cite, within the same citation, that references the same item, or
 *     b) the current cite is the first cite in the citation, and the previous citation consists of a single cite
 *        referencing the same item
 *     If either requirement is met, the presence of locators determines which position is assigned:
 *     -  Preceding cite does not have a locator: if the current cite has a locator, the position of the current cite is
 *        “ibid-with-locator”. Otherwise the position is “ibid”.
 *     -  Preceding cite does have a locator: if the current cite has the same locator, the position of the current cite
 *        is “ibid”. If the locator differs the position is “ibid-with-locator”. If the current cite lacks a locator
 *        its only position is “subsequent”.
 *   - “near-note”: position of a cite following another cite referencing the same item. Both cites have to be located
 *     in foot or endnotes, and the distance between both cites may not exceed the maximum distance (measured in number
 *     of foot or endnotes) set with the near-note-distance option.
 *
 * Whenever position=”ibid-with-locator” tests true, position=”ibid” also tests true. And whenever position=”ibid” or
 * position=”near-note” test true, position=”subsequent” also tests true.
 *
 * @codeCoverageIgnore
 *
 * @package Seboettg\CiteProc\Choose\Constraint
 *
 * @author Sebastian Böttger <seboettg@gmail.com>
 */
class Position implements ConstraintInterface
{
    /**
     * @param $value
     * @param int|null $citationNumber
     * @return bool
     */
    public function validate($value, $citationNumber = null)
    {
        if (!is_null($citationNumber)) {

        }

        return false;
    }
}