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

class gadgets extends manager
{
    function __construct() 
    {
        $this->Init();
    }
    
    function gadgets()
    {
        $this->__construct();
    }
    
    function InitProps() 
    {
        $this->SetProp('headings', array('Name', 'Enabled', 'Tasks'));
        $this->SetProp('tasks', array('publish', TASK_SEPARATOR, 'edit', 'delete'));
        $this->SetProp('cols', array('name', 'published'));
    }
    
    function Trigger()
    {
        global $Core;
        switch ($this->button) 
        {
            case 'add':
            case 'edit':
            case 'addgadgets':
            case 'editgadgets':
                $this->AddButton('Save');
                $this->InitSkin();
                $this->InitEditor();
                $this->Edit();
                break;
        
            case 'delete':
            case 'deletegadgets':
                if (DEMO_MODE) $Core->ExitDemoEvent($this->redirect);
                $this->DeleteItem();
                break;
                
            case 'back':
            case 'cancel':
                $Core->SBRedirect($this->redirect);
                break;
            
            case 'save':
            case 'savegadgets':
                if (DEMO_MODE) $Core->ExitDemoEvent($this->redirect);
                $this->SaveItems();
                break;
                
            case 'publish':
            case 'publishgadgets':
            case 'unpublish':
            case 'unpublishgadgets':
                $this->publish();
                break;
                
            default: 
                $this->AddButton('Add');
                $this->InitProps();
                $this->ViewItems();
                break;
        }
    }
    
    function publish()
    {
        global $Core;

        $name = $this->obj->name;
        if ($name{0} == '_')
        {
            $Core->ExitEvent($Core->MoveFile(
				SB_SITE_DATA_DIR . "gadgets/$name", 
				SB_SITE_DATA_DIR . "gadgets/" . str_replace('_', null, $name)
			), $this->redirect);
        }
        else
        {
            $Core->ExitEvent($Core->MoveFile(
                SB_SITE_DATA_DIR . "gadgets/$name",
                SB_SITE_DATA_DIR . "gadgets/_$name"
            ), $this->redirect);
        }
    }
    
    function InitEditor() 
    {
        global $Core;
        
        // Set the form message
        
        $this->SetFormMessage('name', 'Gadget');
        
        // Initialize the object properties to empty strings or
        // the properties of the object being edited
        
        $_OBJ = $this->InitObjProps($this->skin, $this->obj);
        
        // This step creates a $form array to pass to buildForm().
        // buildForm() merges the $obj properites with the form HTML.
        
        $form['ID']      = $this->GetItemID($_OBJ);
        $form['NAME']    = $this->GetObjProp($_OBJ,'name');
        $form['CONTENT'] = $this->GetgadgetsContent($_OBJ,'content');
        $form['ZONE']    = $this->GetZone($this->GetObjProp($_OBJ,'name'));
        
        $this->BuildForm($form);
    }
    
    function GetZone($name)
    {
        if (empty($name)) return null;
        if ($name{0} == '_') $name = substr($name, 1, strlen($name));
        return "&lt;!--#gadget:" . substr($name, 0, strlen($name)-3) . "--&gt;";
    }
        
    //////////////////////////////////////////////////////////////////////////
    //
    // PARENT CLASS OVER-RIDES && NON-STANDARD FUNCTIONS
    //
    // The functions below this point implement OOP polymorphism to over-ride 
    // the functionality of the Manager parent class. This class requires some 
    // extended functionality that does not exist in the abstract parent class.
    //
    // In some cases more than one function is used to change the default
    // functionality but the over-ride must always begin with a name that is
    // identical to the function in the parent class.
    //
    //////////////////////////////////////////////////////////////////////////
    
    function SaveItems() 
    {
        global $Core;
        
        $name = $Core->GetVar($_POST, 'name', NULL);
        if (isset($_POST['content']) && !empty($_POST['content']))
        {
            $text = $_POST['content'];
        }
 
        $text = stripslashes($text);
        
        if (!empty($name) && !empty($text))
        {
            $name = $this->AddFileExtension($name, 'js');
            $file = SB_SITE_DATA_DIR . "gadgets/" . $name;
            $Core->ExitEvent($Core->WriteFile($file, $text, 1), $this->redirect);
        }
        else
        {
            $Core->ExitEvent(0, $this->redirect);
        }
    }
    
    function AddFileExtension($name, $ext)
    {
        $bits = explode('.', $name);
        if ($bits[count($bits)-1] !== $ext)
        {
            $bits[] = $ext;
        }
        return implode('.', $bits);
    }
    
    function GetGadgetsContent()
    {
        global $Core;
        
        $path = SB_SITE_DATA_DIR . "gadgets/";
        if (!empty($this->obj->name) &&
             file_exists($path.$this->obj->name))
        {
            return stripslashes($Core->SBReadFile($path.$this->obj->name));
        } else {
            return NULL;
        }
    }
    
    function InitObjs()
    {
        global $Core;
        
        $path = SB_SITE_DATA_DIR . "gadgets/";
        if (is_dir($path))
        {
            $files = $Core->ListFilesOptionalRecurse($path, 0, array());
            for ($i=0; $i<count($files); $i++)
            {
                $name = basename($files[$i]);
				$obj = new stdClass;
				$obj->id = $i + 1;
				$obj->name = $name;
				$obj->published = $name{0} == '_' ? 'No' : 'Yes' ;
				$this->objs[] = $obj;
            }
        }
    }
    
    function LoadObj()
    {
        global $Core;
        for ($i=0; $i<count($this->objs); $i++)
        {
            if ($this->objs[$i]->id == $this->id)
            {
                $this->obj = $this->objs[$i];
            }
        }
        if (!isset($this->obj->id))
        {
            $this->obj = array();
        }
    }
    
    function InitObjType()
    {
        $this->objtype = 'gadgets';
    }
    
    function InitDataSource()
    {
        if (!is_dir(SB_SITE_DATA_DIR . "gadgets/"))
        {
            if (mkdir(SB_SITE_DATA_DIR . "gadgets/"))
            {
                @chmod(SB_SITE_DATA_DIR . "gadgets/", 0755);
            }
        }
        return;
    }
    
    function DeleteItem()
    {
        global $Core;
        
        $name = $this->obj->name;
        $file = SB_SITE_DATA_DIR . "gadgets/" . $name;
        if (file_exists($file) && !is_dir($file))
        {
            $Core->ExitEvent(intval(unlink($file)), $this->redirect);
        } else {
            $Core->ExitEvent(0, $this->redirect);
        }
    }
}

?>