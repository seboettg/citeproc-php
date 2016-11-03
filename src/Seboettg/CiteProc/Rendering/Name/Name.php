<?php
/**
 * citeproc-php
 *
 * @link        http://github.com/seboettg/citeproc-php for the source repository
 * @copyright   Copyright (c) 2016 Sebastian Böttger.
 * @license     https://opensource.org/licenses/MIT
 */

namespace Seboettg\CiteProc\Rendering\Name;
use Seboettg\CiteProc\CiteProc;
use Seboettg\CiteProc\Styles\AffixesTrait;
use Seboettg\CiteProc\Styles\DelimiterTrait;
use Seboettg\CiteProc\Styles\FormattingTrait;
use Seboettg\CiteProc\Util\Factory;
use Seboettg\CiteProc\Util\StringHelper;


/**
 * Class Name
 *
 * The cs:name element, an optional child element of cs:names, can be used to describe the formatting of individual
 * names, and the separation of names within a name variable.
 *
 * @package Seboettg\CiteProc\Rendering\Name
 *
 * @author Sebastian Böttger <seboettg@gmail.com>
 */
class Name
{
    use FormattingTrait,
        AffixesTrait,
        DelimiterTrait;

    /**
     * @var array
     */
    protected $nameParts;

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
    private $form = "long";

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
     * Specifies the text string used to separate names in a name variable. Default is ”, ” (e.g. “Doe, Smith”).
     * @var
     */
    private $delimiter = ", ";


    /**
     * @var Names
     */
    private $parent;

    /**
     * Name constructor.
     * @param \SimpleXMLElement $node
     * @param Names $parent
     */
    public function __construct(\SimpleXMLElement $node, Names $parent)
    {
        $this->nameParts = [];
        $this->parent = $parent;

        /** @var \SimpleXMLElement $child */
        foreach ($node->children() as $child) {

            switch ($child->getName()) {
                case "name-part":
                    /** @var NamePart $namePart */
                    $namePart = Factory::create($child, $this);
                    $this->nameParts[$namePart->getName()] = $namePart;
            }
        }


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
                    $this->delimiterPrecedesEtAl = (string) $attribute;
                    break;
                case 'delimiter-precedes-last':
                    $this->delimiterPrecedesLast = (string) $attribute;
                    break;
                case 'et-al-min':
                    $this->etAlMin = intval((string) $attribute);
                    break;
                case 'et-al-use-first':
                    $this->etAlUseFirst = intval((string) $attribute);
                    break;
                case 'et-al-subsequent-min':
                    $this->etAlSubsequentMin = intval((string) $attribute);
                    break;
                case 'et-al-subsequent-use-first':
                    $this->etAlSubsequentUseFirst = intval((string) $attribute);
                    break;
                case 'et-al-use-last':
                    $this->etAlUseLast = boolval((string) $attribute);
                    break;
                case 'form':
                    $this->form = (string) $attribute;
                    break;
                case 'initialize':
                    $this->initialize = boolval((string) $attribute);
                    break;
                case 'initialize-with':
                    $this->initializeWith = (string) $attribute;
                    break;
                case 'name-as-sort-order':
                    $this->nameAsSortOrder = (string) $attribute;
                    break;
                case 'sort-separator':
                    $this->sortSeparator = (string) $attribute;

            }
        }

        $this->initFormattingAttributes($node);
        $this->initAffixesAttributes($node);
        $this->initDelimiterAttributes($node);
    }

    public function render($data)
    {
        $resultNames = [];
        $etAl = false;
        $count = 0;
        /**
         * @var string $type
         * @var array $names
         */
        foreach ($data as $rank => $names) {
            ++$count;
            $resultNames[] = $this->formatName($names, $rank);
        }

        /* Use of et-al-min and et-al-user-first enables et-al abbreviation. If the number of names in a name variable
        matches or exceeds the number set on et-al-min, the rendered name list is truncated after reaching the number of
        names set on et-al-use-first.  */
        if (isset($this->etAlMin) && isset($this->etAlUseFirst)) {
            $cnt = count($resultNames);
            if ($this->etAlMin >= count($cnt)) {
                for ($i = $this->etAlUseFirst; $i < $cnt; ++$i) {
                    unset($resultNames[$i]);
                }
            }
            if ($this->parent->hasEtAl()) {
                $etAl = $this->parent->getEtAl()->render($names);
            } else {
                $etAl = CiteProc::getContext()->getLocale()->filter('terms', 'et-al')->single;
            }
        }


        /* add "and" */
        $count = count($resultNames);
        if (!empty($this->and) && $count > 1 && !$etAl) {
            $new = $this->and . ' ' . end($resultNames); //stick an "and" in front of the last author if "and" is defined
            $resultNames[key($resultNames)] = $new;
        }

        $text = implode($this->delimiter, $resultNames);

        if (!empty($resultNames) && $etAl) {

            /* By default, when a name list is truncated to a single name, the name and the “et-al” (or “and others”)
            term are separated by a space (e.g. “Doe et al.”). When a name list is truncated to two or more names, the
            name delimiter is used (e.g. “Doe, Smith, et al.”). This behavior can be changed with the
            delimiter-precedes-et-al attribute. */
            switch ($this->delimiterPrecedesEtAl) {
                case 'never':
                    $text = $text . " $etAl";
                    break;
                case 'always':
                    $text = $text . "$this->delimiter$etAl";
                    break;
                default:
                    if (count($resultNames) === 1) {
                        $text .= " $etAl";
                    } else {
                        $text .=  $this->delimiter . $etAl;
                    }

            }
        }
        if ($this->form == 'count') {
            if ($etAl === false) {
                return (int)count($resultNames);
            } else {
                return (int)(count($resultNames) - 1);
            }
        }
        // strip out the last delimiter if not required
        if (isset($this->and) && count($resultNames) > 1) {
            $lastDelimiter = strrpos($text, $this->delimiter . $this->and);
            switch ($this->delimiterPrecedesLast) {
                case 'always':
                    return $text;
                    break;
                case 'never':
                    return substr_replace($text, ' ', $lastDelimiter, strlen($this->delimiter));
                    break;
                case 'contextual':
                default:
                    if (count($resultNames) < 3) {
                        return substr_replace($text, ' ', $lastDelimiter, strlen($this->delimiter));
                    }
            }
        }
        return $text;
    }

    private function formatName($name, $rank)
    {
        $useInitials = $this->initialize && !empty($this->initializeWith);
        if ($useInitials) {
            //TODO: initialize with hyphen
            $given = $name->given;
            $name->given = "";
            $givenParts = StringHelper::explodeBySpaceOrHyphen($given);
            foreach ($givenParts as $givenPart) {
                $name->given .= substr($givenPart, 0, 1) . $this->initializeWith;
            }
        }

        // format name-parts
        if (count($this->nameParts) > 0) {
            /** @var NamePart $namePart */
            foreach ($this->nameParts as $namePart) {
                $name->{$namePart->getName()} =   $namePart->render($name);
            }
            $name->suffix = '';
            $name->{'non-dropping-particle'} = '';
            $name->{'dropping-particle'} = '';
        }

        $return = $this->getNamesString($name, $rank);

        return trim($return);
    }

    /**
     * @param $name
     * @return string
     */
    private function getNamesString($name, $rank)
    {
        $text = "";
        $nonDroppingParticle = isset($name->{'non-dropping-particle'}) ? $name->{'non-dropping-particle'} : "";
        $droppingParticle = isset($name->{'dropping-particle'}) ? $name->{'dropping-particle'} : "";
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
                    case 'all':
                    case 'first':
                        if ($this->nameAsSortOrder === "first" && $rank !== 0) {
                            break;
                        }
                        $text =
                            (!empty($nonDroppingParticle) ? $nonDroppingParticle . " " : "") .
                            (trim($name->family) . $this->sortSeparator . trim($name->given)) .
                            (!empty($droppingParticle) ? " " . $droppingParticle : "") .
                            (!empty($suffix) ? $this->sortSeparator . trim($suffix) : "");
                        break;
                    /*
                       use form "given name [dropping particles] [non-dropping particles] family name [suffix]"
                       e.g. [Jean] [de] [La] [Fontaine] [III]
                    */
                    default:
                        $text = trim($name->given) .
                            (!empty($droppingParticle) ? " " . trim($droppingParticle) : "") .
                            (!empty($nonDroppingParticle) ? " " . trim($nonDroppingParticle) : "") .
                            (" " . $name->family) .
                            (!empty($suffix) ? " " . trim($suffix) : "");
                }
            }
        }

        return $text;
    }

    public function getOptions()
    {
        $ignore = ["namePart", "parent", "substitute"];
        $options = [];
        $reflectedName = new \ReflectionClass($this);

        foreach ($reflectedName->getProperties() as $property) {
            $property->setAccessible(true);
            if (in_array($property->getName(), $ignore)) {
                continue;
            } else if ($property->getName() == "and" && $property->getValue($this) === "&") {
                $options["and"] = "symbol";
            } else {
                $propValue = $property->getValue($this);
                if (isset($propValue) && !empty($propValue)) {
                    $options[StringHelper::camelCase2Hyphen($property->getName())] = $propValue;
                }
            }
        }
        return $options;
    }

}