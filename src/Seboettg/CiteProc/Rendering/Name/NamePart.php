<?php

namespace Seboettg\CiteProc\Rendering\Name;
use Seboettg\CiteProc\Styles\AffixesTrait;
use Seboettg\CiteProc\Styles\FormattingTrait;
use Seboettg\CiteProc\Styles\TextCaseTrait;

/**
 * Class NamePart
 *
 * The cs:name element may contain one or two cs:name-part child elements for name-part-specific formatting.
 * cs:name-part must carry the name attribute, set to either “given” or “family”.
 *
 * If set to “given”, formatting and text-case attributes on cs:name-part affect the “given” and “dropping-particle”
 * name-parts. affixes surround the “given” name-part, enclosing any demoted name particles for inverted names.
 *
 * If set to “family”, formatting and text-case attributes affect the “family” and “non-dropping-particle” name-parts.
 * affixes surround the “family” name-part, enclosing any preceding name particles, as well as the “suffix” name-part
 * for non-inverted names.
 *
 * The “suffix” name-part is not subject to name-part formatting. The use of cs:name-part elements does not influence
 * which, or in what order, name-parts are rendered.
 *
 *
 * @package Seboettg\CiteProc\Rendering\Name
 *
 * @author Sebastian Böttger <boettger@hebis.uni-frankfurt.de>
 */
class NamePart
{

    use FormattingTrait,
        TextCaseTrait,
        AffixesTrait;

    private $name;

    /**
     * @var Name
     */
    private $parent;

    /**
     * NamePart constructor.
     * @param \SimpleXMLElement $node
     */
    public function __construct(\SimpleXMLElement $node, $parent)
    {
        $this->parent = $parent;

        /** @var \SimpleXMLElement $attribute */
        foreach ($node->attributes() as $attribute) {
            if ($attribute->getName() === 'name') {
                $this->name = (string) $attribute;
            }
        }

        $this->initFormattingAttributes($node);
        $this->initTextCaseAttributes($node);
        $this->initAffixesAttributes($node);
    }

    public function render($data)
    {
        $ret = "";
        if (!$data->{$this->name}) {
            return "";
        }

        switch ($this->name) {

            /* If set to “given”, formatting and text-case attributes on cs:name-part affect the “given” and
            “dropping-particle” name-parts. affixes surround the “given” name-part, enclosing any demoted name particles
            for inverted names.*/
            case 'given':
                $given = $data->given;
                if (isset($data->{'dropping-particle'})) {
                    $given = " " . $data->{'dropping-particle'};
                }
                $ret = $this->format($this->applyTextCase($given));
                break;

            /* if name set to “family”, formatting and text-case attributes affect the “family” and
            “non-dropping-particle” name-parts. affixes surround the “family” name-part, enclosing any preceding name
            particles, as well as the “suffix” name-part for non-inverted names.*/
            case 'family':
                $family = $data->family;
                if (isset($data->{'non-dropping-particle'})) {
                    $family = $data->{'non-dropping-particle'} . " " . $family;
                }
                $ret = $this->format($this->applyTextCase($family));
                break;
        }

        return $this->addAffixes($ret);
    }

    public function getName()
    {
        return $this->name;
    }
}