<?php

/**
 * Tool that allows users to generate the Mapael getCoords() JS function
 */

// Template of the getCoords() function
$template = <<<EOD
                    getCoords : function (lat, lon) {
                            var xfactor = %xfactor%
                                , xoffset = %xoffset%
                                , x = (lon * xfactor) + xoffset
                                , yfactor = %yfactor%
                                , yoffset = %yoffset%
                                , y = (lat * yfactor) + yoffset;
                            
                        return {'x' : x, 'y' : y};
                    },
EOD;

// Compute the getCoords() function content
$result = '';
if (count($_POST) > 0
        && isset($_POST['x1']) && '' !== $_POST['x1']
        && isset($_POST['x2']) && '' !== $_POST['x2']
        && isset($_POST['y1']) && '' !== $_POST['y1']
        && isset($_POST['y2']) && '' !== $_POST['y2']
        && isset($_POST['lat1']) && '' !== $_POST['lat1']
        && isset($_POST['lat2']) && '' !== $_POST['lat2']
        && isset($_POST['long1']) && '' !== $_POST['long1']
        && isset($_POST['long2']) && '' !== $_POST['long2']
) {
    $x1 = (float) $_POST['x1'];
    $x2 = (float) $_POST['x2'];
    $y1 = (float) $_POST['y1'];
    $y2 = (float) $_POST['y2'];
    $lat1 = (float) $_POST['lat1'];
    $lat2 = (float) $_POST['lat2'];
    $long1 = (float) $_POST['long1'];
    $long2 = (float) $_POST['long2'];
    $ax = ($x1 - $x2) / ($long1 - $long2);
    $bx = $x1 - $ax * $long1;
    $ay = ($y1 - $y2) / ($lat1 - $lat2);
    $by = $y1 - $ay * $lat1;

    $result = htmlentities(str_replace(
            array('%xfactor%', '%xoffset%', '%yfactor%', '%yoffset%'),
            array($ax, $bx, $ay, $by ),
            $template
    ));
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>getCoords() generator - jQuery Mapael</title>
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
    <h2 style="margin-bottom:10px;text-align:center;">getCoords() generator</h2>

    <div class="clearfix">
        <a href="assets/img/create-map-2.png" target="_blank"><img src="assets/img/create-map-2.png"style="float:right;margin-left:10px;width:100px;height:120px;" /></a>
        <p>
            This tool aims to generate the getCoords() function for a jQuery Mapael map file. It assumes that the projection of the map is equirectangular. </p>
        <p>
            You have to fill the form with the x coordinates and longitudes of two points to the left and to the right, and the y coordinates and latitudes of two points at the top and at the bottom.
        </p>
    </div>
    <form role="form" action="getcoords.php" method="post">

        <div class="form-group">
            <label for="x1">x1</label>
            <input type="text" class="form-control" id="x1" name="x1" value="<?php echo isset($_POST['x1']) ? htmlentities($_POST['x1']) : ''; ?>">
            <label for="long1">long1</label>
            <input type="text" class="form-control" id="long1" name="long1" value="<?php echo isset($_POST['long1']) ? htmlentities($_POST['long1']) : ''; ?>">
        </div>
        <div class="form-group">
            <label for="x2">x2</label>
            <input type="text" class="form-control" id="x2" name="x2" value="<?php echo isset($_POST['x2']) ? htmlentities($_POST['x2']) : ''; ?>">
            <label for="long2">long2</label>
            <input type="text" class="form-control" id="long2" name="long2" value="<?php echo isset($_POST['long2']) ? htmlentities($_POST['long2']) : ''; ?>">
        </div>
        <div class="form-group">
            <label for="y1">y1</label>
            <input type="text" class="form-control" id="y1" name="y1" value="<?php echo isset($_POST['y1']) ? htmlentities($_POST['y1']) : ''; ?>">
            <label for="lat1">lat1</label>
            <input type="text" class="form-control" id="lat1" name="lat1" value="<?php echo isset($_POST['lat1']) ? htmlentities($_POST['lat1']) : ''; ?>">
        </div>
        <div class="form-group">
            <label for="y2">y2</label>
            <input type="text" class="form-control" id="y2" name="y2" value="<?php echo isset($_POST['y2']) ? htmlentities($_POST['y2']) : ''; ?>">
            <label for="lat2">lat2</label>
            <input type="text" class="form-control" id="lat2" name="lat2" value="<?php echo isset($_POST['lat2']) ? htmlentities($_POST['lat2']) : ''; ?>">
        </div>
        <button type="submit" class="btn btn-default">Generate getCoords() function</button>
    </form>
    <?php if (count($_POST) > 0): ?>
        <h2>Result</h2>
        <textarea class="form-control" rows="10"><?php echo htmlentities($result); ?></textarea>
    <?php endif; ?>
</div>


<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.1/js/bootstrap.min.js"></script>

<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/SyntaxHighlighter/3.0.83/scripts/shCore.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/SyntaxHighlighter/3.0.83/scripts/shBrushJScript.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/SyntaxHighlighter/3.0.83/scripts/shBrushXml.min.js"></script>
<script type="text/javascript" src="assets/js/main.js"></script>

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