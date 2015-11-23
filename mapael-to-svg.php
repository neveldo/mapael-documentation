<?php

/**
 * Tool that allows users to generate a SVG file from a mapael JS map
 */

// Template of the SVG file
$svgTemplate = <<<EOD
<?xml version="1.0" encoding="utf-8"?>
<svg version="1.1" id="%name%" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" width="%width%px" height="%height%px" viewBox="0 0 %width% %height%">
%paths%
</svg>
EOD;



if (count($_FILES) > 0) {

    $jsSent = isset($_FILES['js'])
            && '.js' === substr($_FILES['js']['name'], -3, 3)
            && ('application/octet-stream' === $_FILES['js']['type']
                    || 'application/x-javascript' === $_FILES['js']['type']
                    || 'application/javascript' === $_FILES['js']['type']
            );

    $jsContent = $jsSent ? file_get_contents($_FILES['js']['tmp_name']) : '';

    $replaces = [
            '%width%' => '',
            '%height%' => '',
            '%name%' => '',
            '%paths%' => '',
    ];

    // Parse the relevant parts of the JS file
    preg_match('/width[\'"]?\s*:\s*[\'"]?([0-9]+\.?[0-9]*)/s', $jsContent, $matches);
    if (count($matches) > 0) {
        $replaces['%width%'] = $matches[1];
    }

    preg_match('/height[\'"]?\s*:\s*[\'"]?([0-9]+\.?[0-9]*)/s', $jsContent, $matches);
    if (count($matches) > 0) {
        $replaces['%height%'] = $matches[1];
    }

    preg_match('/maps[\'"]?\s*:\s*{\s*[\'"]?([a-zA-Z0-9-_]+)/s', $jsContent, $matches);
    if (count($matches) > 0) {
        $replaces['%name%'] = $matches[1];
    }

    $paths = null;
    preg_match('/elems[\'"]?\s*:\s*{(.*)}/sU', $jsContent, $matches);
    if (count($matches) > 0) {
        $paths = json_decode('{' . $matches[1] . '}', true);
    }

    if (is_array($paths) && count($paths) > 0) {
        foreach($paths as $id => $data) {
            $replaces['%paths%'] .= '<path id="' . $id . '" fill-rule="evenodd" clip-rule="evenodd" fill="#E0E0E0" d="' . $data . '" />'  . PHP_EOL;
        }
    }

    $svgContent = strtr($svgTemplate, $replaces);

    // Send proper headers to return the SVG file to the user
    header('Content-Description: File Transfer');
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename='.$replaces['%name%'].'.svg');
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');

    echo $svgContent;

    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Mapael to SVG - jQuery Mapael</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="jQuery Mapael is a jQuery plugin based on raphael.js that allows you to display dynamic vector maps. ">

    <link href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.1/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/css/mapael.css" rel="stylesheet">
    <link type="text/css" rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/SyntaxHighlighter/3.0.83/styles/shCoreDefault.min.css"/>
</head>

<body id="top">

<div class="navbar navbar-inverse navbar-fixed-top">
    <div class="navbar-inner">
        <div class="container">
            <h1><a href="#top">jQuery Mapael</a></h1>
            <ul class="nav">
                <li><a href="/mapael/#overview">Overview</a></li>
                <li><a href="/mapael/#examples">Examples</a></li>
                <li><a href="/mapael/#api-reference">Documentation</a></li>
                <li><a href="https://github.com/neveldo/mapael-maps" target="_blank">Additional maps</a></li>
                <li><a href="create-map.php">Create your own map</a></li>
                <li><a href="https://twitter.com/VincentBroute" target="_blank"><img src="assets/img/twitter_logo.png" width="18px" height="15px" style="vertical-align:top;" /> Twitter</a></li>
                <li><a href="https://github.com/neveldo/jQuery-Mapael" target="_blank"><img src="assets/img/github_logo.png" width="15px" height="15px" style="vertical-align:top;" /> Github</a></li>
            </ul>
        </div>
    </div>
</div>
<div class="container" style="max-width:700px;">
    <h2 style="margin-bottom:10px;text-align:center;">Mapael To SVG</h2>
    <p>This tool aims to generate an SVG file from the jQuery Mapael map file.</p>
    <form role="form" action="mapael-to-svg.php" method="post" enctype="multipart/form-data">
        <div class="form-group">
            <label for="svg">Mapael map (.js)</label>
            <input type="file" name="js" id="js">
        </div>
        <button type="submit" class="btn btn-default">Generate SVG file</button>
    </form>
</div>


<script type="text/javascript" src="http://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
<script type="text/javascript" src="http://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.1/js/bootstrap.min.js"></script>

<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/SyntaxHighlighter/3.0.83/scripts/shCore.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/SyntaxHighlighter/3.0.83/scripts/shBrushJScript.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/SyntaxHighlighter/3.0.83/scripts/shBrushXml.min.js"></script>
<script type="text/javascript" src="/mapael/assets/js/main.js"></script>

<script type="text/javascript">
    (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
                (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
            m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
    })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

    ga('create', 'UA-42216777-2', 'auto');
    ga('send', 'pageview');
</script>

</body>
</html>