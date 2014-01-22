<?php

/**
* @version		1.1 RC1 2008-11-20 21:18:00 $
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

class contacts extends manager
{

    var $mustUpdate = FALSE;
    var $hasmodule = true;
    
    function __construct() 
    {
        $this->Init();
    }
    
    function contacts()
    {
        $this->__construct();
    }

    function AddEventHandlers()
    {
        $this->AddEventHandler('OnBeforeSave','PrepareForSave');
    }
    
    function PrepareForSave()
    {
        $this->AddFieldValidation('name','notnull');
        $this->AddFieldValidation('email','email');
        $this->saveBio();
        $_POST['intro'] = $this->encodeIntro($_POST);
    }
    
    function InitProps() 
    {
        $this->SetProp('headings', array('Name', 'Tasks'));
        $this->SetProp('cols', array('name'));
        $this->SetProp('tasks', array(
            'up:up_arrow.gif', 
            'down:down_arrow.gif',
            TASK_SEPARATOR, 
            'edit', 
            'delete'
        ));
    }
        
    function Trigger()
    {
        global $Core;
        switch ( $this->button ) 
        {
            case 'add':
            case 'edit':
            case 'addcontacts':
            case 'editcontacts':
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
            case 'deletecontacts':
                if (DEMO_MODE)
                {
                    $Core->ExitDemoEvent($this->redirect);
                }
                $this->DeleteItem();
                break;
                
            case 'cancel':
                $Core->ExitEvent( 2, $this->redirect );
                break;
                
            case '': 
                $this->AddButton('Add');
                $this->InitProps();
                $this->ViewItems();
                break;

            default:
                parent::Trigger();
                break;
        }
    }
    
    function InitSkin()
    {
        global $Core;
        
        $file = str_replace( '{objtype}', 'contacts', SB_SKIN_FILE_PATH );
        if ( !file_exists( $file ) )
        {
            $Core->FileNotFound( $file, __LINE__,
                __FILE__.'::InitSkin()'
                );
        } else {
            $this->skin = $Core->OutputBuffer( $file );
        }
    }
    
    function InitEditor() 
    {
        global $Core;

        // Set the form message
        
        $this->SetFormMessage('name','Contact');
        
        // Initialize the object properties to empty strings or
        // the properties of the object being edited
        
        $_OBJ = $this->InitObjProps( $this->skin, $this->obj );
        
        // This step creates a $form array to pass to BuildForm().
        // BuildForm() merges the $obj properites with the form HTML.
        
        $form['ID']      = $this->GetItemID( $_OBJ );
        $form['NAME']    = $this->GetObjProp( $_OBJ, 'name', NULL );
        $form['TITLE']   = $this->GetObjProp( $_OBJ, 'title', NULL );
        $form['EMAIL']   = $this->GetObjProp( $_OBJ, 'email', NULL );
        $form['PHONE']   = $this->GetObjProp( $_OBJ, 'phone', NULL );
        $form['FAX']     = $this->GetObjProp( $_OBJ, 'fax', NULL );
        $form['ADDRESS'] = $this->GetObjProp( $_OBJ, 'address', NULL );
        $form['CITY']    = $this->GetObjProp( $_OBJ, 'city', NULL );
        $form['STATE']   = $Core->StateSelector( 
            $this->GetObjProp( $_OBJ, 'state', NULL ) 
        );
        $form['ZIP']     = $this->GetObjProp( $_OBJ, 'zip', NULL );
        $form['IMAGE']   = $this->GetObjProp( $_OBJ, 'image', NULL );
        $form['IMAGE_SELECTOR']   = $Core->ImageSelector(
			'image', 
			'people/', 
			$_OBJ['image'], 
			' onchange="showImagePreview(\'preview\', this);" '
        );
        $form['BIO']   = $this->GetStoryContent($_OBJ);
        $form['INTRO'] = $this->decodeIntro($_OBJ);
        $form['STORY'] = "contact_bio_{$form['ID']}.txt";
        
        $this->BuildForm( $form );
    }
    
    function encodeIntro($_OBJ) {
        if (trim(Filter::get($_OBJ, 'intro')) == "") return "";
        return base64_encode(Filter::get($_OBJ, 'intro'));
    }
    
    function decodeIntro($_OBJ) {
        if (trim(Filter::get($_OBJ, 'intro')) == "") return "";
        return base64_decode(Filter::get($_OBJ, 'intro'));
    }
    
    function saveBio() {
        global $Core;
        $id = Filter::get($_POST,'id', $this->GetItemID(null));
        $this->SaveStory(
            $Core->GetVar($_POST,'story', "contact_bio_{$id}.txt"), 
            stripslashes(urldecode($_POST['bio']))
        );
        unset($_POST['bio']);
    }
    
    function GetItemID( $obj )
    {
        global $Core;
        
        if ( !isset( $obj['id'] ) ||
             empty( $obj['id'] ) )
        {
            $id = $Core->GetNewID( $this->obs );
        } else {
            $id = $obj['id'];
        }
        return $id;
    }
}