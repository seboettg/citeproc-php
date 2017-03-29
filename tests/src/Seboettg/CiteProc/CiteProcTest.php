<?php
/*
 * citeproc-php
 *
 * @link        http://github.com/seboettg/citeproc-php for the source repository
 * @copyright   Copyright (c) 2016 Sebastian Böttger.
 * @license     https://opensource.org/licenses/MIT
 */

namespace Seboettg\CiteProc;


use PHPUnit_Framework_ExpectationFailedException;
use Seboettg\CiteProc\Exception\CiteProcException;

class CiteProcTest extends \PHPUnit_Framework_TestCase implements TestSuiteTests
{

    use TestSuiteTestCaseTrait;

    /**
     * @var array
     */
    private $dataObj;

    /**
     * @var CiteProc
     */
    private $citeProc;

    public function setUp()
    {
        //parent::setU
    }

    public function testFullStyleBibliography1()
    {
        $this->_testRenderTestSuite("fullstyles_APABibliography");
    }

    public function testFullStyleMultipleAuthors()
    {
        $this->_testRenderTestSuite("fullstyles_APA_MultipleAuthors");
    }

    public function testFullStyleDINBibliography()
    {
        $this->_testRenderTestSuite("fullstyles_DINBibliography");
    }

    public function testRenderTestSuite()
    {
        //$this->_testRenderTestSuite(".json", ['bugreports_', 'number_FailingDelimiters', 'number_LeadingZeros.json']);
    }


}
