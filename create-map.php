<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Create your own vector map - jQuery Mapael</title>
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
<div class="container-narrow" style="max-width:700px;">
    <div class="jumbotron">
        <h2 style="padding-top:0px;">Create your own vector map for jQuery Mapael</h2>
    </div>

    <p>Some maps are packaged with jquery mapael and some other are available on the <a href="https://github.com/neveldo/mapael-maps" target="_blank">mapael-maps</a> repository (feel free to contribute to this repository !). In this tutorial, I will explain you how to create a new map for jQuery Mapael from an existing SVG file. Let’s create a United Kingdom map for mapael !</p>

    <h3>Summary</h3>

    <ul>
        <li><a href="#first-step">First step : find a SVG map that will serve as a basis</a></li>
        <li><a href="#refine-the-map">Refine the map with Inkscape</a></li>
        <li><a href="#go-further-with-inkscape">Go a little further with Inkscape</a></li>
        <li><a href="#build-map">Build the basic JSON map file for Mapael</a></li>
        <li><a href="#getcoords">Fill the getCoords Function</a></li>
    </ul>

    <h3>Tools used to create a map for Mapael</h3>

    <ul>
        <li><a href="https://inkscape.org" target="_blank">Inkscape</a></li>
        <li><a href="svg-to-mapael.php" target="_blank">"SVG to Mapael" tool</a></li>
        <li><a href="mapael-to-svg.php" target="_blank">"Mapael to SVG" tool</a></li>
        <li><a href="getcoords.php" target="_blank">getCoords() generator</a></li>
        <li><a href="https://github.com/SVG-Edit/svgedit" target="_blank">SVG-edit</a></li>
    </ul>

    <h2 id="first-step">
        <a name="first-step" class="anchor" href="#first-step">#</a>First step : find a SVG map that will serve as a basis
    </h2>

    <div class="clearfix">
        <img src="assets/img/create-map-1.png"style="float:right;margin-left:10px;" />
        <p>The first step is to find an SVG map file. You can find many maps on SVG format with a Creative Commons license on the web. You can find this kind of resources on <a href="http://www.naturalearthdata.com">Natural Earth Data</a> or <a href="http://commons.wikimedia.org/wiki/Category:SVG_maps">Wikimedia Commons</a>.
            A map is based on a specific projection such as mercator, Miller, Lambert or equirectangular projection. Here is a <a href="http://en.wikipedia.org/wiki/List_of_map_projections" target="_blank">list of map projections</a>.</p>

        <p>If you want to plot cities by latitude and longitude on your map, you have to care about the projection that is used on the choosen map. Indeed, the algorithm to convert a latitude and a longitude to x and y coordinates depends on the map projection. The simpliest projection is the equirectangular one (see below fore more information). http://commons.wikimedia.org/wiki/Main_Page</p>

        <p>Here is the <a href="http://commons.wikimedia.org/wiki/File:United_Kingdom_NUTS_location_map.svg" target="_blank">map of of United kingdom</a> with an equirectangular projection and under SVG format.</p>
    </div>

    <h2 id="refine-the-map">
        <a name="refine-the-map" class="anchor" href="#refine-the-map">#</a>Refine the map with Inkscape
    </h2>

    <p>Inkscape is a powerful Open Source tool to edit vector graphics. It will allows us to clean the map by removing unwanted SVG paths and groups, SVG transformations, and trimming the map. Thus, we will be able to extract only the wanted paths that compose the map.</p>

    <p>Go to File > Inkscape Preferences > Transforms. Ensure that “Optimized“ is checked for the “Store transformation” option. It will force Inkscape to translate SVG transforms (such as translations, rotations, …) into coordinates within the paths.</p>

    <p>All we want to retrieve from the SVG file are the paths of each county that will compose our map. We do not want lakes, rivers, small islands, neighbourhood countries. You can remove all these unwanted elements.</p>

    <p>The paths may be grouped within SVG &lt;g&gt; elements. As transformations can be applied to SVG groups, we have to ungroup all groups from the SVG file. Ungroup all objects will make inkscape translate transforms into coordinates within the paths. In order to ungroup all groups, open the XML editor (edit > XML editor) and, for each &lt;g&gt; element found in the XML tree,  click on Object > Ungroup.</p>

    <div class="clearfix" style="width:610px;margin:auto;">
        <p style="text-align:center;float:left;"><img src="assets/img/create-map-8.png" /><br /><span class="legend">Ungroup all groups through the XML editor</span></p>
        <p style="text-align:center;float:left;"><img src="assets/img/create-map-3.png" /><br /><span class="legend">Cleaned map</span></p>
    </div>
    <p>Once your SVG file is cleaned from all unwanted elements and all groups are ungrouped, you can be in front of two cases :</p>

    <p><strong>First case :</strong> the SVG file contains paths that already represent correctly  the different areas of the country. In such a case, you just have to select all the wanted areas (by holding CTRL + uppercase pressed) and copy them in a new Inkscape document.</p>

    <p><strong>Second case :</strong> The SVG file contains paths that do not match with the areas (for example, the file can contains paths just for the boundaries). In such a case, you can use the "Fill bounded areas" tool : <img src="assets/img/create-map-4.png" style="width:20px;height:20px;" />. Set appropriate values for the “Threshold” option (you can set it to 0 if the concerned area has a single color) and the "grow/shrink" option. Ensure that you have zoomed your map enough before filling the areas. Indeed, the filling precision depends on the zoom level. You can now fill all the wanted areas. If you can't fill an area with only one click because of the zoom level, you can complete the filling by clicking on another area and holding CTRL + Uppercase pressed. When all the wanted areas are filled, you can select them (by holding CTRL + Uppercase pressed) and copy them in a new Inkscape document.</p>

    <p style="text-align:center;"><img src="assets/img/create-map-5.png" /><br /><span class="legend">All the wanted areas are selected in order to copy them in a new document</span></p>

    <p>The areas in the new document may not fit with the document dimensions. In this case, go to File > Document Properties > Resize page to content, and click on the “Resize page to drawing or selection”.
        Resize the document makes Inkscape group the areas in a &lt;g&gt; element and apply the transformation on this element. In order to translate this transformation into the paths coordinates, open the XML editor (edit > XML editor) and, for each &lt;g&gt; element found in the XML tree,  click on Object > Ungroup.</p>

    <p style="text-align:center;"><img src="assets/img/create-map-6.png" /><img src="assets/img/create-map-7.png" /><br /><span class="legend">Resized page to drawing</span></p>

    <p>The last step is to associate more explicit IDs than “pathXXX” to each area. To achieve this, you can use the XML editor. </p>

    <p style="text-align:center;"><img src="assets/img/create-map-9.png" /><br /><span class="legend">Associate more explicit IDs to the paths</span></p>

    <p>Save this new SVG file, we are done with Inkscape but you may want to go further by reading the next part.</p>

    <h2 id="go-further-with-inkscape">
        <a name="go-further-with-inkscape" class="anchor" href="#go-further-with-inkscape">#</a>Go a little further with Inkscape
    </h2>

    <p>I will not explain all the features for editing vector images provided by Inkscape because this is not a tutorial dedicated to the tool, but some of them are very usefull for refining the maps.</p>

    <h3>Simplify paths</h3>

    <p>If your final javascript map is too big, you can lighten the paths data with Inkscape by using the tool “Path > Simplify”. If your map is not enough lightened, you can also change the value of the round precision field in the tool  “SVG To mapael” (see below).</p>

    <h3>Combine paths</h3>

    <p>Sometimes, a single area is divided into several distinct SVG paths. Inkscape allows you to gather these paths in one single path : select all paths to be combined (by holding Uppercase)  and use the tool “Path > Combine”.</p>

    <h3>Break apart paths</h3>

    <p>In some other cases, you may want to break apart areas that are gathered in one single SVG path. This is the aim of the tool “Path > Break apart”.</p>

    <h2 id="build-map">
        <a name="build-map" class="anchor" href="#build-map">#</a>Build the basic JSON map file for Mapael
    </h2>

    <p>You have two ways for building the basic JSON map file for Mapael.</p>

    <p><strong>The tedious way :</strong> as SVG is just XML, you can open the file with any text editor. You just have to extract manually the wanted paths data (‘d’ attribute in <path> elements) and paths ids (‘id attribute in <path> elements) in order to build the JSON map for mapael. It should look to something like this :</p>

            <pre class="brush: js;">(function($) {
                $.extend(true, $.fn.mapael, 
                    {
                        maps :{
                            yourMapName : {
                                width : 600,
                                height : 500,
                                getCoords : function (lat, lon) {
                                    // Convert latitude,longitude to x,y here
                                    return {'x' : lat, 'y' : lon};
                                }
                                elems : {
                                    // List of SVG paths for building the map
                                    "department-29" : "m 37.28,156.11 c -1.42,1.23 -3.84,1.18 (...)",
                                    "department-22" : "m 77.67,146.73 c -2.58,0.94 -4.37,2.6 -5.78,4.84 1.21 (...)",
                                    (...)
                                }
                            }
                        }
                    }
                );
            })(jQuery);
            </pre>

    <p><strong>The easy way :</strong> Go to the <strong><a href="svg-to-mapael.php" target="_blank">"SVG to Mapael" tool</a></strong> and submit your brand new SVG file in order to get the JS file generated for Mapael. You can set a value for the round precision field in order to round all paths coordinates. It allows to lighten the size of the output. Usually, a round precision of 2 is enough in most of cases. Leave this input blank if you do not want to round the paths coordinates.</p>

    <p>Save the output in a javascript and your map should now be ready to be loaded into jQuery Mapael !</p>

    <p style="text-align:center;"><img src="assets/img/create-map-10.png" /><br /><span class="legend">The UK map displayed through jQuery mapael</span></p>

    <h2 id="getcoords">
        <a name="getcoords" class="anchor" href="#getcoords">#</a>Fill the getCoords Function
    </h2>

    <p>If you want to go further with your map, you may want plot cities by latitude and longitude on it.
        We assume that the projection of your map is an equirectangular one.</p>

    <p>With this projection, the algorithm to convert a latitude and a longitude to x and y coordinates is as simple as that :</p>

    <ul>
        <li>x = xFactor + longitude + xOffset</li>
        <li>y = yFactor * latitude + yOffset</li>
    </ul>

    <p>You just have to found the proper values for xFactor, yFactor, xOffset and yoffset in order to be able to plot cities by latitude and longitude on your map.</p>

    <div style="clearfix">
        <a href="assets/img/create-map-2.png" target="_blank"><img src="assets/img/create-map-2.png"style="float:right;margin-left:10px;width:150px;height:190px;" /></a>
        <p>To get the values of xFactor and xOffset, you just have to resolve an equation with two unknowns.
            Plot two points at the max left and the max right of your map with x and y coordinates. Go to Google Map in order to find the matching longitudes for these two points (Right click > More info about this place on Google map) . </p>

        <p>You can now find the values for xFactor and xOffset by resolving this equation (this is the tedious way, there is a tool for that, see below ;-) ) :</p>

        <ul>
            <li>x1 = xFactor + longitude1 + xOffset</li>
            <li>x2 = xFactor + longitude2 + xOffset</li>
        </ul>

        <p>Then, you  have to do exactly the same thing to get the values of yFactor and yOffset with matching latitudes.</p>
    </div>

    <p>You can use the <strong><a href="getcoords.php" target="_blank">getCoords() generator</a></strong> tool that will resolve equations and generate the getCoords() method for you !</p>

    <p>Use mapael for plotting points in order s to find min x, max x, min y and max y coordinates. Here is an example for the United Kingdom map where I plotted four points with x and y coordinates on the map :</p>
        
        <pre class="brush: js;">(function($) {
            $(function(){
                $(".container1").mapael({
                    map : {
                        name : "united_kingdom",
                        zoom: {
                            enabled:true
                        },
                        defaultPlot : {
                            size:5,
                            attrs: {
                                fill:"red",
                                stroke:"white",
                                "stroke-width":2,
                                opacity:0.5
                            }
                        }
                    },
                    plots : {
                        p1 : {
                            x:647,
                            y:3
                        },
                        p2 : {
                            x:352,
                            y:1238
                        },
                        p3 : {
                            x:1,
                            y:995
                        },
                        p4 : {
                            x:818,
                            y:945
                        }
                    }
                });
            });
        </pre>

    <p>Then, you can use Google Maps in order to find the matching latitudes and longitudes and fill the form of the <strong><a href="getcoords.php" target="_blank">getCoords() generator</a></strong>.</p>

    <p>With the getCoords() function added to your JS vector map, you can now plot cities by latitude and longitude, here is an example with London plotted on the UK map (latitude : 51.5085300, longitude : -0.1257400) :</p>

    <p style="text-align:center;"><img src="assets/img/create-map-12.png" /></p>

    <p>Your map for mapael is now complete ! Take a look at the mapael documentation to see what you can do with it. Feel free to contribute to mapael-maps repository (https://github.com/neveldo/mapael-maps) by adding your new map !</p>

    <div class="jumbotron">
        <h2 style="padding-top:0px;">Editing existing maps</h2>
    </div>

    <li>You want to bring some changes to an existing Mapael map through Inkscape ? You still can transform a Mapael map to an SVG file with the <strong><a href="mapael-to-svg.php" target="_blank">"Mapael to SVG" tool</a></strong>.</li>

    <li>Another alternative is to copy existing SVG paths from the source file into an SVG editor, like <a href="https://github.com/SVG-Edit/svgedit" target="_blank">SVG-edit</a>. <br>
        For example, if you want to an additional country to a map, you first start by drawing a random line with the Pencil Tool. Then you open the SVG editor (the "&ltSVG&gt" button) and paste the SVG paths of the country you wish to add a neighbouring country to. This way you will have a reference to add the new country. <br>
        Then you either draw the new country yourself, or use the SVG paths from a SVG file you found somewhere and use the same method to paste it in the SVG editor. <br>
        Now you only have to copy the SVG paths from SVG-edit into the map source file with a new label and you're done.
    </li>


</div>


<script type="text/javascript" src="http://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
<script type="text/javascript" src="http://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.1/js/bootstrap.min.js"></script>

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