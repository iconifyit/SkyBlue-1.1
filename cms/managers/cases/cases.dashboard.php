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

// This class will not operate as is.
// You need to replace the {name} and 
// {classname} tokens with the values
// You choose.

// NOTE: The name you choose for your class
// MUST be unique and not conflict with any
// previously installed <ssm/> Manager

class cases_dashboard 
{
    var $group      = 'active';
    var $title      = 'Case Studies';
    var $link       = '?mgroup=collections&mgr=cases';
    var $mgroup     = 'collections';
    var $hassubmenu = 0;
    var $hasmodule  = true;
    
    // $linktodashboard: boolean value that tells the dashboard loader
    // ( mod.dashboard.php ) whether or not to link back to the section
    // dashboard. Your code should determine under what circumstances 
    // to include the backlink so that the system does not need to have
    // any knowledge of how your manager works.
    // 
    // mod.dashboard.php will build the link and control what the link looks
    // like. The system is set up this way so that the look of the controls
    // is consistent for usability purposes.
    
    var $linktodashboard = NULL;
    
    function __construct() 
    {
        $this->InitDashLink();
    }
    
    function cases_dashboard()
    {
        $this->__construct();
    }
    
    function getEvent()
    {
        global $Core;
        $event = $Core->getVar( $_POST, 'submit', NULL );
        $event = $Core->getVar( $_GET, 'sub', $event );
        $event = str_replace( ' ', NULL, $event );
        $event = strtolower( $event );
        return $event;
    }
    
    function initDashLink()
    {
        switch ( $this->getEvent() ) 
        {
            case 'addcase':
            case 'editcases':
            case 'add':
            case 'edit':
            case 'save':
            case 'delete':
            case 'deletecases':
            case 'cancel':
                $this->linktodashboard = 0;
                break;
            default:
                $this->linktodashboard = 1;
                break;
        }
    }

    // If your component requires a submenu,
    // set the $hassub property to 1 then
    // Add your code below to build the menu.
    
}
?>
