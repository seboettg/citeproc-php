<?php
/*
 * citeproc-php
 *
 * @link        http://github.com/seboettg/citeproc-php for the source repository
 * @copyright   Copyright (c) 2016 Sebastian Böttger.
 * @license     https://opensource.org/licenses/MIT
 */

namespace Seboettg\CiteProc;

use Seboettg\CiteProc\Exception\CiteProcException;

/**
 * Class StyleSheet
 *
 * Helper class for loading CSL styles and CSL locales
 *
 * @package Seboettg\CiteProc
 * @author Sebastian Böttger <seboettg@gmail.com>
 */
class StyleSheet
{

    /**
     * Loads xml formatted CSL stylesheet of a given stylesheet name, e.g. "american-physiological-society" for
     * apa style.
     *
     * See in styles folder (which is included as git submodule) for all available style sheets
     *
     * @param string $styleName e.g. "american-physiological-society" for apa
     * @return string
     */
    public static function loadStyleSheet($styleName)
    {
        $stylesPath = self::vendorPath() . "/citation-style-language/styles-distribution/";
        return file_get_contents($stylesPath . $styleName . '.csl');
    }

    /**
     * Loads xml formatted locales of given language key
     *
     * @param string $langKey e.g. "en-US", or "de-CH"
     * @return string
     */
    public static function loadLocales($langKey)
    {
        $localesPath = self::vendorPath() . "/citation-style-language/locales/";
        return file_get_contents($localesPath . "locales-" . $langKey . '.xml');
    }

    /**
     * @return bool|string
     * @throws CiteProcException
     */
    private static function vendorPath()
    {
        include_once __DIR__ . '/../../../vendorPath.php';
        if (!($vendorPath = vendorPath())) {
            // @codeCoverageIgnoreStart
            throw new CiteProcException('vendor path not found. Use composer to initialize your project');
            // @codeCoverageIgnoreEnd
        }
        return $vendorPath;
    }
}