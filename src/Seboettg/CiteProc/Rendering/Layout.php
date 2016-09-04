<?php

namespace Seboettg\CiteProc\Rendering;

use Seboettg\CiteProc\CiteProc;
use Seboettg\CiteProc\Rendering\RenderingInterface;
use Seboettg\CiteProc\Styles\AffixesTrait;
use Seboettg\CiteProc\Styles\FormattingTrait;
use Seboettg\CiteProc\Styles\DelimiterTrait;
use Seboettg\CiteProc\Util\Factory;
use Seboettg\Collection\ArrayList;


/**
 * Class Layout
 * @package Seboettg\CiteProc\Rendering
 *
 * @author Sebastian Böttger <boettger@hebis.uni-frankfurt.de>
 */
class Layout implements RenderingInterface
{

    private static $numberOfCitedItems = 0;

    use AffixesTrait,
        FormattingTrait,
        DelimiterTrait;

    /**
     * @var ArrayList
     */
    private $children;

    public function __construct($node)
    {
        self::$numberOfCitedItems = 0;
        $this->children = new ArrayList();
        foreach ($node->children() as $child) {
            $this->children->append(Factory::create($child));
        }
        $this->initDelimiterAttributes($node);
        $this->initAffixesAttributes($node);
        $this->initFormattingAttributes($node);
    }

    public function render($data)
    {
        $sorting = CiteProc::getContext()->getSorting();
        if (!empty($sorting)) {
            $sorting->sort($data);
        }

        $ret = "";
        if (is_array($data)) {
            $arr = [];
            foreach ($data as $item) {
                ++self::$numberOfCitedItems;
                $arr[] = $this->renderSingle($item);
            }
            $ret = implode($this->delimiter, $arr);
        } else {
            $ret .= $this->renderSingle($data);
        }

        return $this->addAffixes($ret);

    }

    private function renderSingle($data)
    {
        $ret = "";
        /** @var RenderingInterface $child */
        foreach ($this->children as $child) {
            $ret .= $child->render($data);
        }

        return $this->format($ret);
    }

    /**
     * @return int
     */
    public static function getNumberOfCitedItems()
    {
        return self::$numberOfCitedItems;
    }

}