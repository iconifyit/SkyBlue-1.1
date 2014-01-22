<?php defined('SKYBLUE') or die('Bad file request');

/**
* @version        Beta 1.1 2008-07-29 11:50:00 $
* @package        SkyBlueCanvas
* @copyright    Copyright (C) 2005 - 2008 Scott Edwin Lewis. All rights reserved.
* @license        GNU/GPL, see COPYING.txt
* SkyBlueCanvas is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYING.txt for copyright notices and details.
*/

class snippets extends manager {
    
    function __construct() {
        $this->Init();
    }
    
    function snippets() {
        $this->__construct();
    }

    function AddEventHandlers() {
        $this->AddEventHandler('OnBeforeSave','PrepareForSave');
    }
    
    function PrepareForSave() {
        global $Core;
        
        if ($this->validateNameField()) {
            
            /**
             * Save the snippet text and set the $_POST variable for the story to the file name.
             */
             
            $name = $Core->GetVar($_POST, 'name', "snippet_" . $Core->GetVar($_POST, 'id'));
            $id = $Core->GetVar($_POST, 'id');
            
            $this->SaveStory("snippet_{$id}.txt", stripslashes($_POST['story_content']));
            $_POST['story_content'] = "file:snippet_{$id}.txt";
            
        }
        else {
            $id   = $Core->GetVar($_POST, 'id', '');
            $type = $Core->GetVar($_POST, 'snippettype', 'text');
            $post = base64_encode(serialize($_POST));
            $Core->SBRedirect(
                "admin.php?mgroup=collections&mgr=snippets&objtype=snippets&sub=edit" 
                . "&id={$id}&snippettype={$type}&data={$post}&error=badname" 
            );
        }
    }
    
    function validateNameField() {
        global $Core;
        $name = $Core->GetVar($_POST, 'name', '');
        $id = $Core->GetVar($_POST, 'id', '');
        if (trim($name) == "") {
            return false;
        }
        else {
            $chars = SB_SAFE_URL_CHARS;
            $count = strlen($name);
            for ($i=0; $i<$count; $i++) {
                if (strpos($chars, $name{$i}) === false) {
                    return false;
                }
            }
            foreach ($this->objs as $obj) {
                if ($obj->name == $name && $obj->id != $id) {
                    return false;
                }
            }
        }
        return true;
    }
    
    function InitProps() {
        $this->SetProp('headings', array('Name', 'Type', 'Tasks'));
        $this->SetProp(
            'tasks', 
            array(
                'edit', 
                'delete'
            )
        );
        $this->SetProp('cols', array('name', 'snippettype'));
    }
    
    function InitSkin($form="form.edit.html") {
        global $Core;
        $this->skin = $Core->OutputBuffer(
            SB_MANAGERS_DIR . "{$this->mgr}/html/{$form}"
        );
    }
    
    function DeleteItem() {
        global $Core;
        $story = $Core->GetVar($this->obj, 'story_content', '');
        if (substr($story, 0, 5) == "file:") {
            FileSystem::delete_file(SB_STORY_DIR . str_replace("file:", "", $story));
        }
        parent::DeleteItem();
    }
    
    function Trigger() {
        global $Core;
        switch($this->button) {
            case 'add':
            case 'change':
            case 'back':
                $this->AddButton('Next');
                $this->InitSkin('form.type.html');
                $this->InitEditor('select_type');
                $this->Edit();
                break;

            case 'next':
            case 'edit':
            case 'editsnippets':
                $this->AddButton('Save');
                $this->InitSkin('form.edit.html');
                $this->InitEditor('edit');
                $this->Edit();
                break;
                
            case 'save':
                $this->SaveItems();
                break;
                
            case 'delete':
            case 'deletesnippets':
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
    
    function InitEditor($action='edit') {
        global $Core;
        
        if (strcasecmp($action, 'select_type') === 0) {
        
            // Set the form message
        
			$this->SetFormMessage('name', 'Snippet');
			
			// Initialize the object properties to empty strings or
			// the properties of the object being edited
			
			$_OBJ = $this->InitObjProps($this->skin, $this->obj);
			
			// This step creates a $form array to pass to buildForm().
			// buildForm() merges the $obj properites with the form HTML.
			
			$snippetType = $this->GetObjProp($_OBJ,'snippettype');
			if (isset($_REQUEST['snippettype'])) {
			    $snippetType = $Core->GetVar($_REQUEST, 'snippettype', 'text');
			}
			
			$form['ID']    = $this->GetItemID($_OBJ);
			$form['SNIPPETTYPE'] = $this->getTypeSelector($snippetType);
        }
        else {
            // Set the form message
        
			$this->SetFormMessage('name', 'Snippet');
			
			// Initialize the object properties to empty strings or
			// the properties of the object being edited
			
			$_OBJ = $this->InitObjProps($this->skin, $this->obj);
			
			$snippetType = $this->GetObjProp($_OBJ,'snippettype');
			if (isset($_REQUEST['snippettype'])) {
			    $snippetType = $Core->GetVar($_REQUEST, 'snippettype', 'text');
			}
			$_OBJ['snippettype'] = $snippetType;
			
			if ($Core->GetVar($_REQUEST, 'error', '') != "") {
			    $this->skin = str_replace(
			        '<!--#error_message-->', 
			        $this->getErrorMessage(), 
			        $this->skin
			     );
			     $data = $Core->GetVar($_GET, 'data', '');
			     if (trim($data) != "") {
			         $data = base64_decode($data);
			         $data = unserialize($data);
			         unset($data['submit']);
			     }
			     
			     foreach ($data as $key=>$value) {
			         $_OBJ[$key] = $value;
			     }
			}
			
			$this->skin = str_replace(
				'<!--#textarea-->', 
				$this->getTextEditor($this->GetObjProp($_OBJ,'snippettype')), 
				$this->skin
			);
			
			// This step creates a $form array to pass to buildForm().
			// buildForm() merges the $obj properites with the form HTML.
			
			$form['ID']            = $this->GetItemID($_OBJ);
			$form['NAME']          = $this->GetObjProp($_OBJ,'name', "snippet_{$form['ID']}");
			$form['SNIPPETTYPE']   = $this->GetObjProp($_OBJ,'snippettype');
			$form['STORY_CONTENT'] = $this->GetItemText($_OBJ);
			$form['LINK']          = $this->PageSelector($_OBJ);
			$form['EXTERNAL_LINK'] = $this->GetObjProp($_OBJ,'external_link');
			$form['LINKTEXT']      = $this->GetObjProp($_OBJ,'linktext');
        }
        $this->BuildForm($form);
    }
    
    function getErrorMessage() {
        return '<tr><td valign="top" colspan="2">'
            . '<div class="msg-error"><h2>Error</h2>'
            . '<p>The snippet name must be unique, cannot contain any spaces, and must use '
            . ' only the characters - ' . SB_SAFE_URL_CHARS . '</p>'
            . '</div></td></tr>';
    }
    
    function getTextEditor($type='text') {
        if (strcasecmp($type, 'wysiwyg') === 0) {
			return '<div id="editoranchor"></div>'
				. '<div style="margin: -4px 1px 4px 4px; width: 99%;">'
				. '<textarea id="story_content" name="story_content" class="editor" cols="55" rows="22">{OBJ:STORY_CONTENT}</textarea>'
				. '</div>';
		}
		else {
		    return '<textarea id="content" name="story_content" class="editor" cols="55" rows="22">{OBJ:STORY_CONTENT}</textarea>';
		}
    }
    
    function getTypeSelector($selected) {
        global $Core;
        return $Core->Selector(
            "snippettype", 
            array(
                array('value'=>'text', 'text'=>'Text'),
                array('value'=>'wysiwyg', 'text'=>'WYSIWYG')
            ), 
            $selected
        );
    }
    
    function GetItemText($obj) {
        global $Core;
        if (isset($obj['story_content']) && !empty($obj['story_content'])) {
            $story = $obj['story_content'];
            if (substr($story, 0, 5) == "file:") {
                return $Core->SBReadFile(SB_STORY_DIR . str_replace("file:", "", $story));
            }
            return $story;
        }
        return null;
    }
    
    function PageSelector($obj) {
        global $Core;
        
        $link = null;
        if (isset($obj['link']) && !empty($obj['link'])) {
            $link = $obj['link'];
        }
        
        $pages = $Core->xmlHandler->ParserMain(SB_PAGE_FILE);
        $opts = array();
        array_push($opts, $Core->MakeOption('No Link', null));
        
        $s = $link == 'external' ? 1 : 0 ;
        array_push($opts, $Core->MakeOption('External Link', 'external', $s));
        foreach ($pages as $p) {
            $s = $link == $p->id ? 1 : 0 ;
            array_push($opts, $Core->MakeOption($p->name, $p->id, $s));
        }
        return $Core->SelectList($opts, 'link');
    }
}