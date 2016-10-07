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
        $ret = "";
        $sorting = CiteProc::getContext()->getSorting();
        if (!empty($sorting)) {
            $sorting->sort($data);
        }

        if (CiteProc::getContext()->isModeBibliography()) {
            if (is_array($data)) {
                $arr = [];
                foreach ($data as $item) {
                    ++self::$numberOfCitedItems;
                    $arr[] = $this->wrapBibEntry($this->renderSingle($item));
                }
                $ret .= implode($this->delimiter, $arr);
            } else {
                $ret .= $this->wrapBibEntry($this->renderSingle($data));
            }

            return "<div class=\"csl-bib-body\">".$ret."\n</div>";

        } else if (CiteProc::getContext()->isModeCitation()) {
            if (is_array($data)) {
                $arr = [];
                foreach ($data as $item) {
                    if (CiteProc::getContext()->hasCitationItems()) {
                        continue;
                    }
                    $arr[] = $this->renderSingle($item);
                }
                $ret .= implode($this->delimiter, $arr);
            } else {
                $ret .= $this->renderSingle($data);
            }
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

    private function wrapBibEntry($value)
    {
        return "\n  <div class=\"csl-entry\">" . $value . "</div>";
    }

}