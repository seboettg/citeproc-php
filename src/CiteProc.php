<?php
/*
 * citeproc-php
 *
 * @link        http://github.com/seboettg/citeproc-php for the source repository
 * @copyright   Copyright (c) 2016 Sebastian Böttger.
 * @license     https://opensource.org/licenses/MIT
 */

namespace Seboettg\CiteProc;

use InvalidArgumentException;
use Seboettg\CiteProc\Data\DataList;
use Seboettg\CiteProc\Exception\CiteProcException;
use Seboettg\CiteProc\Root\Info;
use Seboettg\CiteProc\Style\Bibliography;
use Seboettg\CiteProc\Style\Citation;
use Seboettg\CiteProc\Style\Macro;
use Seboettg\CiteProc\Style\Options\GlobalOptions;
use Seboettg\CiteProc\Root\Root;
use Seboettg\CiteProc\Styles\Css\CssStyle;
use Seboettg\CiteProc\Util\CiteProcHelper;
use Seboettg\Collection\ArrayList;
use SimpleXMLElement;

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
     * @var SimpleXMLElement
     */
    private $styleSheetXml;

    /**
     * @var array
     */
    private $markupExtension;

    /**
     * CiteProc constructor.
     * @param string $styleSheet xml formatted csl stylesheet
     * @param string $lang
     * @param array $markupExtension
     */
    public function __construct($styleSheet, $lang = "en-US", $markupExtension = [])
    {
        $this->styleSheet = $styleSheet;
        $this->lang = $lang;
        $this->markupExtension = $markupExtension;
    }

    public function __destruct()
    {
        self::$context = null;
    }

    /**
     * @param SimpleXMLElement $style
     * @throws CiteProcException
     */
    private function parse(SimpleXMLElement $style)
    {
        $root = new Root();
        $root->initInheritableNameAttributes($style);
        self::$context->setRoot($root);
        $globalOptions = new GlobalOptions($style);
        self::$context->setGlobalOptions($globalOptions);

        /** @var SimpleXMLElement $node */
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
     * @param ArrayList $citationItems
     * @return string
     */
    protected function citation($data, $citationItems)
    {
        return self::$context->getCitation()->render($data, $citationItems);
    }

    /**
     * @param array|DataList $data
     * @param string $mode (citation|bibliography)
     * @param array $citationItems
     * @param bool $citationAsArray
     * @return string
     * @throws CiteProcException
     */
    public function render($data, $mode = "bibliography", $citationItems = [], $citationAsArray = false)
    {
        if (is_array($data)) {
            $data = CiteProcHelper::cloneArray($data);
        }

        if (!in_array($mode, ['citation', 'bibliography'])) {
            throw new InvalidArgumentException("\"$mode\" is not a valid mode.");
        }

        $this->init($citationAsArray); //initialize

        $res = "";

        if (is_array($data)) {
            $data = new DataList(...$data);
        } elseif (!($data instanceof DataList)) {
            throw new CiteProcException('No valid format for variable data. Either DataList or array expected');
        }

        switch ($mode) {
            case 'bibliography':
                self::$context->setMode($mode);
                // set CitationItems to Context
                self::getContext()->setCitationData($data);
                $res = $this->bibliography($data);
                break;
            case 'citation':
                if (is_array($citationItems)) {
                    $citationItems = new ArrayList(...$citationItems);
                } elseif (!($citationItems instanceof ArrayList)) {
                    throw new CiteProcException('No valid format for variable `citationItems`, ArrayList expected.');
                }
                self::$context->setMode($mode);
                // set CitationItems to Context
                self::getContext()->setCitationItems($citationItems);
                $res = $this->citation($data, $citationItems);
        }
        self::setContext(null);

        return $res;
    }

    /**
     * initializes CiteProc and start parsing XML stylesheet
     * @param bool $citationAsArray
     * @throws CiteProcException
     */
    public function init($citationAsArray = false)
    {
        self::$context = new Context($this);
        self::$context->setLocale(new Locale\Locale($this->lang)); //init locale
        self::$context->setCitationsAsArray($citationAsArray);
        // set markup extensions
        self::$context->setMarkupExtension($this->markupExtension);
        $this->styleSheetXml = new SimpleXMLElement($this->styleSheet);
        $this->parse($this->styleSheetXml);
    }

    /**
     * @return string
     * @throws CiteProcException
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
