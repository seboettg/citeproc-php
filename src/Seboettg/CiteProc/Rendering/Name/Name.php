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
use Seboettg\CiteProc\Style\InheritableNameAttributesTrait;
use Seboettg\CiteProc\Style\SubsequentAuthorSubstituteRule;
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
    use InheritableNameAttributesTrait,
        FormattingTrait,
        AffixesTrait,
        DelimiterTrait;

    /**
     * @var array
     */
    protected $nameParts;

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
     * @var \SimpleXMLElement
     */
    private $node;

    /**
     * @var string
     */
    private $etAl;

    /**
     * Name constructor.
     * @param \SimpleXMLElement $node
     * @param Names $parent
     */
    public function __construct(\SimpleXMLElement $node, Names $parent)
    {
        $this->node = $node;
        $this->parent = $parent;

        $this->nameParts = [];

        /** @var \SimpleXMLElement $child */
        foreach ($node->children() as $child) {

            switch ($child->getName()) {
                case "name-part":
                    /** @var NamePart $namePart */
                    $namePart = Factory::create($child, $this);
                    $this->nameParts[$namePart->getName()] = $namePart;
            }
        }

        foreach ($node->attributes() as $attribute) {
            switch ($attribute->getName()) {
                case 'form':
                    $this->form = (string) $attribute;
                    break;
            }

        }

        $this->initFormattingAttributes($node);
        $this->initAffixesAttributes($node);
        $this->initDelimiterAttributes($node);
    }

    public function render($data, $citationNumber = null)
    {
        if (!$this->attributesInitialized) {
            $this->initInheritableNameAttributes($this->node);
        }
        if ("text" === $this->and) {
            $this->and = CiteProc::getContext()->getLocale()->filter('terms', 'and')->single;
        } elseif ('symbol' === $this->and) {
            $this->and = '&#38;';
        }

        $lastName = null;
        $resultNames = [];
        $count = 0;
        $hasPreceding = CiteProc::getContext()->getCitationItems()->hasKey($citationNumber-1);

        if ($hasPreceding) {
            /** @var \stdClass $preceding */
            $preceding = CiteProc::getContext()->getCitationItems()->get($citationNumber-1);
        }

        $subsequentSubstitution = CiteProc::getContext()->getCitationItems()->getSubsequentAuthorSubstitute();
        $subsequentSubstitutionRule = CiteProc::getContext()->getCitationItems()->getSubsequentAuthorSubstituteRule();

        $useSubseqSubstitution = !empty($subsequentSubstitution) && !empty($subsequentSubstitutionRule);

        /**
         * @var string $type
         * @var array $name
         */
        foreach ($data as $rank => $name) {
            ++$count;

            if (!empty($preceding) && $useSubseqSubstitution) {
                if ($subsequentSubstitutionRule == SubsequentAuthorSubstituteRule::PARTIAL_EACH && $rank > 0) {
                    if ($preceding->author[$rank]->family === $name->family) {
                        $resultNames[] = $subsequentSubstitution;
                    } else {
                        $resultNames[] = $this->formatName($name, $rank);
                    }
                } else if ($subsequentSubstitutionRule == SubsequentAuthorSubstituteRule::PARTIAL_FIRST && $rank === 0) {
                    if ($preceding->author[0]->family === $name->family) {
                        $resultNames[] = $subsequentSubstitution;
                    } else {
                        $resultNames[] = $this->formatName($name, $rank);
                    }
                }
            }
            else {
                $resultNames[] = $this->formatName($name, $rank);
            }
        }

        $resultNames = $this->prepareAbbreviation($resultNames);

        if ($this->etAlUseLast) {
            /* When set to “true” (the default is “false”), name lists truncated by et-al abbreviation are followed by
            the name delimiter, the ellipsis character, and the last name of the original name list. This is only
            possible when the original name list has at least two more names than the truncated name list (for this
            the value of et-al-use-first/et-al-subsequent-min must be at least 2 less than the value of
            et-al-min/et-al-subsequent-use-first). */
            $this->and = "…"; // set "and"
            $this->etAl = null; //reset $etAl;
        }

        /* add "and" */
        $count = count($resultNames);
        if (!empty($this->and) && $count > 1 && empty($this->etAl)) {
            $new = $this->and . ' ' . end($resultNames); // add and-prefix of the last name if "and" is defined
            $resultNames[count($resultNames) - 1] = $new; //set prefixed last name at the last position of $resultNames array
        }

        if (!empty($this->and)) {
            switch ($this->delimiterPrecedesLast) {
                case 'after-inverted-name':
                    //TODO: implement
                    break;
                case 'always':
                    $text = implode($this->delimiter, $resultNames);
                    break;
                case 'never':
                    if (count($resultNames) === 1) {
                        $text = $resultNames[0];
                    } else if (count($resultNames) === 2) {
                        $text = implode(" ", $resultNames);
                    } else { // >2
                        $lastName = array_pop($resultNames);
                        $text = implode($this->delimiter, $resultNames) . " " . $lastName;
                    }
                    break;
                case 'contextual':
                default:
                    if (count($resultNames) === 1) {
                        $text = $resultNames[0];
                    } else if (count($resultNames) === 2) {
                        $text = implode(" ", $resultNames);
                    } else {
                        $text = implode($this->delimiter, $resultNames);
                    }
            }
        } else {
            $text = implode($this->delimiter, $resultNames);
        }



        //append et al abbreviation
        if (count($data) > 1 && !empty($resultNames) && !empty($this->etAl)) {
            $text = $this->appendEtAl($text, $resultNames);
        }

        /* A third value, “count”, returns the total number of names that would otherwise be rendered by the use of the
        cs:names element (taking into account the effects of et-al abbreviation and editor/translator collapsing),
        which allows for advanced sorting. */

        if ($this->form == 'count') {
            return (int)count($resultNames);
        }

        return $text;
    }

    private function formatName($name, $rank)
    {
        $nameObj = $this->cloneNamePOSC($name);

        $useInitials = $this->initialize && !empty($this->initializeWith);
        if ($useInitials && isset($name->given)) {
            $nameObj->given = StringHelper::initializeBySpaceOrHyphen($name->given, $this->initializeWith);
        }

        // format name-parts
        if (count($this->nameParts) > 0) {
            /** @var NamePart $namePart */
            foreach ($this->nameParts as $namePart) {
                $nameObj->{$namePart->getName()} =   $namePart->render($name);
            }
        }

        $return = $this->getNamesString($nameObj, $rank);

        return trim($return);
    }

    /**
     * @param \stdClass $name
     * @param int $rank
     * @return string
     */
    private function getNamesString($name, $rank)
    {
        $text = "";

        if (!isset($name->family)) {
            return $text;
        }

        $given = !empty($name->given) ? $this->format(trim($name->given)) : "";
        $nonDroppingParticle = isset($name->{'non-dropping-particle'}) ? $name->{'non-dropping-particle'} : "";
        $droppingParticle = isset($name->{'dropping-particle'}) ? $name->{'dropping-particle'} : "";
        $suffix = (isset($name->{'suffix'})) ? $name->{'suffix'} : "";

        if (isset($name->family)) {
            $family = $this->format($name->family);
            if ($this->form == 'short') {
                $text = (!empty($nonDroppingParticle) ? $nonDroppingParticle . " " : "") . $family;
            } else {
                switch ($this->nameAsSortOrder) {

                    case 'all':
                    case 'first':
                        if ($this->nameAsSortOrder === "first" && $rank !== 0) {
                            break;
                        }
                        /*
                        use form "[non-dropping particel] family name,
                        given name [dropping particle], [suffix]"
                        */
                        $text  = !empty($nonDroppingParticle) ? "$nonDroppingParticle " : "";
                        $text .= $family;
                        $text .= !empty($given) ? $this->sortSeparator . $given : "";
                        $text .= !empty($droppingParticle) ? " $droppingParticle" : "";
                        $text .= !empty($suffix) ? $this->sortSeparator . $suffix : "";

                        //remove last comma when no suffix exist.
                        $text = trim($text);
                        $text = substr($text, -1) === $this->sortSeparator ? substr($text, 0, strlen($text) - 1) : $text;
                        break;
                    default:
                        /*
                        use form "given name [dropping particles] [non-dropping particles] family name [suffix]"
                        e.g. [Jean] [de] [La] [Fontaine] [III]
                        */
                        $text = sprintf(
                            "%s %s %s %s %s",
                            $given,
                            $droppingParticle,
                            $nonDroppingParticle,
                            $family,
                            $suffix);

                }
            }
        }

        //contains nbsp prefixed by normal space or followed by normal space?
        $text = htmlentities($text);
        if (strpos($text, " &nbsp;") !== false || strpos($text, "&nbsp; ") !== false) {

            $text = preg_replace("/[\s]+/", "", $text); //remove normal spaces
            return preg_replace("/&nbsp;+/", " ", $text);
        }
        $text = html_entity_decode(preg_replace("/[\s]+/", " ", $text));
        return trim($text);
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
            } else if ($property->getName() == "and" && $property->getValue($this) === "&#38;") {
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

    /**
     * @param $name
     * @return \stdClass
     */
    private function cloneNamePOSC($name)
    {
        $nameObj = new \stdClass();
        if (isset($name->family)) {
            $nameObj->family = $name->family;
        }
        if (isset($name->given)) {
            $nameObj->given = $name->given;
        }
        if (isset($name->{'non-dropping-particle'})) {
            $nameObj->{'non-dropping-particle'} = $name->{'non-dropping-particle'};
        }
        if (isset($name->{'dropping-particle'})) {
            $nameObj->{'dropping-particle'} = $name->{'dropping-particle'};
        }
        if (isset($name->{'suffix'})) {
            $nameObj->{'suffix'} = $name->{'suffix'};
        }
        return $nameObj;
    }

    /**
     * @param $text
     * @param $resultNames
     * @return string
     */
    protected function appendEtAl($text, $resultNames)
    {
        /* By default, when a name list is truncated to a single name, the name and the “et-al” (or “and others”)
        term are separated by a space (e.g. “Doe et al.”). When a name list is truncated to two or more names, the
        name delimiter is used (e.g. “Doe, Smith, et al.”). This behavior can be changed with the
        delimiter-precedes-et-al attribute. */

        switch ($this->delimiterPrecedesEtAl) {
            case 'never':
                $text = $text . " " . $this->etAl;
                break;
            case 'always':
                $text = $text . $this->delimiter . $this->etAl;
                break;
            case 'contextual':
            default:
                if (count($resultNames) === 1) {
                    $text .= " " . $this->etAl;
                } else {
                    $text .= $this->delimiter . $this->etAl;
                }
        }

        return $text;
    }

    /**
     * @param $resultNames
     * @return array
     */
    protected function prepareAbbreviation($resultNames)
    {
        /* Use of et-al-min and et-al-user-first enables et-al abbreviation. If the number of names in a name variable
        matches or exceeds the number set on et-al-min, the rendered name list is truncated after reaching the number of
        names set on et-al-use-first.  */
        if (isset($this->etAlMin) && isset($this->etAlUseFirst)) {
            $cnt = count($resultNames);
            if ($this->etAlMin >= count($cnt)) {
                if ($this->etAlUseLast && $this->etAlMin - $this->etAlUseFirst >= 2) {
                    /* et-al-use-last: When set to “true” (the default is “false”), name lists truncated by et-al
                    abbreviation are followed by the name delimiter, the ellipsis character, and the last name of the
                    original name list. This is only possible when the original name list has at least two more names
                    than the truncated name list (for this the value of et-al-use-first/et-al-subsequent-min must be at
                    least 2 less than the value of et-al-min/et-al-subsequent-use-first).*/

                    $lastName = array_pop($resultNames); //remove last Element and remember in $lastName

                }
                for ($i = $this->etAlUseFirst; $i < count($resultNames); ++$i) {
                    unset($resultNames[$i]);
                }

                $resultNames = array_values($resultNames);

                if (!empty($lastName)) { // append $lastName if exist
                    $resultNames[] = $lastName;
                }

                if ($this->parent->hasEtAl()) {
                    $this->etAl = $this->parent->getEtAl()->render(null);
                    return $resultNames;
                } else {
                    $this->etAl = CiteProc::getContext()->getLocale()->filter('terms', 'et-al')->single;
                    return $resultNames;
                }
            }
            return $resultNames;
        }
        return $resultNames;
    }


}
