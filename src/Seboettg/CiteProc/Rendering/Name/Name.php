<?php

namespace Seboettg\CiteProc\Rendering\Name;
use Seboettg\CiteProc\CiteProc;
use Seboettg\CiteProc\Styles\AffixesTrait;
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
        AffixesTrait;

    /**
     * Specifies the delimiter between the second to last and last name of the names in a name variable. Allowed values
     * are “text” (selects the “and” term, e.g. “Doe, Johnson and Smith”) and “symbol” (selects the ampersand,
     * e.g. “Doe, Johnson & Smith”).
     *
     * @var string
     */
    private $and;

    /**
     * Specifies the text string used to separate names in a name variable. Default is ”, ” (e.g. “Doe, Smith”).
     *
     * @var string
     */
    private $delimiter;

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
     * @var string
     */
    private $etAlMin;

    /**
     * @var string
     */
    private $etAlUseFirst;

    /**
     * @var string
     */
    private $etAlSubsequentMin;

    /**
     * @var string
     */
    private $etAlSubsequentUseFirst;

    /**
     * @var string
     */
    private $etAlUseLast;

    /**
     * @var string
     */
    private $form;

    /**
     * @var string
     */
    private $initialize;

    /**
     * @var string
     */
    private $initializeWith;

    /**
     * @var string
     */
    private $nameAsSortOrder;

    /**
     * @var string
     */
    private $sortSeparator;

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
                case 'delimiter':
                    $this->delimiter = (string)$attribute;
                    break;
                case 'delimiter-precedes-et-al':
                    $this->delimiterPrecedesEtAl = (string)$attribute;
                    break;
                case 'delimiter-precedes-last':
                    $this->delimiterPrecedesLast = (string)$attribute;
                    break;
                case 'et-al-min':
                    $this->etAlMin = (string)$attribute;
                    break;
                case 'et-al-use-first':
                    $this->etAlUseFirst = (string)$attribute;
                    break;
                case 'et-al-subsequent-min':
                    $this->etAlSubsequentMin = (string)$attribute;
                    break;
                case 'et-al-subsequent-use-first':
                    $this->etAlSubsequentUseFirst = (string)$attribute;
                    break;
                case 'et-al-use-last':
                    $this->etAlUseLast = (string)$attribute;
                    break;
                case 'form':
                    $this->form = (string)$attribute;
                    break;
                case 'initialize':
                    $this->initialize = (string)$attribute;
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
        /* //TODO: necessary?
        if (empty($this->delimiter)) {
            $this->delimiter = $this->{'name-delimiter'};
        }
        */

        $this->initFormattingAttributes($node);
        $this->initAffixesAttributes($node);
    }

    public function render($names)
    {
        $authors = array();
        $count = 0;
        $auth_count = 0;
        $et_al_triggered = false;

        $initializeWith = $this->initializeWith;

        foreach ($names as $rank => $name) {
            $count++;
            /*
            //$given = (!empty($name->firstname)) ? $name->firstname : '';
            if (!empty($name->given) && isset($initializeWith)) {
                $name->given = preg_replace("/([$this->upper])[$this->lower]+/$this->patternModifiers", '\\1', $name->given);
                $name->given = preg_replace("/(?<=[-$this->upper]) +(?=[-$this->upper])/$this->patternModifiers", "", $name->given);
                if (isset($name->initials)) {
                    $name->initials = $name->given . $name->initials;
                }
                $name->initials = $name->given;
            }
            if (isset($name->initials)) {
                // within initials, remove any dots:
                $name->initials = preg_replace("/([$this->upper])\.+/$this->patternModifiers", "\\1", $name->initials);
                // within initials, remove any spaces *between* initials:
                $name->initials = preg_replace("/(?<=[-$this->upper]) +(?=[-$this->upper])/$this->patternModifiers", "", $name->initials);
                if ($this->citeProc->style->{'initialize-with-hyphen'} == 'false') {
                    $name->initials = preg_replace("/-/", '', $name->initials);
                }
                // within initials, add a space after a hyphen, but only if ...
                if (preg_match("/ $/", $initializeWith)) {// ... the delimiter that separates initials ends with a space
                    // $name->initials = preg_replace("/-(?=[$this->upper])/$this->patternModifiers", " -", $name->initials);
                }
                // then, separate initials with the specified delimiter:
                $name->initials = preg_replace("/([$this->upper])(?=[^$this->lower]+|$)/$this->patternModifiers", "\\1$initializeWith", $name->initials);
                //      $shortenInitials = (isset($options['numberOfInitialsToKeep'])) ? $options['numberOfInitialsToKeep'] : false;
                //      if ($shortenInitials) $given = drupal_substr($given, 0, $shortenInitials);
                if (isset($initializeWith)) {
                    $name->given = $name->initials;
                } elseif (!empty($name->given)) {
                    $name->given = $name->given . ' ' . $name->initials;
                } elseif (empty($name->given)) {
                    $name->given = $name->initials;
                }
            }
            */
            $ndp = (!empty($name->{'non-dropping-particle'})) ? $name->{'non-dropping-particle'} . ' ' : '';
            $suffix = (isset($name->{'suffix'})) ? ' ' . $name->{'suffix'} : '';
            if (isset($name->given)) {
                $given = $this->format($name->given);
            } else {
                $given = '';
            }
            if (isset($name->family)) {
                $name->family = $this->format($name->family);

                if ($this->form == 'short') {
                    $text = $ndp . $name->family;
                } else {
                    switch ($this->nameAsSortOrder) {
                        case 'first' && $rank == 0:
                        case 'all':
                            $text = $ndp . $name->family . $this->sortSeparator . $given;
                            break;
                        default:
                            $text = $given . ' ' . $ndp . $name->family . $suffix;
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
            $etal = $this->citeProc->getLocale()->locale('term', 'et-al');

            $et_al_triggered = true;
        }
        if (!empty($authors) && !$et_al_triggered) {
            $auth_count = count($authors);
            if (isset($this->and) && $auth_count > 1) {
                $authors[$auth_count - 1] = $this->and . ' ' . $authors[$auth_count - 1]; //stick an "and" in front of the last author if "and" is defined
            }
        }
        $text = implode($this->delimiter, $authors);
        if (!empty($authors) && $et_al_triggered) {
            switch ($this->delimiterPrecedesEtAl) {
                case 'never':
                    $text = $text . " $etal";
                    break;
                case 'always':
                    $text = $text . "$this->delimiter$etal";
                    break;
                default:
                    $text = count($authors) == 1 ? $text . " $etal" : $text . "$this->delimiter$etal";
            }
        }
        if ($this->form == 'count') {
            if (!$et_al_triggered) {
                return (int)count($authors);
            } else {
                return (int)(count($authors) - 1);
            }
        }
        // strip out the last delimiter if not required
        if (isset($this->and) && $auth_count > 1) {
            $last_delim = strrpos($text, $this->delimiter . $this->and);
            switch ($this->delimiterPrecedesLast) {
                case 'always':
                    return $text;
                    break;
                case 'never':
                    return substr_replace($text, ' ', $last_delim, strlen($this->delimiter));
                    break;
                case 'contextual':
                default:
                    if ($auth_count < 3) {
                        return substr_replace($text, ' ', $last_delim, strlen($this->delimiter));
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