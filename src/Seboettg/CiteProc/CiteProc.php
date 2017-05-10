<?php
/*
 * citeproc-php
 *
 * @link        http://github.com/seboettg/citeproc-php for the source repository
 * @copyright   Copyright (c) 2016 Sebastian Böttger.
 * @license     https://opensource.org/licenses/MIT
 */

namespace Seboettg\CiteProc;

use Seboettg\CiteProc\Data\DataList;
use Seboettg\CiteProc\Exception\CiteProcException;
use Seboettg\CiteProc\Root\Info;
use Seboettg\CiteProc\Style\Bibliography;
use Seboettg\CiteProc\Style\Citation;
use Seboettg\CiteProc\Style\Macro;
use Seboettg\CiteProc\Style\Options\GlobalOptions;
use Seboettg\CiteProc\Root\Root;
use Seboettg\CiteProc\Styles\Css\CssStyle;


/**
 * Class CiteProc
 * @package Seboettg\CiteProc
 *
 * @author Sebastian Böttger <seboettg@gmail.com>
 */
class CiteProc
{

    /**
     * @var Context
     */
    private static $context;


    /**
     * @return Context
     */
    public static function getContext()
    {
        return self::$context;
    }

    /**
     * @param Context $context
     */
    public static function setContext($context)
    {
        self::$context = $context;
    }

    private $lang;

    /**
     * @var string
     */
    private $styleSheet;

    /**
     * @var \SimpleXMLElement
     */
    private $styleSheetXml;

    /**
     * CiteProc constructor.
     * @param string $styleSheet xml formatted csl stylesheet
     */
    public function __construct($styleSheet, $lang = "en-US")
    {
        $this->styleSheet = $styleSheet;
        $this->lang = $lang;
    }

    public function __destruct()
    {
        self::$context = null;
    }

    /**
     * @param \SimpleXMLElement $style
     */
    private function parse(\SimpleXMLElement $style)
    {
        $root = new Root();
        $root->initInheritableNameAttributes($style);
        self::$context->setRoot($root);
        $globalOptions = new GlobalOptions($style);
        self::$context->setGlobalOptions($globalOptions);

        /** @var \SimpleXMLElement $node */
        foreach ($style as $node) {
            $name = $node->getName();
            switch ($name) {
                case 'info':
                    self::$context->setInfo(new Info($node));
                    break;
                case 'locale':
                    self::$context->getLocale()->addXml($node);
                    break;
                case 'macro':
                    $macro = new Macro($node, $root);
                    self::$context->addMacro($macro->getName(), $macro);
                    break;
                case 'bibliography':
                    $bibliography = new Bibliography($node, $root);
                    self::$context->setBibliography($bibliography);
                    break;
                case 'citation':
                    $citation = new Citation($node, $root);
                    self::$context->setCitation($citation);
                    break;
            }
        }
    }

    /**
     * @param DataList $data
     * @return string
     */
    protected function bibliography($data)
    {

        return self::$context->getBibliography()->render($data);
    }

    /**
     * @param DataList $data
     * @return string
     */
    protected function citation($data)
    {
        return self::$context->getCitation()->render($data);
    }

    /**
     * @param array|DataList $data
     * @param string $mode (citation|bibliography)
     * @return string
     * @throws CiteProcException
     */
    public function render($data, $mode = "bibliography")
    {

        if (!in_array($mode, ['citation', 'bibliography'])) {
            throw new \InvalidArgumentException("\"$mode\" is not a valid mode.");
        }

        $this->init(); //initialize

        $res = "";

        if (is_array($data)) {
            $data = new DataList($data);
        } else if (!($data instanceof DataList)) {
            throw new CiteProcException('No valid format for variable data. Either DataList or array expected');
        }

        // set CitationItems to Context
        self::getContext()->setCitationItems($data);

        switch ($mode) {
            case 'bibliography':
                self::$context->setMode($mode);
                $res = $this->bibliography($data);
                break;
            case 'citation':
                self::$context->setMode($mode);
                $res = $this->citation($data);
        }
        self::setContext(null);

        return $res;
    }

    /**
     * initializes CiteProc and start parsing XML stylesheet
     */
    public function init()
    {
        self::$context = new Context($this);
        self::$context->setLocale(new Locale\Locale($this->lang)); //init locale
        $this->styleSheetXml = new \SimpleXMLElement($this->styleSheet);
        $this->parse($this->styleSheetXml);
    }

    /**
     * @return string
     */
    public function renderCssStyles()
    {
        if (self::getContext() === null) {
            $this->init();
        }

        if (self::getContext()->getCssStyle() == null) {
            $cssStyle = new CssStyle(self::getContext()->getBibliographySpecificOptions());
            self::getContext()->setCssStyle($cssStyle);
        }

        return self::getContext()->getCssStyle()->render();
    }
}