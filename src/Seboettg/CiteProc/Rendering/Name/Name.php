<?php

namespace Seboettg\CiteProc\Rendering\Name;
use Seboettg\CiteProc\CiteProc;
use Seboettg\CiteProc\Styles\AffixesTrait;
use Seboettg\CiteProc\Styles\DelimiterTrait;
use Seboettg\CiteProc\Styles\FormattingTrait;


/**
 * Class Name
 *
 * The cs:name element, an optional child element of cs:names, can be used to describe the formatting of individual
 * names, and the separation of names within a name variable.
 *
 * @package Seboettg\CiteProc\Rendering\Name
 *
 * @author Sebastian Böttger <boettger@hebis.uni-frankfurt.de>
 */
class Name
{
    use FormattingTrait,
        AffixesTrait,
        DelimiterTrait;

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
     * When set to “true” (the default is “false”), name lists truncated by et-al abbreviation are followed by the name
     * delimiter, the ellipsis character, and the last name of the original name list. This is only possible when the
     * original name list has at least two more names than the truncated name list (for this the value of
     * et-al-use-first/et-al-subsequent-min must be at least 2 less than the value of
     * et-al-min/et-al-subsequent-use-first).
     * A. Goffeau, B. G. Barrell, H. Bussey, R. W. Davis, B. Dujon, H. Feldmann, … S. G. Oliver
     *
     * @var bool
     */
    private $etAlUseLast = false;

    /**
     * Specifies whether all the name-parts of personal names should be displayed (value “long”, the default), or only
     * the family name and the non-dropping-particle (value “short”). A third value, “count”, returns the total number
     * of names that would otherwise be rendered by the use of the cs:names element (taking into account the effects of
     * et-al abbreviation and editor/translator collapsing), which allows for advanced sorting.
     *
     * @var string
     */
    private $form;

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
    private $initializeWith = "";

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
    private $nameAsSortOrder;

    /**
     * Sets the delimiter for name-parts that have switched positions as a result of name-as-sort-order. The default
     * value is ”, ” (“Doe, John”). As is the case for name-as-sort-order, this attribute only affects names written in
     * the latin or Cyrillic alphabets.
     *
     * @var string
     */
    private $sortSeparator = ", ";

    /**
     * Name constructor.
     * @param $node
     */
    public function __construct(\SimpleXMLElement $node)
    {
        /** @var \SimpleXMLElement $attribute */
        foreach ($node->attributes() as $attribute) {
            switch ($attribute->getName()) {
                case 'and':
                    $and = (string)$attribute;
                    if ("text" === $and) {
                        $this->and = CiteProc::getContext()->getLocale()->filter('terms', 'and')->single;
                    } elseif ('symbol' === $and) {
                        $this->and = '&';
                    }
                    break;
                case 'delimiter-precedes-et-al':
                    $this->delimiterPrecedesEtAl = (string)$attribute;
                    break;
                case 'delimiter-precedes-last':
                    $this->delimiterPrecedesLast = (string)$attribute;
                    break;
                case 'et-al-min':
                    $this->etAlMin = intval((string)$attribute);
                    break;
                case 'et-al-use-first':
                    $this->etAlUseFirst = intval((string)$attribute);
                    break;
                case 'et-al-subsequent-min':
                    $this->etAlSubsequentMin = intval((string)$attribute);
                    break;
                case 'et-al-subsequent-use-first':
                    $this->etAlSubsequentUseFirst = intval((string)$attribute);
                    break;
                case 'et-al-use-last':
                    $this->etAlUseLast = boolval((string)$attribute);
                    break;
                case 'form':
                    $this->form = (string)$attribute;
                    break;
                case 'initialize':
                    $this->initialize = boolval((string)$attribute);
                    break;
                case 'initialize-with':
                    $this->initializeWith = (string)$attribute;
                    break;
                case 'name-as-sort-order':
                    $this->nameAsSortOrder = (string)$attribute;
                    break;
                case 'sort-separator':
                    $this->sortSeparator = (string)$attribute;

            }
        }

        $this->initFormattingAttributes($node);
        $this->initAffixesAttributes($node);
        $this->initDelimiterAttributes($node);
    }

    public function render($names)
    {
        $authors = [];
        $count = 0;
        $authCount = 0;
        $etAlTriggered = false;

        $useInitials = $this->initialize && !empty($this->initializeWith);

        foreach ($names as $rank => $name) {
            $count++;

            // use initials for given names
            if ($useInitials) {
                //TODO: initialize with hyphen
                $given = $name->given;
                $name->given = "";
                $givenParts = explode(" ", $given);
                foreach ($givenParts as $givenPart) {
                    $name->given .= substr($givenPart, 0, 1) . $this->initializeWith;
                }
            }

            $nonDroppingParticle = $name->{'non-dropping-particle'};
            $droppingParticle = $name->{'dropping-particle'};
            $suffix = (isset($name->{'suffix'})) ? ' ' . $name->{'suffix'} : '';
            if (!empty($name->given)) {
                $name->given = $this->format(trim($name->given));
            }

            if (isset($name->family)) {
                $name->family = $this->format($name->family);

                if ($this->form == 'short') {
                    $text = (!empty($nonDroppingParticle) ? $nonDroppingParticle . " " : "") . $name->family;
                } else {
                    switch ($this->nameAsSortOrder) {
                        /*
                            use form "[non-dropping particel] family name,
                            given name [dropping particle], [suffix]"
                         */
                        case 'first' && $rank == 0:
                        case 'all':
                            $text =
                                (!empty($nonDroppingParticle) ? $nonDroppingParticle . " " : "") .
                                (trim($name->family) . $this->sortSeparator . trim($name->given)) .
                                (!empty($droppingParticle) ? " " . $droppingParticle : "") .
                                (!empty($suffix)           ? $this->sortSeparator . $suffix : "");
                            break;
                        /*
                           use form "given name [dropping particles] [non-dropping particles] family name [suffix]"
                           e.g. [Jean] [de] [La] [Fontaine] [III]
                        */
                        default:
                            $text = trim($name->given) .
                                (!empty($droppingParticle)    ? " " . trim($droppingParticle)    : "") .
                                (!empty($nonDroppingParticle) ? " " . trim($nonDroppingParticle) : "") .
                                (" " . $name->family) .
                                (!empty($suffix)              ? " " . trim($suffix) : "");
                    }
                }
                $authors[] = trim($this->format($text));
            }
            if (isset($this->etAlMin) && $count >= $this->etAlMin) {
                break;
            }
        }
        if (isset($this->etAlMin) &&
            $count >= $this->etAlMin &&
            isset($this->etAlUseFirst) &&
            $count >= $this->etAlUseFirst &&
            count($names) > $this->etAlUseFirst
        ) {
            if ($this->etAlUseFirst < $this->etAlMin) {
                for ($i = $this->etAlUseFirst; $i < $count; $i++) {
                    unset($authors[$i]);
                }
            }
            $etAl = CiteProc::getContext()->getLocale()->filter('terms', 'et-al')->single;

            $etAlTriggered = true;
        }
        if (!empty($authors) && !$etAlTriggered) {
            $authCount = count($authors);
            if (!empty($this->and) && $authCount > 1) {
                $authors[$authCount - 1] = $this->and . ' ' . $authors[$authCount - 1]; //stick an "and" in front of the last author if "and" is defined
            }
        }
        $text = implode($this->delimiter, $authors);
        if (!empty($authors) && $etAlTriggered) {
            switch ($this->delimiterPrecedesEtAl) {
                case 'never':
                    $text = $text . " $etAl";
                    break;
                case 'always':
                    $text = $text . "$this->delimiter$etAl";
                    break;
                default:
                    $text = count($authors) == 1 ? $text . " $etAl" : $text . "$this->delimiter$etAl";
            }
        }
        if ($this->form == 'count') {
            if (!$etAlTriggered) {
                return (int)count($authors);
            } else {
                return (int)(count($authors) - 1);
            }
        }
        // strip out the last delimiter if not required
        if (isset($this->and) && $authCount > 1) {
            $lastDelim = strrpos($text, $this->delimiter . $this->and);
            switch ($this->delimiterPrecedesLast) {
                case 'always':
                    return $text;
                    break;
                case 'never':
                    return substr_replace($text, ' ', $lastDelim, strlen($this->delimiter));
                    break;
                case 'contextual':
                default:
                    if ($authCount < 3) {
                        return substr_replace($text, ' ', $lastDelim, strlen($this->delimiter));
                    }
            }
        }
        return $text;
    }
    private function initAttributes($mode)
    {
        //   $and = $this->get_attributes('and');
        if (isset($this->citeProc)) {
            $styleAttrs = $this->citeProc->style->getHierAttributes();
            $modeAttrs = $this->citeProc->{$mode}->getHierAttributes();
            $this->attributes = array_merge($styleAttrs, $modeAttrs, $this->attributes);
        }
        if (isset($this->and)) {
            if ($this->and == 'text') {
                $this->and = $this->citeProc->getLocale()->locale('term', 'and');
            } elseif ($this->and == 'symbol') {
                $this->and = '&';
            }
        }
        if (!isset($this->delimiter)) {
            $this->delimiter = $this->{'name-delimiter'};
        }
        if (!isset($this->alnum)) {
            list($this->alnum, $this->alpha, $this->cntrl, $this->dash,
                $this->digit, $this->graph, $this->lower, $this->print,
                $this->punct, $this->space, $this->upper, $this->word,
                $this->patternModifiers) = $this->get_regex_patterns();
        }
        $this->dpl = $this->{'delimiter-precedes-last'};
        $this->sort_separator = isset($this->{'sort-separator'}) ? $this->{'sort-separator'} : ', ';
        $this->delimiter = isset($this->{'name-delimiter'}) ? $this->{'name-delimiter'} : (isset($this->delimiter) ? $this->delimiter : ', ');
        $this->form = isset($this->{'name-form'}) ? $this->{'name-form'} : (isset($this->form) ? $this->form : 'long');
        $this->attr_init = $mode;
    }

}