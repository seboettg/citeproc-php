<?php
/**
 * citeproc-php
 *
 * @link        http://github.com/seboettg/citeproc-php for the source repository
 * @copyright   Copyright (c) 2016 Sebastian Böttger.
 * @license     https://opensource.org/licenses/MIT
 */

namespace Seboettg\CiteProc\Rendering;
use Seboettg\CiteProc\Styles\AffixesTrait;
use Seboettg\CiteProc\Styles\DelimiterTrait;
use Seboettg\CiteProc\Styles\DisplayTrait;
use Seboettg\CiteProc\Util\Factory;
use Seboettg\Collection\ArrayList;


/**
 * Class Group
 * @package Seboettg\CiteProc\Rendering
 *
 * @author Sebastian Böttger <seboettg@gmail.com>
 */
class Group implements RenderingInterface
{
    use DelimiterTrait,
        AffixesTrait,
        DisplayTrait;

    private $children;

    /**
     * cs:group may carry the delimiter attribute to separate its child elements
     * @var
     */
    private $delimiter = "";

    public function __construct(\SimpleXMLElement $node)
    {
        $this->children = new ArrayList();
        foreach ($node->children() as $child) {
            $this->children->append(Factory::create($child));
        }
        $this->initDisplayAttributes($node);
        $this->initAffixesAttributes($node);
        $this->initDelimiterAttributes($node);
    }

    /**
     * @param \stdClass $data
     * @param int|null $citationNumber
     * @return string
     */
    public function render($data, $citationNumber = null)
    {
        $arr = [];
        /** @var RenderingInterface $child */
        foreach ($this->children as $child) {
            $res = $child->render($data, $citationNumber);
            if (!empty($res)) {
                $arr[] = $res;
            }
        }
        return $this->wrapDisplayBlock($this->addAffixes(implode($this->delimiter, $arr)));
    }
}