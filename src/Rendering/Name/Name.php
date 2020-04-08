<?php
/*
 * citeproc-php
 *
 * @link        http://github.com/seboettg/citeproc-php for the source repository
 * @copyright   Copyright (c) 2016 Sebastian Böttger.
 * @license     https://opensource.org/licenses/MIT
 */

namespace Seboettg\CiteProc\Rendering\Name;

use Seboettg\CiteProc\CiteProc;
use Seboettg\CiteProc\Exception\CiteProcException;
use Seboettg\CiteProc\Exception\InvalidStylesheetException;
use Seboettg\CiteProc\Rendering\HasParent;
use Seboettg\CiteProc\Style\InheritableNameAttributesTrait;
use Seboettg\CiteProc\Style\Options\DemoteNonDroppingParticle;
use Seboettg\CiteProc\Style\Options\SubsequentAuthorSubstituteRule;
use Seboettg\CiteProc\Styles\AffixesTrait;
use Seboettg\CiteProc\Styles\DelimiterTrait;
use Seboettg\CiteProc\Styles\FormattingTrait;
use Seboettg\CiteProc\Util\CiteProcHelper;
use Seboettg\CiteProc\Util\Factory;
use Seboettg\CiteProc\Util\NameHelper;
use Seboettg\CiteProc\Util\StringHelper;
use SimpleXMLElement;
use stdClass;

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
class Name implements HasParent
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
     *
     * @var
     */
    private $delimiter = ", ";

    /**
     * @var Names
     */
    private $parent;

    /**
     * @var SimpleXMLElement
     */
    private $node;

    /**
     * @var string
     */
    private $etAl;

    /**
     * @var string
     */
    private $variable;

    /**
     * Name constructor.
     *
     * @param  SimpleXMLElement $node
     * @param  Names            $parent
     * @throws InvalidStylesheetException
     */
    public function __construct(SimpleXMLElement $node, Names $parent)
    {
        $this->node = $node;
        $this->parent = $parent;

        $this->nameParts = [];

        /**
         * @var SimpleXMLElement $child
*/
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

    /**
     * @param  stdClass     $data
     * @param  string       $var
     * @param  integer|null $citationNumber
     * @return string
     * @throws CiteProcException
     */
    public function render($data, $var, $citationNumber = null)
    {
        $this->variable = $var;
        $name = $data->{$var};
        if (!$this->attributesInitialized) {
            $this->initInheritableNameAttributes($this->node);
        }
        if ("text" === $this->and) {
            $this->and = CiteProc::getContext()->getLocale()->filter('terms', 'and')->single;
        } elseif ('symbol' === $this->and) {
            $this->and = '&#38;';
        }

        $resultNames = $this->handleSubsequentAuthorSubstitution($name, $citationNumber);

        if (empty($resultNames)) {
            return CiteProc::getContext()->getCitationData()->getSubsequentAuthorSubstitute();
        }

        $resultNames = $this->prepareAbbreviation($resultNames);

        /* When set to “true” (the default is “false”), name lists truncated by et-al abbreviation are followed by
        the name delimiter, the ellipsis character, and the last name of the original name list. This is only
        possible when the original name list has at least two more names than the truncated name list (for this
        the value of et-al-use-first/et-al-subsequent-min must be at least 2 less than the value of
        et-al-min/et-al-subsequent-use-first). */
        if ($this->etAlUseLast) {
            $this->and = "…"; // set "and"
            $this->etAl = null; //reset $etAl;
        }

        /* add "and" */
        $this->addAnd($resultNames);

        $text = $this->renderDelimiterPrecedesLast($resultNames);

        if (empty($text)) {
            $text = implode($this->delimiter, $resultNames);
        }

        $text = $this->appendEtAl($name, $text, $resultNames);

        /* A third value, “count”, returns the total number of names that would otherwise be rendered by the use of the
        cs:names element (taking into account the effects of et-al abbreviation and editor/translator collapsing),
        which allows for advanced sorting. */
        if ($this->form == 'count') {
            return (int) count($resultNames);
        }

        return $text;
    }

    /**
     * @param  stdClass $nameItem
     * @param  int      $rank
     * @return string
     * @throws CiteProcException
     */
    private function formatName($nameItem, $rank)
    {
        $nameObj = $this->cloneNamePOSC($nameItem);

        $useInitials = $this->initialize && !is_null($this->initializeWith) && $this->initializeWith !== false;
        if ($useInitials && isset($nameItem->given)) {
            $nameObj->given = StringHelper::initializeBySpaceOrHyphen($nameItem->given, $this->initializeWith);
        }

        $renderedResult = $this->getNamesString($nameObj, $rank);
        CiteProcHelper::applyAdditionMarkupFunction($nameItem, $this->parent->getVariables()[0], $renderedResult);
        return trim($renderedResult);
    }

    /**
     * @param  stdClass $name
     * @param  int      $rank
     * @return string
     * @throws CiteProcException
     */
    private function getNamesString($name, $rank)
    {
        $text = "";

        if (!isset($name->family)) {
            return $text;
        }

        $text = $this->nameOrder($name, $rank);

        //contains nbsp prefixed by normal space or followed by normal space?
        $text = htmlentities($text);
        if (strpos($text, " &nbsp;") !== false || strpos($text, "&nbsp; ") !== false) {
            $text = preg_replace("/[\s]+/", "", $text); //remove normal spaces
            return preg_replace("/&nbsp;+/", " ", $text);
        }
        $text = html_entity_decode(preg_replace("/[\s]+/", " ", $text));
        return $this->format(trim($text));
    }

    /**
     * @param  stdClass $name
     * @return stdClass
     */
    private function cloneNamePOSC($name)
    {
        $nameObj = new stdClass();
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
     * @param  $data
     * @param  $text
     * @param  $resultNames
     * @return string
     */
    protected function appendEtAl($data, $text, $resultNames)
    {
        //append et al abbreviation
        if (count($data) > 1
            && !empty($resultNames)
            && !empty($this->etAl)
            && !empty($this->etAlMin)
            && !empty($this->etAlUseFirst)
            && count($data) != count($resultNames)
        ) {
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
        }
        return $text;
    }

    /**
     * @param  $resultNames
     * @return array
     */
    protected function prepareAbbreviation($resultNames)
    {
        $cnt = count($resultNames);
        /* Use of et-al-min and et-al-user-first enables et-al abbreviation. If the number of names in a name variable
        matches or exceeds the number set on et-al-min, the rendered name list is truncated after reaching the number of
        names set on et-al-use-first.  */

        if (isset($this->etAlMin) && isset($this->etAlUseFirst)) {
            if ($this->etAlMin <= $cnt) {
                if ($this->etAlUseLast && $this->etAlMin - $this->etAlUseFirst >= 2) {
                    /* et-al-use-last: When set to “true” (the default is “false”), name lists truncated by et-al
                    abbreviation are followed by the name delimiter, the ellipsis character, and the last name of the
                    original name list. This is only possible when the original name list has at least two more names
                    than the truncated name list (for this the value of et-al-use-first/et-al-subsequent-min must be at
                    least 2 less than the value of et-al-min/et-al-subsequent-use-first).*/

                    $lastName = array_pop($resultNames); //remove last Element and remember in $lastName
                }
                for ($i = $this->etAlUseFirst; $i < $cnt; ++$i) {
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

    /**
     * @param  $data
     * @param  stdClass $preceding
     * @return array
     * @throws CiteProcException
     */
    protected function renderSubsequentSubstitution($data, $preceding)
    {
        $resultNames = [];
        $subsequentSubstitution = CiteProc::getContext()->getCitationData()->getSubsequentAuthorSubstitute();
        $subsequentSubstitutionRule = CiteProc::getContext()->getCitationData()->getSubsequentAuthorSubstituteRule();

        /**
         * @var string $type
         * @var stdClass $name
         */
        foreach ($data as $rank => $name) {
            switch ($subsequentSubstitutionRule) {
                /* “partial-each” - when one or more rendered names in the name variable match those in the preceding
                bibliographic entry, the value of subsequent-author-substitute substitutes for each matching name.
                Matching starts with the first name, and continues up to the first mismatch. */
                case SubsequentAuthorSubstituteRule::PARTIAL_EACH:
                    if (NameHelper::precedingHasAuthor($preceding, $name)) {
                        $resultNames[] = $subsequentSubstitution;
                    } else {
                        $resultNames[] = $this->formatName($name, $rank);
                    }
                    break;
                 /* “partial-first” - as “partial-each”, but substitution is limited to the first name of the name
                variable. */
                case SubsequentAuthorSubstituteRule::PARTIAL_FIRST:
                    if ($rank === 0) {
                        if ($preceding->author[0]->family === $name->family) {
                            $resultNames[] = $subsequentSubstitution;
                        } else {
                            $resultNames[] = $this->formatName($name, $rank);
                        }
                    } else {
                        $resultNames[] = $this->formatName($name, $rank);
                    }
                    break;

                 /* “complete-each” - requires a complete match like “complete-all”, but now the value of
                subsequent-author-substitute substitutes for each rendered name. */
                case SubsequentAuthorSubstituteRule::COMPLETE_EACH:
                    try {
                        if (NameHelper::identicalAuthors($preceding, $data)) {
                            $resultNames[] = $subsequentSubstitution;
                        } else {
                            $resultNames[] = $this->formatName($name, $rank);
                        }
                    } catch (CiteProcException $e) {
                        $resultNames[] = $this->formatName($name, $rank);
                    }
                    break;
            }
        }
        return $resultNames;
    }

    /**
     * @param  array $data
     * @param  int   $citationNumber
     * @return array
     * @throws CiteProcException
     */
    private function handleSubsequentAuthorSubstitution($data, $citationNumber)
    {
        $hasPreceding = CiteProc::getContext()->getCitationData()->hasKey($citationNumber - 1);
        $subsequentSubstitution = CiteProc::getContext()->getCitationData()->getSubsequentAuthorSubstitute();
        $subsequentSubstitutionRule = CiteProc::getContext()->getCitationData()->getSubsequentAuthorSubstituteRule();
        $preceding = CiteProc::getContext()->getCitationData()->get($citationNumber - 1);


        if ($hasPreceding && !is_null($subsequentSubstitution) && !empty($subsequentSubstitutionRule)) {
            /**
             * @var stdClass $preceding
             */
            if ($subsequentSubstitutionRule == SubsequentAuthorSubstituteRule::COMPLETE_ALL) {
                try {
                    if (NameHelper::identicalAuthors($preceding, $data)) {
                        return [];
                    } else {
                        $resultNames = $this->getFormattedNames($data);
                    }
                } catch (CiteProcException $e) {
                    $resultNames = $this->getFormattedNames($data);
                }
            } else {
                $resultNames = $this->renderSubsequentSubstitution($data, $preceding);
            }
        } else {
            $resultNames = $this->getFormattedNames($data);
        }
        return $resultNames;
    }


    /**
     * @param  array $data
     * @return array
     * @throws CiteProcException
     */
    protected function getFormattedNames($data)
    {
        $resultNames = [];
        foreach ($data as $rank => $name) {
            $formatted = $this->formatName($name, $rank);
            $resultNames[] = NameHelper::addExtendedMarkup($this->variable, $name, $formatted);
        }
        return $resultNames;
    }

    /**
     * @param  $resultNames
     * @return string
     */
    protected function renderDelimiterPrecedesLastNever($resultNames)
    {
        $text = "";
        if (!$this->etAlUseLast) {
            if (count($resultNames) === 1) {
                $text = $resultNames[0];
            } elseif (count($resultNames) === 2) {
                $text = implode(" ", $resultNames);
            } else { // >2
                $lastName = array_pop($resultNames);
                $text = implode($this->delimiter, $resultNames)." ".$lastName;
            }
        }
        return $text;
    }

    /**
     * @param  $resultNames
     * @return string
     */
    protected function renderDelimiterPrecedesLastContextual($resultNames)
    {
        if (count($resultNames) === 1) {
            $text = $resultNames[0];
        } elseif (count($resultNames) === 2) {
            $text = implode(" ", $resultNames);
        } else {
            $text = implode($this->delimiter, $resultNames);
        }
        return $text;
    }

    /**
     * @param $resultNames
     */
    protected function addAnd(&$resultNames)
    {
        $count = count($resultNames);
        if (!empty($this->and) && $count > 1 && empty($this->etAl)) {
            $new = $this->and.' '.end($resultNames); // add and-prefix of the last name if "and" is defined
            // set prefixed last name at the last position of $resultNames array
            $resultNames[count($resultNames) - 1] = $new;
        }
    }

    /**
     * @param  $resultNames
     * @return array|string
     */
    protected function renderDelimiterPrecedesLast($resultNames)
    {
        $text = "";
        if (!empty($this->and) && empty($this->etAl)) {
            switch ($this->delimiterPrecedesLast) {
                case 'after-inverted-name':
                    //TODO: implement
                    break;
                case 'always':
                    $text = implode($this->delimiter, $resultNames);
                    break;
                case 'never':
                    $text = $this->renderDelimiterPrecedesLastNever($resultNames);
                    break;
                case 'contextual':
                default:
                    $text = $this->renderDelimiterPrecedesLastContextual($resultNames);
            }
        }
        return $text;
    }


    /**
     * @param stdClass $data
     * @param integer  $rank
     *
     * @return string
     * @throws CiteProcException
     */
    private function nameOrder($data, $rank)
    {
        $nameAsSortOrder = (($this->nameAsSortOrder === "first" && $rank === 0) || $this->nameAsSortOrder === "all");
        $demoteNonDroppingParticle = CiteProc::getContext()->getGlobalOptions()->getDemoteNonDroppingParticles();
        $normalizedName = NameHelper::normalizeName($data);
        if (StringHelper::isLatinString($normalizedName) || StringHelper::isCyrillicString($normalizedName)) {
            if ($this->form === "long"
                && $nameAsSortOrder
                && ((string) $demoteNonDroppingParticle === DemoteNonDroppingParticle::NEVER
                || (string) $demoteNonDroppingParticle === DemoteNonDroppingParticle::SORT_ONLY)
            ) {
                // [La] [Fontaine], [Jean] [de], [III]
                NameHelper::prependParticleTo($data, "family", "non-dropping-particle");
                NameHelper::appendParticleTo($data, "given", "dropping-particle");

                list($family, $given) = $this->renderNameParts($data);

                $text = $family.(!empty($given) ? $this->sortSeparator.$given : "");
                $text .= !empty($data->suffix) ? $this->sortSeparator.$data->suffix : "";
            } elseif ($this->form === "long"
                && $nameAsSortOrder
                && (is_null($demoteNonDroppingParticle)
                || (string) $demoteNonDroppingParticle === DemoteNonDroppingParticle::DISPLAY_AND_SORT)
            ) {
                // [Fontaine], [Jean] [de] [La], [III]
                NameHelper::appendParticleTo($data, "given", "dropping-particle");
                NameHelper::appendParticleTo($data, "given", "non-dropping-particle");
                list($family, $given) = $this->renderNameParts($data);
                $text = $family;
                $text .= !empty($given) ? $this->sortSeparator.$given : "";
                $text .= !empty($data->suffix) ? $this->sortSeparator.$data->suffix : "";
            } elseif ($this->form === "long" && $nameAsSortOrder && empty($demoteNonDroppingParticle)) {
                list($family, $given) = $this->renderNameParts($data);
                $text = $family;
                $text .= !empty($given) ? $this->delimiter.$given : "";
                $text .= !empty($data->suffix) ? $this->sortSeparator.$data->suffix : "";
            } elseif ($this->form === "short") {
                // [La] [Fontaine]
                NameHelper::prependParticleTo($data, "family", "non-dropping-particle");
                $text = $data->family;
            } else {// form "long" (default)
                // [Jean] [de] [La] [Fontaine] [III]
                NameHelper::prependParticleTo($data, "family", "non-dropping-particle");
                NameHelper::prependParticleTo($data, "family", "dropping-particle");
                NameHelper::appendParticleTo($data, "family", "suffix");
                list($family, $given) = $this->renderNameParts($data);
                $text = !empty($given) ? $given." ".$family : $family;
            }
        } elseif (StringHelper::isAsianString(NameHelper::normalizeName($data))) {
            $text = $this->form === "long" ? $data->family . $data->given : $data->family;
        } else {
            $text = $this->form === "long" ? $data->family . " " . $data->given : $data->family;
        }
        return $text;
    }

    /**
     * @param  $data
     * @return array
     */
    private function renderNameParts($data)
    {
        $given = "";
        if (array_key_exists("family", $this->nameParts)) {
            $family = $this->nameParts["family"]->render($data);
        } else {
            $family = $data->family;
        }
        if (isset($data->given)) {
            if (array_key_exists("given", $this->nameParts)) {
                $given = $this->nameParts["given"]->render($data);
            } else {
                $given = $data->given;
            }
        }
        return [$family, $given];
    }


    /**
     * @return string
     */
    public function getForm()
    {
        return $this->form;
    }

    /**
     * @return string
     */
    public function isNameAsSortOrder()
    {
        return $this->nameAsSortOrder;
    }

    /**
     * @return string
     */
    public function getDelimiter()
    {
        return $this->delimiter;
    }

    /**
     * @param mixed $delimiter
     */
    public function setDelimiter($delimiter)
    {
        $this->delimiter = $delimiter;
    }

    /**
     * @return Names
     */
    public function getParent()
    {
        return $this->parent;
    }
}
