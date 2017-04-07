<?php
/*
 * citeproc-php
 *
 * @link        http://github.com/seboettg/citeproc-php for the source repository
 * @copyright   Copyright (c) 2016 Sebastian Böttger.
 * @license     https://opensource.org/licenses/MIT
 */

namespace Seboettg\CiteProc\Util;
use Seboettg\CiteProc\Exception\CiteProcException;
use Seboettg\CiteProc\Exception\ClassNotFoundException;


/**
 * Class Factory
 * @package Seboettg\CiteProc\Util
 *
 * @author Sebastian Böttger <seboettg@gmail.com>
 */
class Factory
{
    const CITE_PROC_NODE_NAMESPACE = "Seboettg\\CiteProc\\Rendering";

    /**
     * @var array
     */
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
        "name-part"     => "\\Name\\NamePart",
        "substitute"    => "\\Name\\Substitute",
        "et-al"         => "\\Name\\EtAl"
    ];

    /**
     * @param \SimpleXMLElement $node
     * @param mixed $param
     * @return mixed
     * @throws ClassNotFoundException
     */
    public static function create($node, $param = null)
    {
        $nodeClass = self::CITE_PROC_NODE_NAMESPACE . self::$nodes[$node->getName()];
        if (!class_exists($nodeClass)) {
            throw new ClassNotFoundException($nodeClass);
        }
        if ($param != null) {
            return new $nodeClass($node, $param);
        }
        return new $nodeClass($node);
    }
}