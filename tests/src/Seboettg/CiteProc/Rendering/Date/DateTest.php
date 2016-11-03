<?php
/**
 * citeproc-php
 *
 * @link        http://github.com/seboettg/citeproc-php for the source repository
 * @copyright   Copyright (c) 2016 Sebastian Böttger.
 * @license     https://opensource.org/licenses/MIT
 */

namespace Seboettg\CiteProc\Rendering\Date;


use PHPUnit_Framework_ExpectationFailedException;
use Seboettg\CiteProc\CiteProc;
use Seboettg\CiteProc\Context;
use Seboettg\CiteProc\Locale\Locale;
use Seboettg\CiteProc\TestSuiteTestCaseTrait;
use Seboettg\CiteProc\TestSuiteTests;

class DateTest extends \PHPUnit_Framework_TestCase implements TestSuiteTests
{

    use TestSuiteTestCaseTrait;

    public function setUp()
    {
        parent::setUp();
        $context = new Context();
        $context->setLocale(new Locale("en-GB"));
        CiteProc::setContext($context);
    }

    private $data = "{\"id\": \"ITEM-2\", \"issued\": {\"date-parts\": [[\"1983\", \"1\", \"15\"]]}, \"title\": \"Item 2\", \"type\": \"book\"}";

    public function testRenderDateParts()
    {
        $xml = "
              <date variable=\"issued\" form=\"numeric-leading-zeros\">
                <date-part prefix=\" \" suffix=\".\" name=\"day\"/>
                <date-part suffix=\".\" name=\"month\"/>
                <date-part name=\"year\"/>
              </date>";

        $date = new Date(new \SimpleXMLElement($xml));
        $ret = $date->render(json_decode($this->data));

        $this->assertEquals(" 15.01.1983", $ret);

    }

    public function testRenderTestSuite()
    {
        $this->_testRenderTestSuite('date_');

    }
}
