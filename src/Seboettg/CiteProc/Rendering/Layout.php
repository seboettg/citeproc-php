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
use Seboettg\CiteProc\Context;
use Seboettg\CiteProc\Data\DataList;
use Seboettg\CiteProc\RenderingState;
use Seboettg\CiteProc\Styles\AffixesTrait;
use Seboettg\CiteProc\Styles\ConsecutivePunctuationCharacterTrait;
use Seboettg\CiteProc\Styles\FormattingTrait;
use Seboettg\CiteProc\Styles\DelimiterTrait;
use Seboettg\CiteProc\Util\Factory;
use Seboettg\CiteProc\Util\StringHelper;
use Seboettg\Collection\ArrayList;


/**
 * Class Layout
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
     * @var string
     */
    private $delimiter = "";


    private $parent;

    /**
     * @param \Seboettg\CiteProc\Style\StyleElement $parent
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
     * @param array|DataList $data
     * @param int|null $citationNumber
     * @return string
     */
    public function render($data, $citationNumber = null)
    {
        $ret = "";
        $sorting = CiteProc::getContext()->getSorting();
        if (!empty($sorting)) {
            CiteProc::getContext()->setRenderingState(new RenderingState("sorting"));
            $sorting->sort($data);
            CiteProc::getContext()->setRenderingState(new RenderingState("rendering"));
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
            $ret = StringHelper::clearApostrophes($ret);
            return "<div class=\"csl-bib-body\">" . $ret . "\n</div>";

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
        $ret = StringHelper::clearApostrophes($ret);
        return $this->addAffixes($ret);
    }

    /**
     * @param $data
     * @param int|null $citationNumber
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
            if (CiteProc::getContext()->isModeBibliography() && $bibliographyOptions->getSecondFieldAlign() === "flush") {

                if ($key === 0 && !empty($rendered)) {
                    $inMargin[] = $rendered;
                } else {
                    $margin[] = $rendered;
                }
            } else {
                $inMargin[] = $rendered;
            }
        }


        if (!empty($inMargin) && !empty($margin) && CiteProc::getContext()->isModeBibliography()) {
            $leftMargin = $this->removeConsecutiveChars($this->htmlentities($this->format(implode("", $inMargin))));
            $rightInline = $this->removeConsecutiveChars($this->htmlentities($this->format(implode("", $margin))) . $this->suffix);
            $res  = '<div class="csl-left-margin">' . $leftMargin . '</div>';
            $res .= '<div class="csl-right-inline">' . $rightInline . '</div>';
            return $res;
        } else if (!empty($inMargin)) {
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
     * @param string $value
     * @return string
     */
    private function wrapBibEntry($value)
    {
        return "\n  <div class=\"csl-entry\">" . $this->addAffixes($value) . "</div>";
    }

    /**
     * @param string $text
     * @return string
     */
    private function htmlentities($text)
    {
        $text = preg_replace("/(.*)&([^#38;|amp;].*)/u", "$1&#38;$2", $text);
        return $text;
    }

}