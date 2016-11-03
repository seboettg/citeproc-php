<?php
/**
 * citeproc-php
 *
 * @link        http://github.com/seboettg/citeproc-php for the source repository
 * @copyright   Copyright (c) 2016 Sebastian Böttger.
 * @license     https://opensource.org/licenses/MIT
 */

namespace Seboettg\CiteProc;
use Seboettg\CiteProc\Style\Bibliography;
use Seboettg\CiteProc\Style\Citation;
use Seboettg\CiteProc\Style\Macro;

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

    /**
     * @param $styleName
     * @deprecated
     */
    public static function loadStyleSheet($styleName)
    {
        return StyleSheet::loadStyleSheet($styleName);
    }


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
        self::$context = new Context();
        self::$context->setLocale(new Locale\Locale($lang)); //init locale
        $this->styleSheetXml = new \SimpleXMLElement($this->styleSheet);
        $this->parse($this->styleSheetXml);
    }

    /**
     * @param \SimpleXMLElement $style
     */
    private function parse(\SimpleXMLElement $style)
    {
        /** @var \SimpleXMLElement $node */
        foreach ($style as $node) {
            $name = $node->getName();
            switch ($name) {
                case 'info':
                    break;
                case 'locale':
                    self::$context->getLocale()->addXml($node);
                    break;
                case 'macro':
                    $macro = new Macro($node);
                    self::$context->addMacro($macro->getName(), $macro);
                    break;
                case 'bibliography':
                    $bibliography = new Bibliography($node);
                    self::$context->setBibliography($bibliography);
                    break;
                case 'citation':
                    $citation = new Citation($node);
                    self::$context->setCitation($citation);
                    break;
            }
        }
    }

    /**
     * @param string $data
     * @return string
     */
    public function bibliography($data = '')
    {
        return self::$context->getBibliography()->render($data);
    }

    /**
     * @param string $data
     * @return string
     */
    public function citation($data = '')
    {
        return self::$context->getCitation()->render($data);
    }

    public function render($data, $mode = "bibliography") {

        switch ($mode) {
            case 'bibliography':
                self::$context->setMode($mode);
                return $this->bibliography($data);
            case 'citation':
                self::$context->setMode($mode);
                return $this->citation($data);
            default:
                throw new \InvalidArgumentException("\"$mode\" is not a valid mode.");
        }
    }
}