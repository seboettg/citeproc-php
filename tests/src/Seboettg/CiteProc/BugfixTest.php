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
        $this->_testRenderTestSuite("bugfix-github");
    }
}