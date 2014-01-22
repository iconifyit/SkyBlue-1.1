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

class portfolio_dashboard 
{
    var $title      = 'Gallery';
    var $link       = '?mgroup=collections&mgr=portfolio';
    var $mgroup     = 'collections';
    var $group      = 'active';
    var $cantarget  = 1;
    var $hassubmenu = 1;
    var $hasmodule  = 0;
    
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
    
    function portfolio_dashboard()
    {
        $this->__construct();
    }
    
    function GetEvent()
    {
        global $Core;
        $event = $Core->GetVar( $_POST, 'submit', 'default' );
        $event = $Core->GetVar( $_GET, 'sub', $event );
        $event = str_replace( ' ', NULL, $event );
        $event = strtolower( $event );
        return $event;
    }
    
    function InitDashLink()
    {
        switch ( $event = $this->GetEvent() ) 
        {
            case 'add':
            case 'edit':
            case 'save':
            case 'delete':
            case 'additem':
            case 'edititem':
            case 'saveitem':
            case 'deleteitem':
            case 'addportfolio':
            case 'editportfolio':
            case 'saveportfolio':
            case 'deleteportfolio':
            case 'addcategory':
            case 'editcategory':
            case 'savecategory':
            case 'deletecategory':
                $this->linktodashboard = 0;
                break;
           
            default:
                $this->linktodashboard = 1;
                break;
        }
    }
    
    function getObjType()
    {
        global $Core;
        
        $objtype = $Core->GetVar( $_GET, 'objtype', 'portfolio' );
        
        if ( isset( $_POST ) && count( $_POST ) )
        {
            if ( strpos( strtolower( $Core->GetVar( $_POST, 'submit', NULL ) ), 'item' ) !== FALSE ||
                 $Core->GetVar( $_POST, 'objtype', NULL ) == 'settings'
               )
            {
                $objtype = 'portfolio';
            } 
            else 
            {
                $objtype = NULL;
            }
        }
        
        return $objtype;
    }
    
    function Load() 
    {
        global $Core;
        global $config;

        if ($this->getObjType() == 'portfolio' &&
             strpos(strtolower($Core->GetVar($_POST, 'submit', false)), 'add') === false && 
             strpos(strtolower($Core->GetVar($_GET, 'sub', false)), 'editportfolio') === false)
        {
            $show = $Core->GetVar($_GET, 'show', 'all');
            
            $options    = array();
            $url = BASE_PAGE.'?mgroup=collections&mgr=portfolio&objtype=portfolio&amp;show=';

            $s = $show == 'all' ? 1 : 0 ;
            $options[] = $Core->MakeOption( 'Show All', $url.'all', $s );
            
            $file = SB_XML_DIR.'portfolio/category.xml';
            if ( file_exists( $file ) )
            {
                $categories = $Core->xmlHandler->ParserMain( $file );
                foreach ( $categories as $c ) 
                {
                    $s = $c->id == $show ? 1 : 0 ;
                    $options[] = $Core->MakeOption( $c->title, $url.$c->id, $s );
                }
            }
            
            $js       = ' onchange="changeloc( this );"';
            $selector = $Core->SelectList( $options, 'show', 1, $js );
            
            echo '<fieldset>'."\r\n";
            echo '<legend>Filter By Category</legend>'."\r\n";
            echo $selector."\r\n";
            echo '</fieldset>'."\r\n";
        }
    }

}
?>