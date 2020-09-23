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
use Seboettg\CiteProc\Exception\InvalidStylesheetException;
use Seboettg\CiteProc\RenderingState;
use Seboettg\CiteProc\Style\StyleElement;
use Seboettg\CiteProc\Styles\AffixesTrait;
use Seboettg\CiteProc\Styles\ConsecutivePunctuationCharacterTrait;
use Seboettg\CiteProc\Styles\DelimiterTrait;
use Seboettg\CiteProc\Styles\FormattingTrait;
use Seboettg\CiteProc\Util\CiteProcHelper;
use Seboettg\CiteProc\Util\Factory;
use Seboettg\CiteProc\Util\StringHelper;
use Seboettg\Collection\ArrayList;
use SimpleXMLElement;
use stdClass;

/**
 * Class Layout
 *
 * @package Seboettg\CiteProc\Rendering
 *
 * @author Sebastian Böttger <seboettg@gmail.com>
 */
class Layout implements Rendering
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
     *
     * @var string
     */
    private $delimiter = "";


    private $parent;

    /**
     * @param  SimpleXMLElement $node
     * @param  StyleElement     $parent
     * @throws InvalidStylesheetException
     */
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
     * @param  array|DataList  $data
     * @param  array|ArrayList $citationItems
     * @return string|array
     */
    public function render($data, $citationItems = [])
    {
        $ret = "";
        $sorting = CiteProc::getContext()->getSorting();
        if (!empty($sorting)) {
            CiteProc::getContext()->setRenderingState(new RenderingState("sorting"));
            $sorting->sort($data);
            CiteProc::getContext()->setRenderingState(new RenderingState("rendering"));
        }

        if (CiteProc::getContext()->isModeBibliography()) {
            foreach ($data as $citationNumber => $item) {
                ++self::$numberOfCitedItems;
                CiteProc::getContext()->getResults()->append(
                    $this->wrapBibEntry($item, $this->renderSingle($item, $citationNumber))
                );
            }
            $ret .= implode($this->delimiter, CiteProc::getContext()->getResults()->toArray());
            $ret = StringHelper::clearApostrophes($ret);
            return "<div class=\"csl-bib-body\">".$ret."\n</div>";
        } elseif (CiteProc::getContext()->isModeCitation()) {
            if ($citationItems->count() > 0) { //is there a filter for specific citations?
                if ($this->isGroupedCitations($citationItems)) { //if citation items grouped?
                    return $this->renderGroupedCitations($data, $citationItems);
                } else {
                    $data = $this->filterCitationItems($data, $citationItems);
                    $ret = $this->renderCitations($data, $ret);
                }
            } else {
                $ret = $this->renderCitations($data, $ret);
            }
        }
        $ret = StringHelper::clearApostrophes($ret);
        return $this->addAffixes($ret);
    }

    /**
     * @param  $data
     * @param  int|null $citationNumber
     * @return string
     */
    private function renderSingle($data, $citationNumber = null)
    {
        $bibliographyOptions = CiteProc::getContext()->getBibliographySpecificOptions();
        $inMargin = [];
        $margin = [];
        foreach ($this->children as $key => $child) {
            $rendered = $child->render($data, $citationNumber);
            $this->getChildsAffixesAndDelimiter($child);
            if (CiteProc::getContext()->isModeBibliography()
                && $bibliographyOptions->getSecondFieldAlign() === "flush"
            ) {
                if ($key === 0 && !empty($rendered)) {
                    $inMargin[] = $rendered;
                } else {
                    $margin[] = $rendered;
                }
            } else {
                $inMargin[] = $rendered;
            }
        }
        $inMargin = array_filter($inMargin);
        $margin = array_filter($margin);
        if (!empty($inMargin) && !empty($margin) && CiteProc::getContext()->isModeBibliography()) {
            $leftMargin = $this->removeConsecutiveChars($this->htmlentities($this->format(implode("", $inMargin))));
            $rightInline = $this->removeConsecutiveChars(
                $this->htmlentities($this->format(implode("", $margin))).
                $this->suffix
            );
            $res  = '<div class="csl-left-margin">' . trim($leftMargin) . '</div>';
            $res .= '<div class="csl-right-inline">' . trim($rightInline) . '</div>';
            return $res;
        } elseif (!empty($inMargin)) {
            $res = $this->format(implode("", $inMargin));
            return $this->htmlentities($this->removeConsecutiveChars($res));
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

    /**
     * @param  stdClass $dataItem
     * @param  string   $value
     * @return string
     */
    private function wrapBibEntry($dataItem, $value)
    {
        $value = $this->addAffixes($value);
        return "\n  ".
            "<div class=\"csl-entry\">" .
            $renderedItem = CiteProcHelper::applyAdditionMarkupFunction($dataItem, "csl-entry", $value) .
            "</div>";
    }

    /**
     * @param  string $text
     * @return string
     */
    private function htmlentities($text)
    {
        $text = preg_replace("/(.*)&([^#38|amp];.*)/u", "$1&#38;$2", $text);
        return $text;
    }

    /**
     * @param  $data
     * @param  $ret
     * @return string
     */
    private function renderCitations($data, $ret)
    {
        CiteProc::getContext()->getResults()->replace([]);
        foreach ($data as $citationNumber => $item) {
            $renderedItem = $this->renderSingle($item, $citationNumber);
            $renderedItem = CiteProcHelper::applyAdditionMarkupFunction($item, "csl-entry", $renderedItem);
            CiteProc::getContext()->getResults()->append($renderedItem);
            CiteProc::getContext()->appendCitedItem($item);
        }
        $ret .= implode($this->delimiter, CiteProc::getContext()->getResults()->toArray());
        return $ret;
    }

    /**
     * @param  DataList  $data
     * @param  ArrayList $citationItems
     * @return mixed
     */
    private function filterCitationItems($data, $citationItems)
    {
        $arr = $data->toArray();

        $arr_ = array_filter($arr, function ($dataItem) use ($citationItems) {
            foreach ($citationItems as $citationItem) {
                if ($dataItem->id === $citationItem->id) {
                    return true;
                }
            }
            return false;
        });

        return $data->replace($arr_);
    }

    /**
     * @param  ArrayList $citationItems
     * @return bool
     */
    private function isGroupedCitations(ArrayList $citationItems)
    {
        $firstItem = array_values($citationItems->toArray())[0];
        if (is_array($firstItem)) {
            return true;
        }
        return false;
    }

    /**
     * @param  DataList  $data
     * @param  ArrayList $citationItems
     * @return array|string
     */
    private function renderGroupedCitations($data, $citationItems)
    {
        $group = [];
        foreach ($citationItems as $citationItemGroup) {
            $data_ = $this->filterCitationItems(clone $data, $citationItemGroup);
            CiteProc::getContext()->setCitationData($data_);
            $group[] = $this->addAffixes(StringHelper::clearApostrophes($this->renderCitations($data_, "")));
        }
        if (CiteProc::getContext()->isCitationsAsArray()) {
            return $group;
        }
        return implode("\n", $group);
    }
}
