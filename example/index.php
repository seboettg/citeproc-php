<?php
/**
 * citeproc-php
 *
 * @link        http://github.com/seboettg/citeproc-php for the source repository
 * @copyright   Copyright (c) 2017 Sebastian Böttger.
 * @license     https://opensource.org/licenses/MIT
 */


include __DIR__ . "/../vendor/autoload.php";
use Seboettg\CiteProc\StyleSheet;
use Seboettg\CiteProc\CiteProc;

$data = file_get_contents("data.json");
$style = StyleSheet::loadStyleSheet("apa");
$citeProc = new CiteProc($style);
$bibliography = $citeProc->render(json_decode($data), "bibliography");
$cssStyles = $citeProc->renderCssStyles();
?>
<html>
<head>
    <title>CSL Test</title>
    <style type="text/css" rel="stylesheet">
        <?php echo $cssStyles; ?>
    </style>
</head>
<body>
<h1>Bibliography</h1>
<?php echo $bibliography; ?>
</body>
</html>