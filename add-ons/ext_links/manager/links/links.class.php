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

defined('SKYBLUE') or die(basename(__FILE__));

class links extends manager
{
    function __construct() 
    {
        $this->datasources['links']  = SB_XML_DIR . 'links.xml';
        $this->datasources['groups'] = SB_XML_DIR . 'linksgroups.xml';
        $this->Init();
    }
    
    function links()
    {
        $this->__construct();
    }

	function InitObjTypes()
	{
	    $this->SetProp('objtypes', array('links', 'linksgroups'));
	}
    
    function AddEventHandlers()
    {
        $this->AddEventHandler('OnBeforeViewItems', 'GroupIdToName');
    }

    function GroupIdToName()
    {
        global $Core;
        if ($this->objtype != 'links') return;
        $groups = $this->GetObjects($this->datasources['groups']);
        for ($i=0; $i<count($this->objs); $i++)
        {
            $group = $Core->SelectObj($groups, $this->objs[$i]->group);
            $obj = &$this->objs[$i];
            $obj->group =  @$group->name;
        }
    }
    
    function InitProps() 
    {
        if ($this->objtype == 'links')
        {
            $this->SetProp('headings', array('Name', 'Group', 'Tasks'));
            $this->SetProp('cols', array('name', 'group' ));
            # $this->AddTab('admin.php?mgroup=collections&mgr=links&objtype=linksgroups&sub=viewgroups', 'View Groups');
        }
        else
        {
            $this->SetProp('headings', array('Name', 'Tasks'));
            $this->SetProp('cols',     array('name'));
            # $this->AddTab('admin.php?mgroup=collections&mgr=links', 'View Links');
        }
        $this->SetProp('tasks', array('up:up_arrow.gif', 'down:down_arrow.gif',
                                      TASK_SEPARATOR, 'edit', 'delete'));
    }

	function Trigger()
	{
	    global $Core;
	
	    if (empty($this->button))
	    {
	        if ($this->objtype == 'links')
	        {
	    		$this->button = 'viewlinks';
            }
            else
			{
			    $this->button = 'viewgroups';
			}
        }
	
	    switch($this->button) 
	    {
	        case 'add':
	        case 'edit':
	        case 'edit'.$this->objtype:
	        case 'add'.$this->objtype:
	            $this->AddButton('Save');
	            $this->InitSkin();
	            $this->InitEditor();
	            $this->Edit();
	            break;
	
			case 'addgroup':
		    case 'editgroup':
		    case 'edit'.$this->objtype:
		    case 'add'.$this->objtype:
		        $this->SetObjType('linksgroups');
		        $this->AddButton('Save');
		        $this->InitSkin();
		        $this->InitEditor();
		        $this->Edit();
		        break;
            
	        case 'save':
	            if (DEMO_MODE) $Core->ExitDemoEvent($this->redirect);
	            $this->SaveItems();
	            break;
            
	        case 'delete':
	        case 'delete'.$this->objtype:
	            if (DEMO_MODE) $Core->ExitDemoEvent($this->redirect);
	            $this->DeleteItem();
	            break;
            
	        case 'cancel':
	            $this->SetObjType(
					$Core->GetVar($_POST, 'objtype', $this->objtype)
				);
	            $this->Cancel();
	            break;
	
		    case 'viewgroups': 
			    $this->SetObjType('linksgroups');
		        $this->AddButton('Add Group');
		        $this->AddButton('View Links');
		        $this->InitProps();
		        $this->ViewItems();
		        break;
            
            case 'viewlinks':
			    $this->SetObjType('links');
	            $this->AddButton('Add');
	            $this->AddButton('View Groups');
	            $this->InitProps();
	            $this->ViewItems();
				break;

		    default: 
                parent::Trigger();
                break;
	    }
	}

    function InitEditor() 
    {
        global $Core;
        
        // Set the form message
        
        $this->SetFormMessage('name', 'Link');
        
        // Initialize the object properties to empty strings or
        // the properties of the object being edited
        
        $_OBJ = $this->InitObjProps($this->skin, $this->obj);
        
        // This step creates a $form array to pass to buildForm().
        // buildForm() merges the $obj properites with the form HTML.

        $form['ID']            = $this->GetItemID($_OBJ);
        $form['NAME']          = $this->GetObjProp($_OBJ, 'name');
        $form['DESCRIPTION']   = $this->GetObjProp($_OBJ, 'caption');
        $form['RELATIONSHIP']  = $this->GetRelSelector($this->GetObjProp($_OBJ, 'relationship'));
        $form['ORDER']         = 
        $Core->OrderSelector2($this->objs, 'name', $_OBJ['name']);    
        
        if ($this->objtype == 'links')
        {
            $form['URL']           = $this->GetObjProp($_OBJ, 'url');
            $form['GROUP']         = 
            $this->ObjectSelector(
                $this->datasources['groups'], 
                    'group', 
                    'id', 
                    'name', 
                    $this->GetObjProp($_OBJ, 'group')
            );    
        }
        $this->BuildForm($form);
    }
    
    function GetRelSelector($rel)
    {
        global $Core;
        $enum = $this->relValues();
        $opts = array(
            $Core->MakeOption(
                ' -- Choose -- ',
                ''
        ));
        for ($i=0; $i<count($enum); $i++)
        {
            $selected = $enum[$i] == $rel ? 1 : 0 ;
            array_push($opts,
                $Core->MakeOption(
                    $enum[$i],
                    $enum[$i],
                    $selected
            ));
        }
        return $Core->SelectList($opts, 'relationship');
    }
    
    function relValues()
    {
        return array(
			"alternate",
			"stylesheet",
			"start",
			"next",
			"prev",
			"contents",
			"index",
			"glossary",
			"copyright",
			"chapter",
			"section",
			"subsection",
			"appendix",
			"help",
			"bookmark",
			"nofollow"
		);
    }

}

?>