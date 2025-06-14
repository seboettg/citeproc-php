<?php
declare(strict_types=1);
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
     * @throws CiteProcException
     */
    public static function loadStyleSheet(string $stylesFile): string
    {
        // default encoding for multi byte, maybe useful on some old systems 
        mb_internal_encoding("UTF-8");
        // style name to find in vendor/
        if (substr($stylesFile, -4 ) !== ".csl") {
            $stylesFile = self::vendorPath() . "/citation-style-language/styles/$stylesFile.csl";
        }
        // absolute path
        return self::readFileContentsOrThrowException($stylesFile);
    }

    /**
     * Loads xml formatted locales of given language key
     *
     * @param string $langKey e.g. "en-US", or "de-CH"
     * @return string
     * @throws CiteProcException
     */
    public static function loadLocales(string $langKey): string
    {
        $localesPath = self::vendorPath()."/citation-style-language/locales";
        $localeFile = "$localesPath/locales-{$langKey}.xml";
        if (file_exists($localeFile)) {
            return self::readFileContentsOrThrowException($localeFile);
        } else {
            $metadata = self::loadLocalesMetadata();
            if (!empty($metadata->{'primary-dialects'}->{$langKey})) {
                return self::readFileContentsOrThrowException(
                    sprintf("%s/locales-%s.xml", $localesPath, $metadata->{'primary-dialects'}->{$langKey})
                );
            }
        }
        throw new CiteProcException("No Locale file found for $langKey");
    }

    /**
     * @throws CiteProcException
     */
    private static function readFileContentsOrThrowException($path): string
    {
        $fileContent = file_get_contents($path);
        if (false === $fileContent) {
            throw new CiteProcException("Couldn't read $path");
        }
        return $fileContent;
    }

    /**
     * @return mixed
     * @throws CiteProcException
     */
    public static function loadLocalesMetadata()
    {
        $localesMetadataPath = self::vendorPath() . "/citation-style-language/locales/locales.json";
        return json_decode(self::readFileContentsOrThrowException($localesMetadataPath));
    }

    /**
     * @return bool|string
     * @throws CiteProcException
     */
    private static function vendorPath()
    {
        include_once realpath(__DIR__ . '/../') . '/vendorPath.php';
        if (!($vendorPath = vendorPath())) {
            throw new CiteProcException('vendor path not found. Use composer to initialize your project');
        }
        return $vendorPath;
    }
}
