<?php
include './CiteProc.php';

$dir_handle = opendir('./tests');
while (FALSE !== ($filename = readdir($dir_handle))) {
  if ( !is_dir('./tests/' . $filename) && $filename[0] != '.') {
    $json_data = file_get_contents('./tests/' . $filename);
    $json_data = substr($json_data, strpos($json_data, '*/{')+2);
    $test_data = json_decode($json_data);
    if ($test_data->mode != 'bibliography') {
      $citeproc = new citeproc($test_data->csl);
      $input_data  = (array)$test_data->input;
      $count =  count($input_data);
      $output = '';
      foreach($input_data as $data) {
        $output .= $citeproc->render($data, $test_data->mode);
      }
      //print '<html><body>';
      if ($output != $test_data->result) {
        print './tests/' . $filename . " FAILED\n";
        print $output . " !=  <br>\n" . $test_data->result ."<br><br>\n\n";
      }
      else {
        print './tests/' . $filename . " PASSED\n";
      }
    }
  }
}
//print '</body></html>';
//print($csl_parse);
