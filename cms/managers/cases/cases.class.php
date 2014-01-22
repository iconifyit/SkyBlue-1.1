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

define('SHOW_PREVIEW_JS', ' onchange="showImagePreview(\'{id}\', this);" ');

class Cases extends manager
{

    var $hasmodule = true;

    function __construct() 
    {
        $this->Init();
    }
    
    function Cases()
    {
        $this->__construct();
    }

    
    function AddEventHandlers()
    {
        $this->AddEventHandler('OnBeforeLoad','InitObjTypes');
        $this->AddEventHandler('OnBeforeLoad','InitObjFilter');
        $this->AddEventHandler('OnBeforeShow','DefineButtons');
        $this->AddEventHandler('OnBeforeSave','PrepareForSave');
    }
    
    function PrepareForSave()
    {
        $this->AddFieldValidation('title','notnull');
        $this->SaveDescription();
    }
    
    function SaveDescription()
    {
        global $Core;
        $this->SaveStory(
            $Core->GetVar($_POST,'story', $this->GetStoryFileName()), 
            stripslashes(urldecode($_POST['blurb'])));
        unset($_POST['blurb']);
    }
    
    function InitObjFilter()
    {
        $this->setProp('filter', 'gid');
        $this->setProp('filterprop', 'group');
    }
    
    function InitObjTypes()
    {
        $this->setProp('objtypes', array('cases'));
    }
    
    function InitProps() 
    {
        if (empty($this->button) ||
             strpos($this->button, 'view') !== FALSE)
        {
            $this->setProp('headings', array('Case', 'Tasks'));
            $this->SetProp('tasks', array('up:up_arrow.gif', 'down:down_arrow.gif',
                                          TASK_SEPARATOR, 'edit', 'delete'));
            $this->setProp('cols', array('title'));
        }
    }

    function DefineButtons()
    {
        global $Core;
        if ($this->showcancel != 1)
        {
            $this->AddButton('Add Case');
        }
    }
    
    function Trigger()
    {
        global $Core;
        
        $this->InitProps();
        
        switch ($this->button) 
        {
            case 'addcase':
            case 'editcase':
            case 'editcases':
            case 'add':
            case 'edit':
                $this->objtype = 'cases';
                $this->AddButton('Save');
                $this->InitSkin();
                $this->InitEditor();
                $this->Edit();
                break;
                
            case 'save':
                $this->SaveItems();
                break;
                
            case 'delete':
            case 'deletecase':
            case 'deletecases':
                $this->DeleteItem();
                break;
                
            case 'cancel':
                $this->Cancel();
                break;
                
            case '':
                $this->ViewItems();
                break;

            default:
                parent::Trigger();
                break;
        }
    }
    
    function initEditor() 
    {
        global $Core;
        
        // Set the form message
                
        $this->SetFormMessage('title','Case Study');
        
        // Initialize the object properties to empty strings or
        // the properties of the object being edited
        
        $_OBJ = $this->InitObjProps($this->skin, $this->obj);
        
        // This step creates a $form array to pass to buildForm().
        // buildForm() merges the $obj properites with the form HTML.
        
        $form['ID']           = $this->GetItemID($_OBJ);
        $form['TITLE']        = $this->GetObjProp($_OBJ, 'title', NULL);
        $form['PROJECTTYPE']  = $this->GetObjProp($_OBJ, 'projecttype', NULL);
        $form['TECHNOLOGIES'] = $this->GetObjProp($_OBJ, 'technologies', NULL);
        $form['DESIGNER']     = $this->GetObjProp($_OBJ, 'designer', NULL);
        $form['DEVELOPER']    = $this->GetObjProp($_OBJ, 'developer', NULL);
        $form['CLIENT']       = $this->GetObjProp($_OBJ, 'client', NULL);
        $form['URL']          = $this->GetObjProp($_OBJ, 'url', NULL);
        $form['BLURB']        = $this->GetStoryContent($_OBJ); // "";
        $form['STORY']        = $this->GetStoryFileName();
        
        if (strpos($_OBJ['thumb'], SB_SITE_DATA_DIR) === FALSE)
        {
            $_OBJ['thumb'] = SB_SITE_DATA_DIR.$_OBJ['thumb'];
        }
        
        if (strpos($_OBJ['photo'], SB_SITE_DATA_DIR) === FALSE)
        {
            $_OBJ['photo'] = SB_SITE_DATA_DIR.$_OBJ['photo'];
        }
        
        $form['THUMB']    = $this->ImageSelector('previewthumb', 'thumb', $_OBJ['thumb']);
        $form['THUMBIMG'] = $this->ImagePreview('previewthumb', $_OBJ['thumb']);

        $form['PHOTO']    = $this->ImageSelector('previewphoto', 'photo', $_OBJ['photo']);
        $form['PHOTOIMG'] = $this->ImagePreview('previewphoto', $_OBJ['photo']);

        $form['ORDER'] = $Core->OrderSelector2($this->objs, 'title', $_OBJ['title']);

        $this->BuildForm($form);
    }
        
    function ImageSelector($id, $fieldname, $selected)
    {
        global $Core;
        $js = str_replace('{id}', $id, SHOW_PREVIEW_JS);
        return $Core->ImageSelector($fieldname, '', $selected, $js);
    }
    
    function ImagePreview($id, $img)
    {
        global $Core;
        $attrs = array();
        $attrs['src'] =  $img;
        if (!file_exists($attrs['src']) || is_dir($attrs['src']))
        {
            $attrs['src'] = CAMERA_ICON_GIF;
        }
        $attrs['id']      = $id;
        $w = $Core->imageWidth($attrs['src']);
        $h = $Core->imageHeight($attrs['src']);
        list($w, $h)    = $Core->ImageDimsToMaxDim(array($w, $h), '72', '72');
        $attrs['width']   = $w;
        $attrs['height']  = $h;
        $attrs['alt']     = 'preview of '.basename($attrs['src']);
        return $Core->HTML->MakeElement('img', $attrs, '');
    }
    
    function GetRelativeImgPaths()
    {
		if (isset($_POST['thumb']) && !empty($_POST['thumb']))
		{
			$_POST['thumb'] = str_replace(SB_SITE_DATA_DIR, NULL, $_POST['thumb']);
		}
		if (isset($_POST['photo']) && !empty($_POST['photo']))
		{
			$_POST['photo'] = str_replace(SB_SITE_DATA_DIR, NULL, $_POST['photo']);
		}
    }
}

?>

