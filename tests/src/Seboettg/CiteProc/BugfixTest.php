<?php
/*
 * citeproc-php
 *
 * @link        http://github.com/seboettg/citeproc-php for the source repository
 * @copyright   Copyright (c) 2017 Sebastian Böttger.
 * @license     https://opensource.org/licenses/MIT
 */

namespace Seboettg\CiteProc;

use PHPUnit\Framework\TestCase;

/**
 * Class BugfixTest
 * @package Seboettg\CiteProc
 * @author Sebastian Böttger <seboettg@gmail.com>
 */
class BugfixTest extends TestCase
{

    use TestSuiteTestCaseTrait;

    public function testBugfixGithub()
    {
        $this->_testRenderTestSuite("bugfix-github", ["bugfix-github-58"]);
    }

    /**
     * @throws Exception\CiteProcException
     */
    public function testBugfixGithub58()
    {
        $testFiles = loadFixtures("bugfix-github-58");
        $testData = json_decode(file_get_contents(PHPUNIT_FIXTURES."/$testFiles[0]"));
        $mode = $testData->mode;
        $citeProc = new CiteProc($testData->csl);
        $input = $testData->input;
        $result = $citeProc->render($input, $mode);
        $this->assertNotEmpty($result);
        $this->assertTrue(isset($input[0]->title), "Failed asserting that title property exists in input data");
    }
}