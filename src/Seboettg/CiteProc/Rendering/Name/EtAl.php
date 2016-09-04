<?php

namespace Seboettg\CiteProc\Rendering\Name;
use Seboettg\CiteProc\CiteProc;
use Seboettg\CiteProc\Rendering\RenderingInterface;
use Seboettg\CiteProc\Styles\FormattingTrait;


/**
 * Class EtAl
 * Et-al abbreviation, controlled via the et-al-... attributes (see Name), can be further customized with the optional
 * cs:et-al element, which must follow the cs:name element (if present). The term attribute may be set to either “et-al”
 * (the default) or to “and others” to use either term. The formatting attributes may also be used, for example to
 * italicize the “et-al” term.
 *
 * @package Seboettg\CiteProc\Rendering\Name
 *
 * @author Sebastian Böttger <boettger@hebis.uni-frankfurt.de>
 */
class EtAl implements RenderingInterface
{
    use FormattingTrait;

    private $term;

    public function __construct(\SimpleXMLElement $node)
    {
        /** @var \SimpleXMLElement $attribute */
        foreach ($node->attributes() as $attribute) {
            switch ($attribute->getName()) {
                case 'term':
                    $this->term = (string) $attribute;
                    break;
            }
        }
        $this->initFormattingAttributes($node);
    }

    public function render($data) {
        return $this->format(CiteProc::getContext()->getLocale()->filter('terms', $this->term)->single);
    }
}