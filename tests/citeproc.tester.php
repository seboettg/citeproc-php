<?php

require_once '../vendor/autoload.php';

use AcademicPuma\CiteProc\CiteProc;

const TEST_FOLDER = '../json/';

$dir_handle = opendir(TEST_FOLDER);
while (FALSE !== ($filename = readdir($dir_handle))) {
    if (!is_dir(TEST_FOLDER . $filename) && $filename[0] != '.') {
        $json_data = file_get_contents(TEST_FOLDER . $filename);
        //  $json_data = substr($json_data, strpos($json_data, '*/{')+2);
        $test_data = json_decode($json_data);
        switch (json_last_error()) {
            case JSON_ERROR_NONE:
                echo ' - No errors';
                break;
            case JSON_ERROR_DEPTH:
                echo ' - Maximum stack depth exceeded';
                break;
            case JSON_ERROR_STATE_MISMATCH:
                echo ' - Underflow or the modes mismatch';
                break;
            case JSON_ERROR_CTRL_CHAR:
                echo ' - Unexpected control character found';
                break;
            case JSON_ERROR_SYNTAX:
                echo ' - Syntax error, malformed JSON';
                break;
            case JSON_ERROR_UTF8:
                echo ' - Malformed UTF-8 characters, possibly incorrectly encoded';
                break;
            default:
                echo ' - Unknown error';
                break;
        }

        if ($test_data->mode == 'bibliography') {
            $citeproc = new CiteProc($test_data->csl);
            $input_data = (array) $test_data->input;
            $count = count($input_data);
            $output = '';
            foreach ($input_data as $data) {
                $output .= $citeproc->render($data, $test_data->mode);
            }
            //print '<html><body>';
            if ($output != $test_data->result) {
                print TEST_FOLDER . $filename . " FAILED\n";
                print $output . " !=  <br>\n" . $test_data->result . "<br><br>\n\n";
            } else {
                print TEST_FOLDER . $filename . " PASSED\n";
            }
        }
    }
}
//print '</body></html>';
//print($csl_parse);
