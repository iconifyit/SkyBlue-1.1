<?php

/**
* @version		RC 1.1 2008-12-08 21:00:00 $
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

class news extends manager
{

    var $storyfile = null;
    var $updatesitemap = true;

    function __construct() 
    {
        $this->Init();
    }
    
    function news()
    {
        $this->__construct();
    }

    function AddEventHandlers()
    {
        $this->AddEventHandler('OnBeforeSave','PrepareForSave');
    }
    
    function PrepareForSave()
    {
        global $Core;
    
        $this->AddFieldValidation('title','notnull');
        $this->AddFieldValidation('intro','notnull');
        $this->SaveStoryText();
        
        $_POST['intro'] = base64_encode(stripslashes(urldecode($_POST['intro'])));
        $_POST['text']  = base64_encode(stripslashes(urldecode($_POST['text'])));
    }
    
    function InitProps() 
    {
        $this->setProp('headings', array('News', 'Date', 'Tasks'));
        $this->setProp('tasks', array('edit', 'delete'));
        $this->setProp('cols', array('title', 'date'));
    }
    
    function Trigger()
    {
        global $Core;
        switch($this->button) 
        {
            case 'add':
            case 'edit':
            case 'editnews':
                $this->AddButton('Save');
                $this->InitSkin();
                $this->InitEditor();
                $this->Edit();
                break;
                
            case 'save':
                if (DEMO_MODE)
                {
                    $Core->ExitDemoEvent($this->redirect);
                }
                $this->SaveItems();
                break;
                
            case 'delete':
            case 'deletenews':
                if (DEMO_MODE)
                {
                    $Core->ExitDemoEvent($this->redirect);
                }
                $this->DeleteItem();
                break;
                
            case 'cancel':
                $this->Cancel();
                break;
                
            default: 
                $this->AddButton('Add');
                $this->InitProps();
                $this->ViewItems();
                break;
        }
    }
    
    function SaveStoryText()
    {
        global $Core;
        $this->SaveStory(
            $Core->GetVar($_POST,'story', $this->GetStoryFileName()), 
            stripslashes(urldecode($_POST['text'])));
        $_POST['text'] = 
            base64_encode(stripslashes(urldecode($_POST['text'])));
    }
    
    function InitEditor() 
    {
        global $Core;
        
        // Set the form message
        
        $this->SetFormMessage('title','News');
        
        // Initialize the object properties to empty strings or
        // the properties of the object being edited
        
        $_OBJ = $this->InitObjProps($this->skin, $this->obj);
        
        // This step creates a $form array to pass to buildForm().
        // buildForm() merges the $obj properites with the form HTML.
        
        $form['ID']          = $this->GetItemID($_OBJ);
        $form['TITLE']       = $this->GetObjProp($_OBJ,'title');
        $form['DATE']        = $this->GetObjProp($_OBJ,'date');
        $form['TEXT']        = $this->GetStoryContent($_OBJ); // "";
        $form['INTRO']       = $this->Decode($_OBJ,'intro');
        $form['PUBLISHED']   = 
            $Core->YesNoList('published', $this->GetObjProp($_OBJ,'published'));
        $form['ORDER']       = 
            $Core->OrderSelector2($this->objs, 'title', $_OBJ['title']);
        $form['STORY']       = $this->GetStoryFileName();
        
        $this->BuildForm($form);
        
        // id, title, date, text, published, introlength, link, order
    }
    
    function GetStoryContent($obj)
    {
        global $Core;
        if (!isset($obj['story'])) return null;
        if (!file_exists(SB_STORY_DIR . $obj['story']) || empty($obj['story'])) return null;
        return $Core->SBReadFile(SB_STORY_DIR . $obj['story']);
    }
    
}

?>
