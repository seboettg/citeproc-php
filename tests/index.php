<?php

require_once '../vendor/autoload.php';

use academicpuma\citeproc\php\CSLUtils;
use academicpuma\citeproc\php\CiteProc;

$publications = array();

function init() {
    global $publications;
    $pubs_folder = dirname('.') . CSLUtils::PUBLICATIONS_FOLDER;  //\academicpuma\citeproc\php\CSLUtils::PUBLICATIONS_FOLDER;
        
        if ($handle = opendir($pubs_folder)) {
            while (false !== ($file = readdir($handle))) {
                if (!is_dir($pubs_folder . "/$file") && $file[0] != '.') {
                    $json_data = file_get_contents($pubs_folder . "/$file");
                    $publications[str_replace('.json', '', $file)] = json_decode($json_data);
                    if(JSON_ERROR_NONE !== json_last_error()) {
                        throw new Exception("json error");
                    }
                }
            }
            closedir($handle);
        }
    }
    
function render() {
    global $publications;   
    
    echo "<dl>";
    
    foreach($publications as $key => $pub) {
        echo "<dt>$key</dt>";
        echo "<dd><ul>";
        foreach(CSLUtils::$styles as $styleName) {
            $cslFilename = dirname('..').CSLUtils::STYLES_FOLDER.$styleName.".csl";

            $csl = file_get_contents($cslFilename);
            $citeProc = new CiteProc($csl);

            //$actual = preg_replace("!(\s{2,})!","",strip_tags($citeProc->render($pub)));
            $actual = $citeProc->render($pub);
            echo "<li>$styleName:<br />$actual</li>";
                
                //$expected = file_get_contents($key.'_'.$styleName.'.html');
                //$this->assertSame("", $actual);
        }
        echo "</ul></dd>";
    }
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
       "http://www.w3.org/TR/html4/loose.dtd">
<html lang="de">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    
</head>
<body>
<?php
    init();
    render();
?>
</body>
</html>