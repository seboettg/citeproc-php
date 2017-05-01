<?php
/*
 * citeproc-php
 *
 * @link        http://github.com/seboettg/citeproc-php for the source repository
 * @copyright   Copyright (c) 2016 Sebastian Böttger.
 * @license     https://opensource.org/licenses/MIT
 */

namespace Seboettg\CiteProc\Rendering;

use Seboettg\CiteProc\Styles\AffixesTrait;
use Seboettg\CiteProc\Styles\ConsecutivePunctuationCharacterTrait;
use Seboettg\CiteProc\Styles\DelimiterTrait;
use Seboettg\CiteProc\Styles\DisplayTrait;
use Seboettg\CiteProc\Styles\FormattingTrait;
use Seboettg\CiteProc\Util\Factory;
use Seboettg\Collection\ArrayList;


/**
 * Class Group
 * The cs:group rendering element must contain one or more rendering elements (with the exception of cs:layout).
 * cs:group may carry the delimiter attribute to separate its child elements, as well as affixes and display attributes
 * (applied to the output of the group as a whole) and formatting attributes (transmitted to the enclosed elements).
 * cs:group implicitly acts as a conditional: cs:group and its child elements are suppressed if a) at least one
 * rendering element in cs:group calls a variable (either directly or via a macro), and b) all variables that are
 * called are empty. This accommodates descriptive cs:text elements.
 *
 * @package Seboettg\CiteProc\Rendering
 *
 * @author Sebastian Böttger <seboettg@gmail.com>
 */
class Group implements Rendering, HasParent
{
    use DelimiterTrait,
        AffixesTrait,
        DisplayTrait,
        FormattingTrait,
        ConsecutivePunctuationCharacterTrait;

    const CLASS_PATH = 'Seboettg\CiteProc\Rendering';

    /**
     * @var ArrayList
     */
    private $children;

    /**
     * cs:group may carry the delimiter attribute to separate its child elements
     * @var
     */
    private $delimiter = "";

    private $parent;

    /**
     * @var array
     */
    private $renderedChildsWithVariable = [];


    public function __construct(\SimpleXMLElement $node, $parent)
    {
        $this->parent = $parent;
        $this->children = new ArrayList();
        foreach ($node->children() as $child) {
            $this->children->append(Factory::create($child, $this));
        }
        $this->initDisplayAttributes($node);
        $this->initAffixesAttributes($node);
        $this->initDelimiterAttributes($node);
        $this->initFormattingAttributes($node);
    }

    /**
     * @param $data
     * @param int|null $citationNumber
     * @return string
     */
    public function render($data, $citationNumber = null)
    {
        $textParts = array();
        $terms = $variables = $haveVariables = $elementCount = 0;
        foreach ($this->children as $child) {
            $elementCount++;

            if (($child instanceof Text) &&
                ($child->getSource() == 'term' ||
                    $child->getSource() == 'value')) {
                ++$terms;
            }

            if (($child instanceof Label)) {
                ++$terms;
            }
            if (method_exists($child, "getSource") && $child->getSource() == 'variable' &&
                !empty($child->getVariable()) && $child->getVariable() != "date" &&
                !empty($data->{$child->getVariable()})
            ) {
                ++$variables;
            }

            $text = $child->render($data, $citationNumber);
            $delimiter = $this->delimiter;
            if (!empty($text)) {
                if ($delimiter && ($elementCount < count($this->children))) {
                    //check to see if the delimiter is already the last character of the text string
                    //if so, remove it so we don't have two of them when the group will be merged
                    $stext = strip_tags(trim($text));
                    if ((strrpos($stext, $delimiter[0]) + 1) == strlen($stext) && strlen($stext) > 1) {
                        $text = str_replace($stext, '----REPLACE----', $text);
                        $stext = substr($stext, 0, -1);
                        $text = str_replace('----REPLACE----', $stext, $text);
                    }
                }
                $textParts[] = $text;

                if (method_exists($child, "getSource") && $child->getSource() == 'variable' ||
                   (method_exists($child, "getVariable") && $child->getVariable() != "date" && !empty($child->getVariable()))) {

                    $haveVariables++;
                }

                if (method_exists($child, "getSource") && $child->getSource() == 'macro') {
                    $haveVariables++;
                }
            }
        }
        return $this->formatting($textParts, $variables, $haveVariables, $terms);
    }

    /**
     * @return mixed
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * @param $textParts
     * @param $variables
     * @param $haveVariables
     * @param $terms
     * @return string
     */
    protected function formatting($textParts, $variables, $haveVariables, $terms)
    {
        if (empty($textParts)) {
            return "";
        }

        if ($variables && !$haveVariables) {
            return ""; // there has to be at least one other none empty value before the term is output
        }

        if (count($textParts) == $terms) {
            return ""; // there has to be at least one other none empty value before the term is output
        }

        $text = implode($this->delimiter, $textParts); // insert the delimiter if supplied.
        if (!empty($text)) {
            return $this->wrapDisplayBlock($this->addAffixes($this->format(($text))));
        }

        return "";
    }
}
