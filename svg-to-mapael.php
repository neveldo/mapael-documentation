<?php

/**
 * Tool that allows users to generate a mapael map from a SVG file
 */

// Map template
$template = <<<EOD
/*!
 *
 * Jquery Mapael - Dynamic maps jQuery plugin (based on raphael.js)
 * Requires jQuery and Mapael >=2.0.0
 *
 * Map of %name%
 * 
 * @author %author%
 */
(function (factory) {
    if (typeof exports === 'object') {
        // CommonJS
        module.exports = factory(require('jquery'), require('jquery-mapael'));
    } else if (typeof define === 'function' && define.amd) {
        // AMD. Register as an anonymous module.
        define(['jquery', 'mapael'], factory);
    } else {
        // Browser globals
        factory(jQuery, jQuery.mapael);
    }
}(function ($, Mapael) {

    "use strict";
    
    $.extend(true, Mapael,
        {
            maps :  {
                %js_name% : {
                    width : %width%,
                    height : %height%,
                    getCoords : function (lat, lon) {
                        // todo
                        return {"x" : long, "y" : lat};
                    },
                    'elems': {
%areas%
                    }
                }
            }
        }
    );

    return Mapael;

}));
EOD;

/**
 * Slugify a string
 * @param $text
 * @return string
 */
function slugify($text)
{
    $text = preg_replace('~[^\\pL\d]+~u', '_', $text);
    $text = trim($text, '_');
    $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
    $text = strtolower($text);
    $text = preg_replace('~[^_\w]+~', '', $text);

    if (empty($text)) {
        return '';
    }

    return $text;
}

if (count($_POST) > 0) {

    $svgContentSent = isset($_FILES['svg'])
            && '.svg' === substr($_FILES['svg']['name'], -4, 4)
            && 'image/svg+xml' === $_FILES['svg']['type'];

    $svgContent = $svgContentSent ? file_get_contents($_FILES['svg']['tmp_name']) : '';



    $roundPrecision = null;
    if (isset($_POST['roundprecision'])) {
        if ($_POST['roundprecision'] !== '') {
            $roundPrecision = (int) $_POST['roundprecision'];
        }
    } else {
        $roundPrecision = 2;
    }

    $jsContent = $template;

    $replaces = [
            '%width%' => '/* Unable to find the width value in the SVG file (<svg (...) width="{width}" (...)> */',
            '%height%' => '/* Unable to find the height value in the SVG file (<svg (...) height="{height}" (...)> */',
            '%name%' => 'your_map_name',
            '%js_name%' => 'your_map_name',
            '%author%' => 'author name',
            '%areas%' => '/* Unable to find the paths data in the SVG file (<path (...) d="{data}" (...) /> */',
    ];

    if (isset($_POST['name']) && $_POST['name'] !== '') {
        $replaces['%name%'] = htmlspecialchars($_POST['name']);
        $replaces['%js_name%'] = slugify($_POST['name']);
    }

    if (isset($_POST['author']) && $_POST['author'] !== '') {
        $replaces['%author%'] = htmlspecialchars($_POST['author']);
    }

    // Retrieve width & height from the SVG content
    preg_match('/<svg.*?width="(.*?)".*?>/s', $svgContent, $matches);
    if (count($matches) > 0) {
        $replaces['%width%'] = (float) $matches[1];
    }

    preg_match('/<svg.*?height="(.*?)".*?>/s', $svgContent, $matches);
    if (count($matches) > 0) {
        $replaces['%height%'] = (float) $matches[1];
    }

    // Retrieve paths coordinates
    $elems = [];
    preg_match_all('/<path(.*?)\/?>/s', $svgContent, $matches);
    $size = count($matches[0]);

    for ($i = 0; $i < $size; $i++) {
        preg_match_all('/\sd="(.*?)"/s', $matches[1][$i], $matchesData);
        preg_match_all('/\sid="(.*?)"/s', $matches[1][$i], $matchesId);

        if (isset($matchesId[1][0])) {
            $id = trim($matchesId[1][0]);
        } else {
            // If the path has no ID, generate one ...
            $id = sha1(microtime(true).mt_rand(10000,90000));
        }

        if (isset($matchesData[1][0])) {
            $elems[] = sprintf('                        "%s" : "%s"',
                    $id,
                    preg_replace('/\s+/', ' ',trim(str_replace(["\r\n", "\n", "\t", "\r"], " ", $matchesData[1][0])))
            );
        }
    }

    if (count($elems) > 0) {
        $elems = implode(",\n", $elems);

        if ($roundPrecision !== null) {
            $elems = preg_replace_callback("/([0-9]+)\.([0-9]+)/",
                    function ($matches) use ($roundPrecision) {
                        return round($matches[0], $roundPrecision);
                    }
                    , $elems
            );
        }

        $replaces['%areas%'] = $elems;
    }

    $jsContent = strtr($jsContent, $replaces);

    // Send proper headers to return the JS file to the user
    header('Content-Description: File Transfer');
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename='.$replaces['%js_name%'].'.js');
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    echo $jsContent;
    exit;
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>SVG To Mapael - jQuery Mapael</title>
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
    <h2 style="margin-bottom:10px;text-align:center;">SVG To Mapael</h2>
    <p>This tool aims to generate the jQuery Mapael map file from an SVG file that contains the paths of the areas to retrieve.</p>
    <form role="form" action="svg-to-mapael.php" method="post" enctype="multipart/form-data">
        <div class="form-group">
            <label for="roundprecision">Round precision</label>
            <input type="text" class="form-control" id="roundprecision" name="roundprecision" value="2">
        </div>
        <div class="form-group">
            <label for="name">Name of the map</label>
            <input type="text" class="form-control" id="name" name="name" value="">
        </div>
        <div class="form-group">
            <label for="author">Author</label>
            <input type="text" class="form-control" id="author" name="author" value="">
        </div>
        <div class="form-group">
            <label for="svg">SVG map (.svg)</label>
            <input type="file" name="svg" id="svg">
        </div>
        <button type="submit" class="btn btn-default">Generate mapael map</button>
    </form>
</div>


<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.1/js/bootstrap.min.js"></script>

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
