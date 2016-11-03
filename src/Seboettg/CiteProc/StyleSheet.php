<?php
/**
 * citeproc-php
 *
 * @link        http://github.com/seboettg/citeproc-php for the source repository
 * @copyright   Copyright (c) 2016 Sebastian Böttger.
 * @license     https://opensource.org/licenses/MIT
 */

namespace Seboettg\CiteProc;

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
        $stylesPath = __DIR__.'/../../../styles/';
        return file_get_contents($stylesPath.$styleName.'.csl');
    }

    /**
     * Loads xml formatted locales of given language key
     *
     * @param string $langKey e.g. "en-US", or "de-CH"
     * @return string
     */
    public static function loadLocales($langKey)
    {
        $localesPath = __DIR__.'/../../../locales/';
        return file_get_contents($localesPath."locales-".$langKey.'.xml');
    }
}