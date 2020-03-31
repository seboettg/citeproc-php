<?php
/*
 * citeproc-php
 *
 * @link        http://github.com/seboettg/citeproc-php for the source repository
 * @copyright   Copyright (c) 2017 Sebastian Böttger.
 * @license     https://opensource.org/licenses/MIT
 */

namespace Seboettg\CiteProc\Style\Options;

use SimpleXMLElement;

/**
 * Class GlobalOptionsTrait
 * @package Seboettg\CiteProc\Style
 * @author Sebastian Böttger <seboettg@gmail.com>
 */
class GlobalOptions
{
    /**
     * @var bool
     */
    private $initializeWithHyphen = true;

    /**
     * @var PageRangeFormats
     */
    private $pageRangeFormat;

    /**
     * @var DemoteNonDroppingParticle
     */
    private $demoteNonDroppingParticles;

    /**
     * GlobalOptions constructor.
     * @param SimpleXMLElement $node
     */
    public function __construct(SimpleXMLElement $node)
    {
        /** @var SimpleXMLElement $attribute */
        foreach ($node->attributes() as $attribute) {
            switch ($attribute->getName()) {
                case 'initialize-with-hyphen':
                    $this->initializeWithHyphen = "false" === (string) $attribute ? false : true;
                    break;
                case 'page-range-format':
                    $this->pageRangeFormat = new PageRangeFormats((string) $attribute);
                    break;
                case 'demote-non-dropping-particle':
                    $this->demoteNonDroppingParticles = new DemoteNonDroppingParticle((string) $attribute);
            }
        }
    }

    /**
     * Specifies whether compound given names (e.g. “Jean-Luc”) should be initialized with a hyphen (“J.-L.”, value
     * “true”, default) or without (“J.L.”, value “false”).
     * @return bool
     */
    public function isInitializeWithHyphen()
    {
        return $this->initializeWithHyphen;
    }

    /**
     * Activates expansion or collapsing of page ranges: “chicago” (“321–28”), “expanded” (e.g. “321–328”),
     * “minimal” (“321–8”), or “minimal-two” (“321–28”). Delimits page ranges
     * with the “page-range-delimiter” term (introduced with CSL 1.0.1, and defaults to an en-dash). If the attribute is
     * not set, page ranges are rendered without reformatting.
     * @return PageRangeFormats
     */
    public function getPageRangeFormat()
    {
        return $this->pageRangeFormat;
    }

    /**
     * Sets the display and sorting behavior of the non-dropping-particle in inverted names (e.g. “Koning, W. de”).
     * @return DemoteNonDroppingParticle
     */
    public function getDemoteNonDroppingParticles()
    {
        return $this->demoteNonDroppingParticles;
    }
}
