<?php
/**
 * citeproc-php
 *
 * @link        http://github.com/seboettg/citeproc-php for the source repository
 * @copyright   Copyright (c) 2016 Sebastian Böttger.
 * @license     https://opensource.org/licenses/MIT
 */

namespace Seboettg\CiteProc\Rendering\Name;
use Seboettg\CiteProc\Exception\CiteProcException;
use Seboettg\CiteProc\Rendering\Layout;
use Seboettg\CiteProc\Rendering\RenderingInterface;
use Seboettg\CiteProc\Util\Factory;
use Seboettg\Collection\ArrayList;


/**
 * Class Substitute
 * @package Seboettg\CiteProc\Rendering\Name
 *
 * @author Sebastian Böttger <seboettg@gmail.com>
 */
class Substitute implements RenderingInterface
{

    /**
     * @var ArrayList
     */
    private $children;

    private $variable;

    public function __construct(\SimpleXMLElement $node)
    {
        $this->children = new ArrayList();
        foreach ($node->children() as $child) {
            $object = Factory::create($child);
            /*
            if (! $object instanceof  || $object instanceof Layout::class) {
                throw new CiteProcException( get_class($object) . " is not a valid rendering object");
            }
            */
            $this->children->append($object);
        }
    }

    /**
     * @param $data
     * @return string
     */
    public function render($data)
    {
        $str = "";
        /** @var RenderingInterface $child */
        foreach ($this->children as $child) {
            $str .= $child->render($data);
        }
        return $str;
    }

    /**
     * @return mixed
     */
    public function getVariable()
    {
        return $this->variable;
    }

    /**
     * @param mixed $variable
     */
    public function setVariable($variable)
    {
        $this->variable = $variable;
    }
}