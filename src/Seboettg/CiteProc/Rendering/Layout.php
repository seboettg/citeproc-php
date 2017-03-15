<?php
/*
 * citeproc-php
 *
 * @link        http://github.com/seboettg/citeproc-php for the source repository
 * @copyright   Copyright (c) 2016 Sebastian Böttger.
 * @license     https://opensource.org/licenses/MIT
 */

namespace Seboettg\CiteProc\Rendering;

use Seboettg\CiteProc\CiteProc;
use Seboettg\CiteProc\Data\DataList;
use Seboettg\CiteProc\Rendering\RenderingInterface;
use Seboettg\CiteProc\Styles\AffixesTrait;
use Seboettg\CiteProc\Styles\ConsecutivePunctuationCharacterTrait;
use Seboettg\CiteProc\Styles\FormattingTrait;
use Seboettg\CiteProc\Styles\DelimiterTrait;
use Seboettg\CiteProc\Util\Factory;
use Seboettg\Collection\ArrayList;


/**
 * Class Layout
 * @package Seboettg\CiteProc\Rendering
 *
 * @author Sebastian Böttger <seboettg@gmail.com>
 */
class Layout implements RenderingInterface
{

    private static $numberOfCitedItems = 0;

    use AffixesTrait,
        FormattingTrait,
        DelimiterTrait,
        ConsecutivePunctuationCharacterTrait;

    /**
     * @var ArrayList
     */
    private $children;

    /**
     * When used within cs:citation, the delimiter attribute may be used to specify a delimiter for cites within a
     * citation.
     * @var string
     */
    private $delimiter = "";


    private $parent;

    public function __construct($node, $parent)
    {
        $this->parent = $parent;
        self::$numberOfCitedItems = 0;
        $this->children = new ArrayList();
        foreach ($node->children() as $child) {
            $this->children->append(Factory::create($child, $this));
        }
        $this->initDelimiterAttributes($node);
        $this->initAffixesAttributes($node);
        $this->initFormattingAttributes($node);
    }

    /**
     * @param array|DataList $data
     * @param int|null $citationNumber
     * @return string
     */
    public function render($data, $citationNumber = null)
    {
        $ret = "";
        $sorting = CiteProc::getContext()->getSorting();
        if (!empty($sorting)) {
            $sorting->sort($data);
        }

        if (CiteProc::getContext()->isModeBibliography()) {
            if ($data instanceof DataList) {
                foreach ($data as $citationNumber => $item) {
                    ++self::$numberOfCitedItems;
                    CiteProc::getContext()->getResults()->append($this->wrapBibEntry($this->renderSingle($item, $citationNumber)));
                }
                $ret .= implode($this->delimiter, CiteProc::getContext()->getResults()->toArray());
            } else {
                $ret .= $this->wrapBibEntry($this->renderSingle($data, $citationNumber));
            }

            return "<div class=\"csl-bib-body\">".$ret."\n</div>";

        } else if (CiteProc::getContext()->isModeCitation()) {
            if (is_array($data) || $data instanceof DataList) {
                foreach ($data as $citationNumber => $item) {
                    CiteProc::getContext()->getResults()->append($this->renderSingle($item, $citationNumber));
                }
                $ret .= implode($this->delimiter, CiteProc::getContext()->getResults()->toArray());
            } else {
                $ret .= $this->renderSingle($data, $citationNumber);
            }
        }

        return $this->addAffixes($ret);
    }

    private function renderSingle($data, $citationNumber = null)
    {
        $ret = [];
        /** @var RenderingInterface $child */
        foreach ($this->children as $child) {
            $rendered = $child->render($data, $citationNumber);
            $this->getChildsAffixesAndDelimiter($child);
            if (!empty($rendered)) {
                $ret[] = $rendered;
            }
        }

        if (!empty($ret)) {
            $res = $this->format(implode($this->delimiter, $ret));
            return $this->removeConsecutiveChars($res);
        }
        return "";
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