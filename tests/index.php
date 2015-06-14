<?php

require_once '../vendor/autoload.php';


use AcademicPuma\CiteProc\CiteProc;

$publications = array();

function init() {
    global $publications;
    //$pubs_folder = dirname('.') . CSLUtils::PUBLICATIONS_FOLDER;  //\academicpuma\citeproc\CSLUtils::PUBLICATIONS_FOLDER;
        
    $file = file_get_contents("data.json");
    
    $publications = json_decode($file);
    
    
}
    
function render() {
    global $publications;   
    
    echo "<dl>";
    
    foreach($publications as $dataId => $dataObject) {
        echo "<dt><h3>$dataId</h3></dt>";
        echo "<dd><ul>";

        foreach($dataObject->rendereddata as $styleName => $renderedText) {
            $style = CiteProc::loadStyleSheet($styleName);
            print_r($style);
            $lang = substr($publications->{$dataId}->locales, 0, 2);
            $citeProc = new CiteProc($style, $lang);

            $actual = preg_replace('!(\s{2,})!', ' ', strip_tags($citeProc->render($dataObject->rawdata)));

            echo '<li><h4>'.$styleName.':</h4>'
                    . '<div id="'.$dataId.'-'.$styleName.'" data-pub-ident="'.$dataId.'" data-style="'.$styleName.'">'
                    . '<strong>rendered:</strong><br />'
                    . '<div class="actual">'.$actual.'</div>'
                    . '<strong>expected:</strong><br />'
                    . '<div class="expected"></div>'
                    . '<strong>diff:</strong><br />'
                    . '<div class="diff"></div>'
                    . '</div></li>';
            
        }
        echo "</ul></dd>";
    }
}

init();

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
       "http://www.w3.org/TR/html4/loose.dtd">
<html lang="de">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <style type="text/css">
        body {
            font-family: "Helvetica Neue",Helvetica,Arial,sans-serif;
        }
        dl {
            font-size: 12px;
        }
    </style>
    <script type="text/javascript" src="//code.jquery.com/jquery-1.11.2.min.js"></script>
    <script type="text/javascript" src="diff_match_patch.js"></script>
    <script type="text/javascript">
        //<![CDATA[
           
            $(function() {
                $.getJSON('data.json', function(data) {
                    
                    $.each(data, function(dataId, val) {
                        
                        $.each(val.rendereddata, function(styleId, expectedText) {
                            console.log(dataId+"-"+styleId);
                            $("#"+dataId+"-"+styleId + " .expected").text(expectedText);
                            
                            var dmp = new diff_match_patch();
                            var actualText = $("#"+dataId+"-"+styleId + " .actual").text();
                            var d = dmp.diff_main(actualText, expectedText);
                            dmp.diff_cleanupSemantic(d);
                            dmp.diff_cleanupEfficiency(d);
                            var ds = dmp.diff_prettyHtml(d);
                            $("#"+dataId+"-"+styleId + " .diff").html(ds);
                        });
                    });
                });
            });
            

        //]]>
    </script>
        
</head>
<body>
<?php
    render();
?>
</body>
</html>