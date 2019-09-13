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
        $this->_testRenderTestSuite("bugfix-github", ["bugfix-github-58", "bugfix-github-date"]);
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

    /**
     * @throws Exception\CiteProcException
     */
    public function testBugfixGitub59()
    {
        $style = "modern-language-association";
        $input = '[{"type": "book","accessed": {"date-parts": [["2016","01","01"]]},"publisher": "lol2","title": "lol"},{"type": "book","author": [{"given": "Daniel","suffix": "H.","family": "Nexon"},{"given": "Iver","suffix": "B.","family": "Neumann"}],"accessed": {"date-parts": [["2006","01","01"]]},"publisher": "Rowman & Littlefield","title": "Harry Potter and International Relations"}]';
        $citeProc = new CiteProc(StyleSheet::loadStyleSheet($style));
        $data = json_decode($input);
        $datum = $data[0];
        $this->assertNotTrue(isset($datum->author)); //first entry has no author
        $result = $citeProc->render($data);
        $this->assertNotEmpty($result);
    }

    public function testBugfixGithubDate()
    {
        $this->_testRenderTestSuite("bugfix-github-date");
    }
}