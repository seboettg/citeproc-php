<?php
/*
 * citeproc-php
 *
 * @link        http://github.com/seboettg/citeproc-php for the source repository
 * @copyright   Copyright (c) 2016 Sebastian Böttger.
 * @license     https://opensource.org/licenses/MIT
 */
date_default_timezone_set('Europe/Berlin');
require_once realpath(__DIR__ . '/../vendor').'/autoload.php';

define('PHPUNIT_FIXTURES', realpath(__DIR__ . '/fixtures/basic-tests/processor-tests/machines'));
putenv('XDEBUG_CONFIG="PHPSTORM"');
$loader = new Composer\Autoload\ClassLoader();
$loader->add('Seboettg', realpath(__DIR__ . '/src'));
$loader->register();

/**
 * @param string $filter
 * @param array|null $ignore
 * @return array
 */
function loadFixtures($filter, $ignore = null)
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

    if (!empty($ignore)) {
        return array_filter($files, function($value) use ($ignore) {
            foreach ($ignore as $filter) {
                if (strpos($value, $filter) !== false) {
                    return false;
                }
            }
            return true;
        });
    }

    return $files;
}