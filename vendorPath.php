<?php
/*
 * citeproc-php
 *
 * @link        http://github.com/seboettg/citeproc-php for the source repository
 * @copyright   Copyright (c) 2017 Sebastian Böttger.
 * @license     https://opensource.org/licenses/MIT
 */

if (!function_exists('vendorPath')) {
    /**
     * @return bool|string returns path of composer vendor folder, and false if folder does not exist
     */
    function vendorPath() {

        if (file_exists(__DIR__ . '/vendor/')) {
            return __DIR__ . '/vendor';
        } else {
            if (!file_exists(__DIR__ . '/../../')) {
                return false;
            } else {
                return __DIR__ . '/../..';
            }
        }
    }
}