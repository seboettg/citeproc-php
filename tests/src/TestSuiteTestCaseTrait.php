<?php
/*
 * citeproc-php
 *
 * @link        http://github.com/seboettg/citeproc-php for the source repository
 * @copyright   Copyright (c) 2016 Sebastian Böttger.
 * @license     https://opensource.org/licenses/MIT
 */

namespace Seboettg\CiteProc;

use Exception;
use PHPUnit\Framework\ExpectationFailedException;
use RuntimeException;
use Seboettg\CiteProc\Exception\CiteProcException;

trait TestSuiteTestCaseTrait
{

    /** @var array $FILTER */
    private static $FILTER = [
    ];


    /**
     * @param $filterTests
     * @param null $ignore
     */
    public function runTestSuite($filterTests, $ignore = null)
    {
        $testFiles = loadFixtures($filterTests, $ignore);
        $i = 0;
        $failures = [];
        $success = [];
        $exceptions = [];
        foreach ($testFiles as $testFile) {
            if (in_array($testFile, self::$FILTER)) {
                continue; //stop testing filtered tests
            }

            $testData = json_decode(file_get_contents(PHPUNIT_FIXTURES."/$testFile"));
            $mode = $testData->mode;
            if ($mode !== "bibliography" && $mode !== "citation") {
                continue;
            }

            $expected = $testData->result;
            $citeProc = new CiteProc($testData->csl);
            ++$i;
            $echo = sprintf("%03d (%s / %s): ", $i, $testFile, $mode);
            try {
                $citationItems = [];
                if (!empty($testData->citation_items)) {
                    if (array_key_exists(0, $testData->citation_items) && is_array($testData->citation_items[0])) {
                        // use only the first array
                        $citationItems =  $testData->citation_items[0];
                    } else {
                        $citationItems = $testData->citation_items;
                    }
                }
                $actual = $citeProc->render($testData->input, $mode, $citationItems);
                $this->assertEquals($expected, $actual, "Test case \"$testFile\" ($i) has failed.");
                //echo "succeeded.\n\n\n";
                $success[] = $echo . "\n$actual";
            } catch (ExpectationFailedException $e) {
                echo "failed\n";
                $str = $e->getMessage() . "\n" . $e->getComparisonFailure()->getDiff() . "\n\n\n";
                $failures[] = "$echo\n$str";
                echo $actual;
                echo $str;
            } catch (CiteProcException $e) {
                //$failures[] =
                $exceptions[] = "$echo\nCiteProc Exception in $testFile\n".$e->getFile()."\n".$e->getMessage()."\n";
            } catch (RuntimeException $e) {
                //$failures[] = $e->getMessage();
                $exceptions[] = sprintf(
                    "%s\nRuntime Exception in %s\n%s on line %s\n%s\n",
                    $echo,
                    $testFile,
                    $e->getFile(),
                    $e->getLine(),
                    $e->getMessage()
                );
            } catch (Exception $e) {
                $exceptions[] = sprintf(
                    "%s\nException in %s\n%s on line %s\n%s\n",
                    $echo,
                    $testFile,
                    $e->getFile(),
                    $e->getLine(),
                    $e->getMessage()
                );
            }
        }
        if (!empty($success)) {
            print count($success)." assertions were succeeded:\n\t".implode("\n\t", $success);
        }

        if (!empty($failures)) {
            print "\n\n".count($failures)." assertions failed:\n\t".implode("\n\t", $failures);
            throw new ExpectationFailedException(count($failures)." assertions failed:\n\t".implode("\n\t", $failures));
        }

        if (!empty($exceptions)) {
            print "\n\n".count($exceptions)." assertions have caused an exception :\n\t".implode("\n\t", $exceptions);
        }

        print "\n\nSummary: ".count($success)."/".count($failures)."/".count($exceptions);
        print "\n++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++\n\n";
    }
}
