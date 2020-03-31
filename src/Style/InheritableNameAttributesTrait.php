<?php
/*
 * citeproc-php
 *
 * @link        http://github.com/seboettg/citeproc-php for the source repository
 * @copyright   Copyright (c) 2017 Sebastian Böttger.
 * @license     https://opensource.org/licenses/MIT
 */

namespace Seboettg\CiteProc\Style;

use Seboettg\CiteProc\CiteProc;
use Seboettg\CiteProc\Rendering\HasParent;
use Seboettg\CiteProc\Rendering\Name\Name;
use Seboettg\CiteProc\Rendering\Name\Names;
use Seboettg\CiteProc\Root\Root;
use SimpleXMLElement;

/**
 * Class InheritableNameAttributesTrait
 *
 * Attributes for the cs:names and cs:name elements may also be set on cs:style, cs:citation and cs:bibliography. This
 * eliminates the need to repeat the same attributes and attribute values for every occurrence of the cs:names and
 * cs:name elements.
 *
 * The available inheritable attributes for cs:name are and, delimiter-precedes-et-al, delimiter-precedes-last,
 * et-al-min, et-al-use-first, et-al-use-last, et-al-subsequent-min, et-al-subsequent-use-first, initialize,
 * initialize-with, name-as-sort-order and sort-separator. The attributes name-form and name-delimiter correspond to the
 * form and delimiter attributes on cs:name. Similarly, names-delimiter corresponds to the delimiter attribute on
 * cs:names.
 *
 *
 * @package Seboettg\CiteProc\Style
 * @author Sebastian Böttger <seboettg@gmail.com>
 */
trait InheritableNameAttributesTrait
{
    public static $attributes = [
        'and',
        'delimiter-precedes-et-al',
        'delimiter-precedes-last',
        'et-al-min',
        'et-al-use-first',
        'et-al-use-last',
        'et-al-subsequent-min',
        'et-al-subsequent-use-first',
        'initialize',
        'initialize-with',
        'name-as-sort-order',
        'sort-separator',
        'name-form',
        'form',
        'name-delimiter',
        'delimiter'
    ];

    /**
     * @var bool
     */
    protected $attributesInitialized = false;

    /**
     * Specifies the delimiter between the second to last and last name of the names in a name variable. Allowed values
     * are “text” (selects the “and” term, e.g. “Doe, Johnson and Smith”) and “symbol” (selects the ampersand,
     * e.g. “Doe, Johnson & Smith”).
     *
     * @var string
     */
    private $and;

    /**
     * Determines when the name delimiter or a space is used between a truncated name list and the “et-al”
     * (or “and others”) term in case of et-al abbreviation. Allowed values:
     * - “contextual” - (default), name delimiter is only used for name lists truncated to two or more names
     *   - 1 name: “J. Doe et al.”
     *   - 2 names: “J. Doe, S. Smith, et al.”
     * - “after-inverted-name” - name delimiter is only used if the preceding name is inverted as a result of the
     *   - name-as-sort-order attribute. E.g. with name-as-sort-order set to “first”:
     *   - “Doe, J., et al.”
     *   - “Doe, J., S. Smith et al.”
     * - “always” - name delimiter is always used
     *   - 1 name: “J. Doe, et al.”
     *   - 2 names: “J. Doe, S. Smith, et al.”
     * - “never” - name delimiter is never used
     *   - 1 name: “J. Doe et al.”
     *   - 2 names: “J. Doe, S. Smith et al.”
     *
     * @var string
     */
    private $delimiterPrecedesEtAl;

    /**
     * Determines when the name delimiter is used to separate the second to last and the last name in name lists (if
     * and is not set, the name delimiter is always used, regardless of the value of delimiter-precedes-last). Allowed
     * values:
     *
     * - “contextual” - (default), name delimiter is only used for name lists with three or more names
     *   - 2 names: “J. Doe and T. Williams”
     *   - 3 names: “J. Doe, S. Smith, and T. Williams”
     * - “after-inverted-name” - name delimiter is only used if the preceding name is inverted as a result of the
     *   name-as-sort-order attribute. E.g. with name-as-sort-order set to “first”:
     *   - “Doe, J., and T. Williams”
     *   - “Doe, J., S. Smith and T. Williams”
     * - “always” - name delimiter is always used
     *   - 2 names: “J. Doe, and T. Williams”
     *   - 3 names: “J. Doe, S. Smith, and T. Williams”
     * - “never” - name delimiter is never used
     *   - 2 names: “J. Doe and T. Williams”
     *   - 3 names: “J. Doe, S. Smith and T. Williams”
     *
     * @var string
     */
    private $delimiterPrecedesLast;

    /**
     * Use of etAlMin (et-al-min attribute) and etAlUseFirst (et-al-use-first attribute) enables et-al abbreviation. If
     * the number of names in a name variable matches or exceeds the number set on etAlMin, the rendered name list is
     * truncated after reaching the number of names set on etAlUseFirst.
     *
     * @var int
     */
    private $etAlMin;

    /**
     * Use of etAlMin (et-al-min attribute) and etAlUseFirst (et-al-use-first attribute) enables et-al abbreviation. If
     * the number of names in a name variable matches or exceeds the number set on etAlMin, the rendered name list is
     * truncated after reaching the number of names set on etAlUseFirst.
     *
     * @var int
     */
    private $etAlUseFirst;

    /**
     * When set to “true” (the default is “false”), name lists truncated by et-al abbreviation are followed by the name
     * delimiter, the ellipsis character, and the last name of the original name list. This is only possible when the
     * original name list has at least two more names than the truncated name list (for this the value of
     * et-al-use-first/et-al-subsequent-min must be at least 2 less than the value of
     * et-al-min/et-al-subsequent-use-first).
     *
     * @var bool
     */
    private $etAlUseLast = false;

    /**
     * If used, the values of these attributes (et-al-subsequent-min and et-al-subsequent-use-first) replace those of
     * respectively et-al-min and et-al-use-first for subsequent cites (cites referencing earlier cited items).
     *
     * @var int
     */
    private $etAlSubsequentMin;

    /**
     * If used, the values of these attributes (et-al-subsequent-min and et-al-subsequent-use-first) replace those of
     * respectively et-al-min and et-al-use-first for subsequent cites (cites referencing earlier cited items).
     *
     * @var int
     */
    private $etAlSubsequentUseFirst;

    /**
     * When set to “false” (the default is “true”), given names are no longer initialized when “initialize-with” is set.
     * However, the value of “initialize-with” is still added after initials present in the full name (e.g. with
     * initialize set to “false”, and initialize-with set to ”.”, “James T Kirk” becomes “James T. Kirk”).
     *
     * @var bool
     */
    private $initialize = true;

    /**
     * When set, given names are converted to initials. The attribute value is added after each initial (”.” results
     * in “J.J. Doe”). For compound given names (e.g. “Jean-Luc”), hyphenation of the initials can be controlled with
     * the global initialize-with-hyphen option
     *
     * @var string
     */
    private $initializeWith = false;

    /**
     * Specifies that names should be displayed with the given name following the family name (e.g. “John Doe” becomes
     * “Doe, John”). The attribute has two possible values:
     *   - “first” - attribute only has an effect on the first name of each name variable
     *   - “all” - attribute has an effect on all names
     * Note that even when name-as-sort-order changes the name-part order, the display order is not necessarily the same
     * as the sorting order for names containing particles and suffixes (see Name-part order). Also, name-as-sort-order
     * only affects names written in the latin or Cyrillic alphabets. Names written in other alphabets (e.g. Asian
     * scripts) are always displayed with the family name preceding the given name.
     *
     * @var string
     */
    private $nameAsSortOrder = "";

    /**
     * Sets the delimiter for name-parts that have switched positions as a result of name-as-sort-order. The default
     * value is ”, ” (“Doe, John”). As is the case for name-as-sort-order, this attribute only affects names written in
     * the latin or Cyrillic alphabets.
     *
     * @var string
     */
    private $sortSeparator = ", ";

    /**
     * Specifies whether all the name-parts of personal names should be displayed (value “long”, the default), or only
     * the family name and the non-dropping-particle (value “short”). A third value, “count”, returns the total number
     * of names that would otherwise be rendered by the use of the cs:names element (taking into account the effects of
     * et-al abbreviation and editor/translator collapsing), which allows for advanced sorting.
     *
     * @var string
     */
    private $form;

    private $nameForm = "long";

    private $nameDelimiter = ", ";

    public function isDescendantOfMacro()
    {
        $parent = $this->parent;

        while ($parent != null && $parent instanceof HasParent) {
            if ($parent instanceof Macro) {
                return true;
            }
            $parent = $parent->getParent();
        }
        return false;
    }

    /**
     * @param SimpleXMLElement $node
     */
    public function initInheritableNameAttributes(SimpleXMLElement $node)
    {
        $context = CiteProc::getContext();
        $parentStyleElement = null;
        if ($this instanceof  Name || $this instanceof Names) {
            if ($context->getMode() === "bibliography") {
                if ($this->isDescendantOfMacro()) {
                    $parentStyleElement = $context->getRoot();
                } else {
                    $parentStyleElement = $context->getBibliography();
                }
            } else {
                $parentStyleElement = $context->getCitation();
            }
        } elseif ($this instanceof StyleElement) {
            $parentStyleElement = $context->getRoot();
        }

        foreach (self::$attributes as $nameAttribute) {
            $attribute = $node[$nameAttribute];
            switch ($nameAttribute) {
                case 'and':
                    if (!empty($attribute)) {
                        $this->and = (string) $attribute;
                    } elseif (!empty($parentStyleElement)) { //inherit from parent style
                        $this->and = $parentStyleElement->getAnd();
                    }
                    break;
                case 'delimiter-precedes-et-al':
                    if (!empty($attribute)) {
                        $this->delimiterPrecedesEtAl = (string) $attribute;
                    } elseif (!empty($parentStyleElement)) { //inherit from parent style
                        $this->delimiterPrecedesEtAl = $parentStyleElement->getDelimiterPrecedesEtAl();
                    }
                    break;
                case 'delimiter-precedes-last':
                    if (!empty($attribute)) {
                        $this->delimiterPrecedesLast = (string) $attribute;
                    } elseif (!empty($parentStyleElement)) { //inherit from parent style
                        $this->delimiterPrecedesLast = $parentStyleElement->getDelimiterPrecedesLast();
                    }
                    break;
                case 'et-al-min':
                    if (!empty($attribute)) {
                        $this->etAlMin = intval((string) $attribute);
                    } elseif (!empty($parentStyleElement)) {
                        $this->etAlMin = $parentStyleElement->getEtAlMin();
                    }
                    break;
                case 'et-al-use-first':
                    if (!empty($attribute)) {
                        $this->etAlUseFirst = intval((string) $attribute);
                    } elseif (!empty($parentStyleElement)) {
                        $this->etAlUseFirst = $parentStyleElement->getEtAlUseFirst();
                    }
                    break;
                case 'et-al-subsequent-min':
                    if (!empty($attribute)) {
                        $this->etAlSubsequentMin = intval((string) $attribute);
                    } elseif (!empty($parentStyleElement)) {
                        $this->etAlSubsequentMin = $parentStyleElement->getEtAlSubsequentMin();
                    }
                    break;
                case 'et-al-subsequent-use-first':
                    if (!empty($attribute)) {
                        $this->etAlSubsequentUseFirst = intval((string) $attribute);
                    } elseif (!empty($parentStyleElement)) {
                        $this->etAlSubsequentUseFirst = $parentStyleElement->getEtAlSubsequentUseFirst();
                    }
                    break;
                case 'et-al-use-last':
                    if (!empty($attribute)) {
                        $this->etAlUseLast = boolval((string) $attribute);
                    } elseif (!empty($parentStyleElement)) {
                        $this->etAlUseLast = $parentStyleElement->getEtAlUseLast();
                    }
                    break;
                case 'initialize':
                    if (!empty($attribute)) {
                        $this->initialize = boolval((string) $attribute);
                    } elseif (!empty($parentStyleElement)) {
                        $this->initialize = $parentStyleElement->getInitialize();
                    }
                    break;
                case 'initialize-with':
                    if (!empty($attribute)) {
                        $this->initializeWith = (string) $attribute;
                    } elseif (!empty($parentStyleElement)) {
                        $this->initializeWith = $parentStyleElement->getInitializeWith();
                    }
                    break;
                case 'name-as-sort-order':
                    if (!empty($attribute)) {
                        $this->nameAsSortOrder = (string) $attribute;
                    } elseif (!empty($parentStyleElement)) {
                        $this->nameAsSortOrder = $parentStyleElement->getNameAsSortOrder();
                    }
                    break;
                case 'sort-separator':
                    if (!empty($attribute)) {
                        $this->sortSeparator = (string) $attribute;
                    } elseif (!empty($parentStyleElement)) {
                        $this->sortSeparator = $parentStyleElement->getSortSeparator();
                    }
                    break;
                case 'name-form':
                    if ($this instanceof Root || $this instanceof StyleElement) {
                        if (!empty($attribute)) {
                            $this->setNameForm((string) $attribute);
                        } elseif (!empty($parentStyleElement)) {
                            $this->setNameForm($parentStyleElement->getNameForm());
                        }
                    }
                    break;
                case 'form':
                    if ($this instanceof Name) {
                        if (!empty($attribute)) {
                            $this->setForm((string) $attribute);
                        } elseif (!empty($parentStyleElement)) {
                            $this->setForm($parentStyleElement->getNameForm());
                        }
                    }
                    break;
                case 'name-delimiter':
                    if ($this instanceof Root || $this instanceof StyleElement) {
                        if (!empty($attribute)) {
                            $this->setNameDelimiter((string) $attribute);
                        } elseif (!empty($parentStyleElement)) {
                            $this->setNameDelimiter($parentStyleElement->getNameDelimiter());
                        }
                    } else {
                        /* The attributes name-form and name-delimiter correspond to the form and delimiter attributes
                           on cs:name. Similarly, names-delimiter corresponds to the delimiter attribute on cs:names. */
                        if (!empty($attribute)) {
                            $this->nameDelimiter = $this->delimiter = (string) $attribute;
                        } elseif (!empty($parentStyleElement)) {
                            $this->nameDelimiter = $this->delimiter = $parentStyleElement->getNameDelimiter();
                        }
                    }
                    break;
                case 'delimiter':
                    if ($this instanceof Name) {
                        if (!empty($attribute)) {
                            $this->setDelimiter((string) $attribute);
                        } elseif (!empty($parentStyleElement)) {
                            $this->setDelimiter($parentStyleElement->getNameDelimiter());
                        }
                    }
            }
        }
        $this->attributesInitialized = true;
    }

    /**
     * @return string
     */
    public function getAnd()
    {
        return $this->and;
    }

    /**
     * @param string $and
     */
    public function setAnd($and)
    {
        $this->and = $and;
    }

    /**
     * @return string
     */
    public function getDelimiterPrecedesEtAl()
    {
        return $this->delimiterPrecedesEtAl;
    }

    /**
     * @param string $delimiterPrecedesEtAl
     */
    public function setDelimiterPrecedesEtAl($delimiterPrecedesEtAl)
    {
        $this->delimiterPrecedesEtAl = $delimiterPrecedesEtAl;
    }

    /**
     * @return string
     */
    public function getDelimiterPrecedesLast()
    {
        return $this->delimiterPrecedesLast;
    }

    /**
     * @param string $delimiterPrecedesLast
     */
    public function setDelimiterPrecedesLast($delimiterPrecedesLast)
    {
        $this->delimiterPrecedesLast = $delimiterPrecedesLast;
    }

    /**
     * @return int
     */
    public function getEtAlMin()
    {
        return $this->etAlMin;
    }

    /**
     * @param int $etAlMin
     */
    public function setEtAlMin($etAlMin)
    {
        $this->etAlMin = $etAlMin;
    }

    /**
     * @return int
     */
    public function getEtAlUseFirst()
    {
        return $this->etAlUseFirst;
    }

    /**
     * @param int $etAlUseFirst
     */
    public function setEtAlUseFirst($etAlUseFirst)
    {
        $this->etAlUseFirst = $etAlUseFirst;
    }

    /**
     * @return bool
     */
    public function getEtAlUseLast()
    {
        return $this->etAlUseLast;
    }

    /**
     * @param bool $etAlUseLast
     */
    public function setEtAlUseLast($etAlUseLast)
    {
        $this->etAlUseLast = $etAlUseLast;
    }

    /**
     * @return int
     */
    public function getEtAlSubsequentMin()
    {
        return $this->etAlSubsequentMin;
    }

    /**
     * @param int $etAlSubsequentMin
     */
    public function setEtAlSubsequentMin($etAlSubsequentMin)
    {
        $this->etAlSubsequentMin = $etAlSubsequentMin;
    }

    /**
     * @return int
     */
    public function getEtAlSubsequentUseFirst()
    {
        return $this->etAlSubsequentUseFirst;
    }

    /**
     * @param int $etAlSubsequentUseFirst
     */
    public function setEtAlSubsequentUseFirst($etAlSubsequentUseFirst)
    {
        $this->etAlSubsequentUseFirst = $etAlSubsequentUseFirst;
    }

    /**
     * @return bool
     */
    public function getInitialize()
    {
        return $this->initialize;
    }

    /**
     * @param bool $initialize
     */
    public function setInitialize($initialize)
    {
        $this->initialize = $initialize;
    }

    /**
     * @return string
     */
    public function getInitializeWith()
    {
        return $this->initializeWith;
    }

    /**
     * @param string $initializeWith
     */
    public function setInitializeWith($initializeWith)
    {
        $this->initializeWith = $initializeWith;
    }

    /**
     * @return string
     */
    public function getNameAsSortOrder()
    {
        return $this->nameAsSortOrder;
    }

    /**
     * @param string $nameAsSortOrder
     */
    public function setNameAsSortOrder($nameAsSortOrder)
    {
        $this->nameAsSortOrder = $nameAsSortOrder;
    }

    /**
     * @return string
     */
    public function getSortSeparator()
    {
        return $this->sortSeparator;
    }

    /**
     * @param string $sortSeparator
     */
    public function setSortSeparator($sortSeparator)
    {
        $this->sortSeparator = $sortSeparator;
    }

    /**
     * @return mixed
     */
    public function getForm()
    {
        return $this->form;
    }

    /**
     * @param mixed $form
     */
    public function setForm($form)
    {
        $this->form = $form;
    }

    /**
     * @return string
     */
    public function getNameForm()
    {
        return $this->nameForm;
    }

    /**
     * @param string $nameForm
     */
    public function setNameForm($nameForm)
    {
        $this->nameForm = $nameForm;
    }

    /**
     * @return string
     */
    public function getNameDelimiter()
    {
        return $this->nameDelimiter;
    }

    /**
     * @param string $nameDelimiter
     */
    public function setNameDelimiter($nameDelimiter)
    {
        $this->nameDelimiter = $nameDelimiter;
    }
}
