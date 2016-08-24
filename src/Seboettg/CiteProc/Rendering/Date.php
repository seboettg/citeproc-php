<?php

namespace Seboettg\CiteProc\Rendering;
use Seboettg\CiteProc\CiteProc;
use Seboettg\CiteProc\Rendering\Date\DatePart;
use Seboettg\CiteProc\Styles\AffixesTrait;
use Seboettg\CiteProc\Styles\DisplayTrait;
use Seboettg\CiteProc\Styles\FormattingTrait;
use Seboettg\CiteProc\Styles\TextCaseTrait;
use Seboettg\CiteProc\Util\Factory;
use Seboettg\Collection\ArrayList;


/**
 * Class Date
 * @package Seboettg\CiteProc\Rendering
 *
 * @author Sebastian Böttger <boettger@hebis.uni-frankfurt.de>
 */
class Date
{
    use AffixesTrait,
        DisplayTrait,
        FormattingTrait,
        TextCaseTrait;

    /**
     * @var ArrayList
     */
    private $dateParts;

    /**
     * @var string
     */
    private $form = "";

    /**
     * @var string
     */
    private $variable = "";

    public function __construct(\SimpleXMLElement $node)
    {
        $this->dateParts = new ArrayList();

        /** @var \SimpleXMLElement $attribute */
        foreach ($node->attributes() as $attribute) {
            switch ($attribute->getName()) {
                case 'form':
                    $this->form = (string) $attribute;
                    break;
                case 'variable':
                    $this->variable = (string) $attribute;
                    break;
            }
        }

        foreach ($node->children() as $child) {
            $this->dateParts->append(Factory::create($child));
        }

        $this->initAffixesAttributes($node);
        $this->initDisplayAttributes($node);
        $this->initFormattingAttributes($node);
        $this->initTextCaseAttributes($node);
    }

    /**
     * @param $data
     * @return string
     */
    public function render($data)
    {
        $ret = "";
        /** @var DatePart $datePart */
        foreach ($this->dateParts as $datePart) {
            $var = isset($data->{$this->variable}) ? $data->{$this->variable} : new \stdClass();
            $ret .= $datePart->render($var->{'date-parts'}, $this->form);
        }
        return $ret;
    }
}