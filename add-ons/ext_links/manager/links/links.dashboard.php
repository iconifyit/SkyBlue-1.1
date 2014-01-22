<?php

/**
* @version		Beta 1.1 2008-07-29 11:50:00 $
* @package		SkyBlueCanvas
* @copyright	Copyright (C) 2005 - 2008 Scott Edwin Lewis. All rights reserved.
* @license		GNU/GPL, see COPYING.txt
* SkyBlueCanvas is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYING.txt for copyright notices and details.
*/

class links_dashboard 
{
    var $title      = 'Links';
    var $link       = '?mgroup=collections&mgr=links';
    var $mgroup     = 'collections';
    var $group      = 'active';
    var $base       = 1;
    var $hassubmenu = 0;
    var $hasmodule  = 1;
    
    // $linktodashboard: boolean value that tells the dashboard loader
    // ( mod.dashboard.php ) whether or not to link back to the section
    // dashboard. Your code should determine under what circumstances 
    // to include the backlink so that the system does not need to have
    // any knowledge of how your manager works.
    // 
    // mod.dashboard.php will build the link and control what the link looks
    // like. The system is set up this way so that the look of the controls
    // is consistent for usability purposes.
    
    var $linktodashboard = null;
    
    function __construct() 
    {
        if ( strpos($this->getEvent(),"add") !== false ||
             strpos($this->getEvent(),"edit") !== false ||
             strpos($this->getEvent(),"delete") !== false ||
             strpos($this->getEvent(),"save") !== false ||
             strpos($this->getEvent(),"cancel") !== false)
        {
            $this->linktodashboard = 0;
        }
        else
        {
            $this->linktodashboard = 1;
        }
    }
    
    function links_dashboard()
    {
        $this->__construct();
    }

    function GetEvent()
    {
        global $Core;
        $event = $Core->GetVar( $_POST, 'submit', null);
        $event = $Core->GetVar( $_GET, 'sub', $event );
        $event = str_replace( ' ', null, $event );
        $event = strtolower( $event );
        return $event;
    }
}
?>