<?php
/**
 * @version        1.1 RC1 2008-11-20 21:18:00 $
 * @package        SkyBlueCanvas
 * @copyright    Copyright (C) 2005 - 2008 Scott Edwin Lewis. All rights reserved.
 * @license        GNU/GPL, see COPYING.txt
 * SkyBlueCanvas is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYING.txt for copyright notices and details.
 */
 
?>
<?php if ($remoteFeed = @file_get_contents('http://skybluecanvas.com/remote_dash.html')) : ?>
    <?php echo $remoteFeed; ?>
<?php else : ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"  lang="en-us">
    <head>
        <title>SkyBlueCanvas Info</title>
        <meta http-equiv="Content-Type" content="text/html;charset=UTF-8" />     
        <meta http-equiv="imagetoolbar" content="no" />
        <style type="text/css">
            body { background: #FFF; padding: 0px; }
            .dashboard-block { position: absolute; width: 220px; height: auto; margin: 0px; padding: 0px; }
            #sbc-blog-feed { left: 230px; }
            #sbc-resources { left: 460px; }
            h2,
            .dashboard-block a,
            .dashboard-block li p { color: #555; text-decoration: none; font-family: Arial, Helvetica, sans-serif; font-size: 12px; }
            h2 { color: #000; }
            .dashboard-block a:hover { color: #F60; }
            .dashboard-block ul { margin: 0px; padding: 0px; list-style-type: none; }
            .dashboard-block li { line-height: 1.3em; }
        </style>
    </head>
    <body>
        <div id="sbc-news-feed" class="dashboard-block">
            <h2>SkyBlueCanvas Resources</h2>
            <ul>
                <li><a href="http://blog.skybluecanvas.com" target="_blank">SkyBlueCanvas Blog</a></li>
                <li><a href="http://blog.skybluecanvas.com/topics/skybluecanvas-lightweight-cms/skyblue-canvas-documentation/" target="_blank">Documentation</a></li>
                <li><a href="http://www.skybluecanvas.com" target="_blank">SkyBlueCanvas Home</a></li>
                <li><a href="http://forum.skybluecanvas.com" target="_blank">SkyBlueCanvas Forum</a></li>
                <li><a href="http://forum.skybluecanvas.com/viewforum.php?f=19" target="_blank">Report A Bug</a></li>
            </ul>
        </div>
        <div id="sbc-blog-feed" class="dashboard-block">
            <h2>Premium Templates &amp; Graphics</h2>
            <ul>
                <li><a href="http://themeforest.net?ref=skybluecanvas" target="_blank">ThemeForest.net - Themes from $10</a></li>
                <li><a href="http://graphicriver.net?ref=skybluecanvas" target="_blank">GraphicRiver.net - Graphics from $1</a></li>
                <li><a href="http://codecanyon.net?ref=skybluecanvas" target="_blank">CodeCanyon.net - Scripts from $3</a></li>
                <li><a href="http://activeden.net?ref=skybluecanvas" target="_blank">ActiveDen.net - Flash &amp; Flex from $1</a></li>
            </ul>
            <h2>Free Templates</h2>
            <ul>
                <li><a href="http://www.styleshout.com/" target="_blank">StyleShout.com</a></li>
                <li><a href="http://skybluecanvas.com/SkyBlueCanvas-skins-pg-17.htm"  target="_blank">SkyBlueCanvas.com</a></li>
            </ul>
            <h2>Downloads</h2>
            <ul>
                <li><a href="http://skybluecanvas.com/skybluecanvas-downloads" target="_blank">Extensions and Plugins</a></li>
            </ul>
        </div>
        <div id="sbc-resources" class="dashboard-block">
            <h2>Need Expert Help?</h2>
            <ul>
                <li><a href="http://skybluecanvas.com?subject=Template+Conversion" target="_blank">Template Conversion</a></li>
                <li><a href="http://skybluecanvas.com/contact.html?subject=SkyBlueCanavs+Customization" target="_blank">Customization &amp; Programming</a></li>
                <li><a href="http://skybluecanvas.com/contact.html?subject=SkyBlueCanvas+Support" target="_blank">Paid Support</a></li>
                <li><a href="http://skybluecanvas.com/contact.html?subject=Hosting+Inquiry" target="_blank">Web Site Hosting</a></li>
                <li><a href="http://skybluecanvas.com/contact.html?subject=Web+Design" target="_blank">Web Design</a></li>
                <li><a href="http://skybluecanvas.com/contact.html?subject=Copywriting+Inquiry" target="_blank">Copy &amp; Technical Writing</a></li>
            </ul>
        </div>
    </body>
</html>
<?php endif; ?>