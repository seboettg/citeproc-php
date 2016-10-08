<?php

namespace Seboettg\CiteProc\Rendering\Date;

use Seboettg\CiteProc\CiteProc;
use Seboettg\CiteProc\Exception\CiteProcException;
use Seboettg\CiteProc\Styles\AffixesTrait;
use Seboettg\CiteProc\Styles\DisplayTrait;
use Seboettg\CiteProc\Styles\FormattingTrait;
use Seboettg\CiteProc\Styles\TextCaseTrait;
use Seboettg\CiteProc\Util;
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

    private $datePartsAttribute = "";

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
                case 'date-parts':
                    $this->datePartsAttribute = (string) $attribute;
            }
        }
        /** @var \SimpleXMLElement $child */
        foreach ($node->children() as $child) {
            if ($child->getName() === "date-part") {
                $datePartName = (string) $child->attributes()["name"];
                $this->dateParts->set($this->form."-".$datePartName, Util\Factory::create($child));
            }
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
        $var = null;
        if(isset($data->{$this->variable})) {
            $var = $data->{$this->variable};
        } else {
            return "";
        }

        if (!isset($data->{$this->variable}->{'date-parts'}) || empty($data->{$this->variable}->{'date-parts'})) {
            if (isset($data->{$this->variable}->raw) && !empty($data->{$this->variable}->raw)) {
                try {
                    $var->{'date-parts'} = Util\Date::parseDateParts($data->{$this->variable});
                } catch (CiteProcException $e) {
                    return "";
                }
            } else {
                return "";
            }
        }

        // date parts from locales
        $dateFromLocale = CiteProc::getContext()->getLocale()->getDateXml();

        // no custom date parts within the date element (this)?
        if ($this->dateParts->count() <= 0 && !empty($dateFromLocale["date"])) {
            //if exist, add date parts from locales

            $datePartsXml = $dateFromLocale["date"];

            //filter dateParts by form
            $form = $this->form;
            $dateForm = array_filter($datePartsXml, function($element) use ($form){
                /** @var \SimpleXMLElement $element */
                $dateForm = (string) $element->attributes()["form"];
                return  $dateForm === $form;
            });

            //has dateForm from locale children (date-part elements)?
            $localeDate = array_pop($dateForm);
            if ($localeDate instanceof \SimpleXMLElement && $localeDate->count() > 0) {
                //add only date parts defined in date-parts attribute of (this) date element
                $dateParts = explode("-", $this->datePartsAttribute);

                /** @var \SimpleXMLElement $child */
                foreach ($localeDate->children() as $child) {
                    if ($child->getName() === "date-part") {
                        $datePartName = (string) $child->attributes()["name"];
                        if (in_array($datePartName, $dateParts)) {
                            $this->dateParts->set("$form-$datePartName", Util\Factory::create($child));
                        }
                    }
                }
            }
        }


        if ($this->dateParts->count() > 0) {
            // ignore empty date-parts
            if (!isset($var->{'date-parts'})) {
                return "";
            }
            /** @var DatePart $datePart */
            foreach ($this->dateParts as $datePart) {
                $ret .= $datePart->render($var->{'date-parts'}, $this);
            }
        }
        // fallback:
        // When there are no dateParts children, but date-parts attribute in date
        // render numeric
        else if (!empty($this->datePartsAttribute)) {

            $ret = $this->renderNumeric($var->{'date-parts'});

        }

        return !empty($ret) ? $this->addAffixes($this->format($this->applyTextCase($ret))) : "";
    }

    private function renderNumeric($date)
    {
        $date = $date[0];
        $str = "";
        $dateParts = explode("-", $this->datePartsAttribute);

        if (in_array("year", $dateParts)) {
            $str .= $date[0];
        }
        if (in_array("month", $dateParts)) {
            $str = $date[1] . "/$str";
        }
        if (in_array("day", $dateParts)) {
            $str = $date[2] . "/$str";
        }

        return $str;
    }

    public function getForm()
    {
        return $this->form;
    }

}