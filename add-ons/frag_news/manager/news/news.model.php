<?php

/**
* @version		RC 1.0.3.2 2008-04-24 15:03:43 $
* @package		SkyBlueCanvas
* @copyright	Copyright (C) 2005 - 2008 Scott Edwin Lewis. All rights reserved.
* @license		GNU/GPL, see COPYING.txt
* SkyBlueCanvas is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYING.txt for copyright notices and details.
*/

defined('SKYBLUE') or die(basename(__FILE__));

class news_model
{
    var $id           = NULL;
    var $name         = NULL;
    var $content      = NULL;
    var $cantarget    = 1;
    var $group        = 'collections';
    var $bundletype   = 'module';
    var $bundlesource = 'mod.news.php';
    var $objtype      = NULL;
    var $loadas       = "module";
    
    function __construct() 
    {
        ;
    }
    
    function news_model()
    {
        $this->__construct();
    }

}

?>