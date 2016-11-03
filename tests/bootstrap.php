<?php
/**
 * citeproc-php
 *
 * @link        http://github.com/seboettg/citeproc-php for the source repository
 * @copyright   Copyright (c) 2016 Sebastian Böttger.
 * @license     https://opensource.org/licenses/MIT
 */

require_once realpath(__DIR__ . '/../vendor/autoload.php');

define('PHPUNIT_FIXTURES', realpath(__DIR__ . '/fixtures/test-suite/processor-tests/machines'));

$loader = new Composer\Autoload\ClassLoader();
$loader->add('Seboettg', realpath(__DIR__ . '/src'));
$loader->register();

/**
 * @param string $filter
 * @return array
 */
function loadFixtures($filter)
{
    $files = [];
    if ($handle = opendir(PHPUNIT_FIXTURES)) {
        /**  @var string $entry */
        while (false !== ($entry = readdir($handle))) {
            if ($entry != "." && $entry != ".." && strpos($entry, $filter) !== false) {
                $files[] = $entry;
            }
        }
        closedir($handle);
    }
    return $files;
}