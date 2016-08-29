<?php

namespace Seboettg\CiteProc\Style\Sort;
use Seboettg\CiteProc\Util\Variables;
use Seboettg\Collection\ArrayList;


/**
 * Class Sort
 * cs:citation and cs:bibliography may include a cs:sort child element before the cs:layout element to specify the
 * sorting order of respectively cites within citations, and bibliographic entries within the bibliography.
 *
 * he cs:sort element must contain one or more cs:key child elements. The sort key, set as an attribute on cs:key, must
 * be a variable (see Appendix IV - Variables) or macro name. For each cs:key element, the sort direction can be set to
 * either “ascending” (default) or “descending” with the sort attribute.
 *
 * @package Seboettg\CiteProc\Style
 *
 * @author Sebastian Böttger <boettger@hebis.uni-frankfurt.de>
 */
class Sort
{

    private $sortingKeys;

    /**
     * @var \SimpleXMLElement $node
     */
    public function __construct(\SimpleXMLElement $node)
    {
        $this->sortingKeys = new ArrayList();
        /** @var \SimpleXMLElement $child */
        foreach ($node->children() as $child) {
            if ("key" === $child->getName()) {
                $this->sortingKeys->append(new Key($child));
            }
        }
    }

    /**
     * Sort keys are evaluated in sequence. A primary sort is performed on all items using the first sort key.
     * A secondary sort, using the second sort key, is applied to items sharing the first sort key value. A tertiary
     * sort, using the third sort key, is applied to items sharing the first and second sort key values. Sorting
     * continues until either the order of all items is fixed, or until the sort keys are exhausted. Items with an
     * empty sort key value are placed at the end of the sort, both for ascending and descending sorts.
     */
    public function sort($data)
    {
        for ($i = $this->sortingKeys->count(); $i > 0; --$i) {
            /** @var Key $key */
            $key = $this->sortingKeys->get($i);
            $variable = $key->getVariable();
            $order = $key->getSort();

            /* Name variables called via the variable attribute (e.g. <key variable="author"/>) are returned as a
             * name list string, with the cs:name attributes form set to “long”, and name-as-sort-order set to “all”.
             */
            if (Variables::isNameVariable($variable)) {

                usort($data, function ($a, $b) use ($variable, $order) {
                    /**
                     * @param $a
                     * @param $key
                     * @return string
                     */
                    $strA = Variables::nameHash($a, $variable);
                    $strB = Variables::nameHash($b, $variable);
                    if ("descending" === $order) {
                        return strcmp($strB, $strA);
                    }
                    return strcmp($strA, $strB);
                });
            }

            /*
             * numbers: Number variables called via the variable attribute are returned as integers (form is “numeric”).
             * If the original variable value only consists of non-numeric text, the value is returned as a text string.
             */
            if (Variables::isNumberVariable($variable)) {
                usort($data,function ($a, $b) use ($variable, $order) {
                    $numA = $a->{$variable};
                    $numB = $b->{$variable};
                    if (is_numeric($numA) && is_numeric($numB)) {
                        $ret = $numA - $numB;
                    } else {
                        $ret = strcmp($numA, $numB);
                    }
                    if ("descending" === $order) {
                        return $ret <= 0 ? -1 : 1;
                    }
                    return $ret <= 0 ? 1 : -1;
                });
            }

            if (Variables::isDateVariable($variable)) {

            }
        }
    }

    /**
     * @return ArrayList
     */
    public function getSortingKeys()
    {
        return $this->sortingKeys;
    }
}