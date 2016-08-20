<?php

namespace Seboettg\CiteProc\Util;
use Seboettg\CiteProc\Exception\CiteProcException;


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

        'text'      => "\\Text",
        "macro"     => "\\Macro",
        'date'      => "\\Date",
        "number"    => "\\Number",
        "names"     => "\\Names",
        "label"     => "\\Label",
        "group"     => "\\Group",
        "choose"    => "\\Choose\\Choose",
        "if"        => "\\Choose\\ChooseIf",
        "else-if"   => "\\Choose\\ChooseElseIf",
        "else"      => "\\Choose\\ChooseElse",

    ];

    public static function create($node)
    {
        $nodeClass = self::CITE_PROC_NODE_NAMESPACE . self::$nodes[$node->getName()];
        if (!class_exists($nodeClass)) {
            //TODO: throw ex
            throw new CiteProcException("Class \"$nodeClass\" does not exist");
        }

        return new $nodeClass($node);
    }

}