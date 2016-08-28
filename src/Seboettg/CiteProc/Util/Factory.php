<?php

namespace Seboettg\CiteProc\Util;
use Seboettg\CiteProc\Exception\CiteProcException;
use Seboettg\CiteProc\Exception\ClassNotFoundException;


/**
 * Class Factory
 * @package Seboettg\CiteProc\Util
 *
 * @author Sebastian Böttger <boettger@hebis.uni-frankfurt.de>
 */
class Factory
{
    const CITE_PROC_NODE_NAMESPACE = "Seboettg\\CiteProc\\Rendering";

    static $nodes = [

        'layout'        => "\\Layout",
        'text'          => "\\Text",
        "macro"         => "\\Macro",
        "number"        => "\\Number",
        "label"         => "\\Label",
        "group"         => "\\Group",
        "choose"        => "\\Choose\\Choose",
        "if"            => "\\Choose\\ChooseIf",
        "else-if"       => "\\Choose\\ChooseElseIf",
        "else"          => "\\Choose\\ChooseElse",
        'date'          => "\\Date\\Date",
        "date-part"     => "\\Date\\DatePart",
        "names"         => "\\Name\\Names",
        "name"          => "\\Name\\Name",
        "substitute"    => "\\Name\\Substitute",
        "et-al"         => "\\Name\\EtAl"
    ];

    public static function create($node)
    {
        $nodeClass = self::CITE_PROC_NODE_NAMESPACE . self::$nodes[$node->getName()];
        if (!class_exists($nodeClass)) {
            throw new ClassNotFoundException($nodeClass);
        }

        return new $nodeClass($node);
    }


    public static function loadLocale($lang) {
        $directory = __DIR__."/../../../../vendor/academicpuma/locales";
        $file = $directory . "/locales-" . ($lang) . ".xml";

        if (file_exists($file) == false) {
            throw new CiteProcException("Locale file \"locale-$file.xml\" does not exist!");
        }

        $content = file_get_contents($file);
        return new \SimpleXMLElement($content);
    }
}