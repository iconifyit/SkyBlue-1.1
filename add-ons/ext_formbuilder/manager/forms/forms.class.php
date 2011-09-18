<?php

/**
* @version        Beta 1.1 2008-06-22 11:50:00 $
* @package        SkyBlueCanvas
* @copyright    Copyright (C) 2005 - 2008 Scott Edwin Lewis. All rights reserved.
* @license        GNU/GPL, see COPYING.txt
* SkyBlueCanvas is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYING.txt for copyright notices and details.
*/

defined('SKYBLUE') or die(basename(__FILE__));


class forms extends manager {

    var $redirects = array();
    var $ini = array();
    
    function __construct() {
        $this->parse_ini();
        parent::Init();
    }
    
    function parse_ini() {
        if (file_exists(dirname(__FILE__) . "/formbuilder.ini")) {
            $this->ini = parse_ini_file(dirname(__FILE__) . "/formbuilder.ini", true);
        }
    }
    
    function forms() {
        $this->__construct();
    }
    
    function InitItemID() {
        parent::InitItemID();
        if ($this->id <= 0) {
           $id = $this->GetContext('ID');
           $this->id = $id ? $id : 1 ;
        }
    }
    
    function InitObjTypes() {
        $this->SetProp(
            'objtypes', 
            array('forms', 'fields')
       );
    }
    
    function InitProps() {
        if (empty($this->button) ||
            strpos($this->button, 'view') !== false ||
            strpos($this->button, 'fields') !== false ||
            $this->button == 'back')
        {
            $this->SetProp('headings', array('Name', 'Id', 'Tasks'));
            $this->SetProp('cols', array('title', 'id'));
            if ($this->objtype == 'forms') {
                $this->SetProp('tasks', array('preview', 'fields', 'edit', 'delete'));
            }
            else {
                $this->SetProp(
                    'tasks', 
                    array(
                        'up:up_arrow.gif', 
                        'down:down_arrow.gif',
                        TASK_SEPARATOR, 
                        'edit', 
                        'delete'
                   )
               );
            }
        }

    }
    
    function FilterFields($formid, $objs) {
        $fobjs = array();
        foreach ($objs as $obj) {
            if ($obj->formid == $formid) $fobjs[] = $obj;
        }
        return $fobjs;
    }

    function FilterViewItems($objs) {
        $fobjs = array();

        if (count($objs) > 0) {
            // When dealing with fields, filter out items belonging to other forms.
            if ($objs[0]->objtype == 'fields') {
                $fobjs = $this->FilterFields($this->id, $objs);
            }
            else {
                $fobjs = $objs;
            }
        }
        return parent::FilterViewItems($fobjs);
    }

    function SetContext($field, $value) {
        if (isset($this->id) && $this->id != 0) {
            parent::SetContext($field, $value);
        }
    }

    function Trigger() {
        global $Core;
        switch ($this->button) {
            case 'cancel':
                $objtype = Filter::get($_POST, 'objtype', 'forms');
                $this->UpdateReferences($objtype);
                if ($objtype == 'fields') {
                    $this->redirect = MGR_DEFAULT_REDIRECT.'&objtype=fields&sub=fieldsforms';
                }
                $this->Cancel();
                break;
            case 'savefield':
                $this->UpdateReferences('fields');
                $this->SaveItems(MGR_DEFAULT_REDIRECT.'&objtype=fields&sub=fieldsforms');
                break;
            case 'saveform':
                $this->UpdateReferences('forms');
                $this->SaveItems(MGR_DEFAULT_REDIRECT);
                break;
            case 'addfield':
                $this->id = $this->obj->id = null;
            case 'editfields':
                $this->SetContext('TYPE', 'fields');
                $this->UpdateReferences('fields');
                $this->AddButton('Save Field');
                $this->GetEditorForm();
                $this->InitEditor();
                $this->Edit();
                break;
            case 'upfields':
                $this->UpdateReferences('fields');
                $this->redirect = MGR_DEFAULT_REDIRECT.'&objtype=fields&sub=fieldsforms';
                $this->ReorderObjs(true, 'formid');
                break;
            case 'downfields':
                $this->UpdateReferences('fields');
                $this->redirect = MGR_DEFAULT_REDIRECT.'&objtype=fields&sub=fieldsforms';
                $this->ReorderObjs(false, 'formid');
                break;
            case 'addform':
            case 'editforms':
                $this->AddButton('Save Form', '');
                $this->GetEditorForm();
                $this->InitEditor();
                $this->Edit();
                break;
            case 'deletefields':
                $this->UpdateReferences('fields');
                $this->redirect = MGR_DEFAULT_REDIRECT.'&objtype=fields&sub=fieldsforms';
                $this->DeleteItem();
                break;
            case 'deleteforms':
                $this->UpdateReferences('forms');
                $this->redirect = MGR_DEFAULT_REDIRECT;
                $this->DeleteFields();
                $this->DeleteItem();
                break;
            case 'previewforms':
                $this->PreviewForm();
                break;
            case 'fieldsforms':
                $this->objs = array();
                $this->UpdateReferences('fields');
                $this->SetContext('TYPE', 'fields');
                $this->SetContext('ID', $this->id);
                $this->InitProps();
                $this->AddButton('Add Field');
                $this->AddButton('View Forms');
                $this->ViewItems();
                break;
            case 'viewforms':
            case 'back':
            default:
                $this->UpdateReferences('forms');
                $this->SetContext('TYPE', 'none');
                $this->SetContext('ID', 0);
                $this->InitProps();
                $this->AddButton('Add Form', '');
                $this->ViewItems();
                break;
        }
    }
        
    function AddEventHandlers() {
        $this->AddEventHandler('OnBeforeSave', 'PrepareForSave');
    }
    
    function PrepareForSave() {
        global $Core;
        
        $this->AddFieldValidation('title','notnull');
        
        if ($this->objtype == 'forms') {
            $_POST['blurb'] = $this->Encode($_POST['blurb']);
            $_POST['autoresponse'] = $this->Encode($_POST['autoresponse']);
        }
        else {
            $_POST['label']  = $this->Encode(Filter::get($_POST, 'label', null));
        }
        
        if (in_array('regex', Filter::get($_POST, 'validation', array()))) {
			if (trim(Filter::get($_POST, 'regex')) != "") {
				$_POST['regex'] = $this->encodeRaw(
					Filter::get($_POST, 'regex')
				);
			}
        }
        else if (Filter::get($_POST, 'fieldtype') == "password") {
            $validation = Filter::get($_POST, 'validation', array());
            array_push($validation, 'regex');
            $_POST['validation'] = $validation;
        	$_POST['regex'] = $this->encodeRaw(
        	    $this->getPasswordRegex($this->getPasswordOptions())
        	);
        }
        
        $chars = Filter::get($_POST, 'pass_chars');
        if (!empty($chars)) {
            $_POST['pass_chars'] = $this->encodeRaw($chars);
        }
        
        $_POST['validation_error_message'] = $this->encodeRaw(
            Filter::get($_POST, 'validation_error_message')
        );
        
        $this->saveButtonCallbackFile();
        
        $_POST['button_onclick'] = "";
    }
    
    function deleteButtonCallbackFile($callback_file) {
        if (file_exists($callback_file)) {
            @unlink($callback_file);
        }
    }
    
    function initFormScriptsFile($formId) {
        $script_file = ACTIVE_SKIN_DIR . "js/formbuilder_form_id_{$formId}.js";
        if (!file_exists($script_file)) {
            FileSystem::write_file(
                $script_file,
                "/*@ THIS FILE IS DYNAMICALLY GENERATED BY FORMBUILDER : DO NOT MODIFY @*/\n\n"
            );
        }
        return $script_file;
    }
    
    function saveButtonCallbackFile() {
        $formId = Filter::get($_POST, 'formid');
        if (!empty($formId)) {
            $script_file = $this->initFormScriptsFile($formId);
            if (Filter::get($_POST, 'fieldtype') == "button") {
                $callback = Filter::get($_POST, 'button_onclick');
                $callbackId = Filter::get($_POST, 'button_onclick_id');
                $callback_file = $script_file;
                $_POST['button_callback_file'] = basename($script_file);
                if (!empty($callback)) {
                    $name = Filter::get($_POST, 'title');
                    $script_contents = $this->getScriptFileText($script_file);
                    FileSystem::write_file(
                        $script_file, 
                        $this->stripCallbackScript($script_contents, $callbackId) . 
                        $this->wrapCallbackScript($name, $callback, $callbackId)
                    );
                }
            }
        }
    }
    
    function wrapCallbackScript($name, $callback, $script_id) {
        $script = "/*@start.{$script_id}@*/\r\n"
            . '$(function(){$("button[@name=\'' . $name 
            . '\']").bind("click", /*@*/' . $callback . '/*@*/);});'
            . "\r\n/*@end.{$script_id}@*/\r\n\r\n";
        return $script;
    }
    
    function stripCallbackScript($script_file_text, $script_id) {
        $matches = $this->getCallbackRegexMatches($script_file_text, $script_id);
        if (count($matches)) {
            $script_file_text = str_replace($matches[0], "", $script_file_text);
        }
        return $script_file_text;
    }
    
    function getCallbackScript($script_file_text, $script_id) {
        $matches = $this->getCallbackRegexMatches($script_file_text, $script_id);
        if (count($matches) == 2) {
            $script_file_text = $matches[1];
            $parts = explode('/*@*/', $script_file_text);
            if (count($parts) == 3) {
                $script_file_text = trim($parts[1]);
            }
        }
        return $script_file_text;
    }
    
    function getCallbackRegexMatches($script_file_text, $script_id) {
        $wrapper = "/\/\*@start\.{$script_id}@\*\/(.*)\\r\\n\/\*@end\.{$script_id}@\*\/\\r\\n\\r\\n/is";
        preg_match($wrapper, $script_file_text, $matches);
        return $matches;
    }
    
    function getScriptFileText($script_file) {
        if (file_exists($script_file)) {
            return FileSystem::read_file($script_file);
        }
        return "";
    }
    
    function encodeRaw($value) {
        if (trim($value) != "") {
            return base64_encode($value);
        }
        return "";
    }
    
    function decodeRaw($value) {
        if (trim($value) != "") {
            return base64_decode($value);
        }
        return "";
    }
        
    function InitEditor() {
        global $Core;
        
        // Set the form header message

        $this->SetFormMessage(
        	'title', 
        	$this->objtype == 'forms' ? 'Form' : 'Field'
        );
        
        // Initialize the object properties to empty strings or
        // the properties of the object being edited
        
        $_OBJ = $this->InitObjProps($this->skin, $this->obj);
        
        // This step creates a $form array to pass to buildForm().
        // buildForm() merges the $obj properites with the form HTML.
        
        $form['ID']        = $this->GetItemID($_OBJ);
        $form['TITLE']     = $this->GetObjProp($_OBJ, 'title');
        $form['OBJTYPE']   = $this->objtype;
        
        if ($this->objtype == 'forms') {
            $form['SHOWTITLE'] = $Core->YesNoList(
                'showtitle', 
                $this->GetObjProp($_OBJ, 'showtitle')
           );
            $form['BLURB'] = $this->Decode($this->GetObjProp($_OBJ, 'blurb'));
            
            $opts = array();
            
            $form['ACTION'] = $Core->SelectList($opts, 'action');
            $form['CONTACT'] = $this->GetObjProp($_OBJ, 'contact');
            $form['AUTORESPONSE'] = $this->Decode($this->GetObjProp($_OBJ, 'autoresponse'));
            $form['CALLBACK_EVENT'] = $this->getCallbackEvent($_OBJ);
        }
        else {
            $form['FORMID'] = $this->GetObjProp($_OBJ, 'formid', $this->GetContext('ID'));
            $form['FORM'] = $this->FormSelector(
                $this->GetObjProp($_OBJ, 'formid', $this->GetContext('ID'))
           );
            $form['LABEL']  = $this->Decode($this->GetObjProp($_OBJ, 'label'));
            $form['FIELDTYPE'] = $this->TypeSelector($this->GetObjProp($_OBJ, 'fieldtype'));
            $form['VALIDATION'] = $this->ValidationSelector($this->GetObjProp($_OBJ, 'validation'));
            $form['ROWS'] = $this->GetObjProp($_OBJ, 'rows');
            $form['COLS'] = $this->GetObjProp($_OBJ, 'cols');
            $form['OPTIONS'] = $this->GetObjProp($_OBJ, 'options');
            $form['CLASS'] = $this->GetObjProp($_OBJ, 'class');
            $form['SIZE']  = $this->GetObjProp($_OBJ, 'size');
            $form['MAXLENGTH']  = $this->GetObjProp($_OBJ, 'maxlength');
            $form['MINLENGTH']  = $this->GetObjProp($_OBJ, 'minlength');
            $form['REGEX']  = $this->GetRegEx($_OBJ);
            $form['MULTI_SELECT']  = $Core->YesNoList('multi_select', $this->GetObjProp($_OBJ, 'multi_select'));
            $form['VALIDATION_ERROR_MESSAGE'] = $this->GetValidationErrorMessage($_OBJ);
            
            $form['BUTTON_ONCLICK'] = "";
            $form['BUTTON_ONCLICK_ID'] = "";
            $form['BUTTON_CALLBACK_FILE'] = "";
            
            if ($this->GetObjProp($_OBJ, 'fieldtype') == "button") {
                $form['BUTTON_ONCLICK'] = $this->GetButtonCallback($_OBJ);
                $form['BUTTON_ONCLICK_ID'] = $this->getButtonCallbackID($_OBJ);
                $form['BUTTON_CALLBACK_FILE'] = $this->GetObjProp($_OBJ, 'button_callback_file');
            }
            
			$form['PASS_LENGTH'] = $this->GetObjProp($_OBJ, 'pass_length', 8);
			if (empty($length) || !is_numeric($length) || $length < 1) {
				$length = 8;
			}
			$form['PASS_LENGTH'] = $length;
			$form['PASS_DIGIT'] = $Core->YesNoList(
				'pass_digit', 
				$this->GetObjProp($_OBJ, 'pass_digit', 1)
			);
			$form['PASS_UCCHAR'] = $Core->YesNoList(
				'pass_ucchar', 
				$this->GetObjProp($_OBJ, 'pass_ucchar', 1)
			);
			$form['PASS_CHARS'] = $this->getPasswordChars($_OBJ);
            
            $form['FILE_TYPES'] = $this->GetObjProp($_OBJ, 'file_types');
            
            $form['ORDER'] = null;
            if (count($this->objs) > 1) {
                $objs = $this->OrderFields($this->objs, $this->id);
                $objs = $this->FilterFields($this->GetContext('ID'), $objs);
                $form['ORDER'] = $Core->OrderSelector2($objs, 'title', $_OBJ['title'], false);
            }
        }
        
        $this->BuildForm($form);
    }
    
    function getPasswordChars($_OBJ) {
    	$chars = $this->GetObjProp($_OBJ, 'pass_chars');
    	if (empty($chars)) {
    	    return '~!@#$%^&*()_+=';
    	}
    	return $this->decodeRaw($chars);
    }
    
    function getPasswordOptions() {
        return array(
            'pass_length' => Filter::get($_POST, 'pass_length', 8),
            'pass_digit'  => Filter::get($_POST, 'pass_digit',  1), 
            'pass_ucchar' => Filter::get($_POST, 'pass_ucchar', 1), 
            'pass_chars'  => Filter::get($_POST, 'pass_chars',  '')
        );
    }

    function getPasswordRegex($options) {
		
		$length  = Filter::get($options, 'pass_length');
		$ucchar  = Filter::get($options, 'pass_ucchar');
		$digit   = Filter::get($options, 'pass_digit');
		$special = Filter::get($options, 'pass_chars');
		
		$regex = "/^.*";
		if (!empty($length)) {
			$length = "(?=.{" . $length . ",})" ;
		}
		if ($digit) {
			$digit = "(?=.*\d)" ;
		}
		if ($ucchar) {
			$ucchar = "(?=.*[A-Z])" ;
		}
		if (!empty($special)) {
		    $chars = array();
		    for ($i=0; $i<strlen($special); $i++) {
		        array_push($chars, "\\".$special{$i});
		    }
		    $special = implode('', $chars);
			$special = "(?=.*[" . $special .  "])";
		}
	
		return "/^.*" . $length . $digit . $ucchar . $special . ".*$/";
	}
    
    function getButtonCallbackID($_OBJ) {
        $callbackId = Filter::get($_OBJ, 'button_onclick_id');
        if (empty($callbackId)) {
            return 'button_onclick_id_' . $_OBJ['id'];
        }
        return $callbackId;
    }
    
    function GetButtonCallback($_OBJ) {
        $callback = "";
        $formId = Filter::get($_OBJ, 'formid');
        if (!empty($formId)) {
            $script_file_text = $this->getScriptFileText(
                ACTIVE_SKIN_DIR . "js/formbuilder_form_id_{$formId}.js"
            );
            $callbackId = Filter::get($_OBJ, 'button_onclick_id');
            if (!empty($callbackId)) {
                return $this->getCallbackScript($script_file_text, $callbackId);
            }
        }
        return "";
    }
    
    function GetValidationErrorMessage($_OBJ) {
        return $this->decodeRaw(
            Filter::get($_OBJ, 'validation_error_message')
        );
        return "";
    }
    
    function GetRegEx($_OBJ) {
        return $this->decodeRaw(
            Filter::get($_OBJ, 'regex')
        );
        return "";
    }
    
    function getCallbackEvent($_OBJ) {
        $event = $this->GetObjProp($_OBJ, 'callback_event');
        if (empty($event)) {
            $event = 'callback_form_id_' 
                .  $this->GetObjProp($_OBJ, 'formid', $this->GetItemID($_OBJ));
        }
        return $event;
    }
    
    function getCallbacks() {
        $callbacks = array();
        $plugins = FileSystem::list_files(SB_SITE_DATA_DIR . "plugins/");
        if (!count($plugins)) return array();
        for ($i=0; $i<count($plugins); $i++) {
            if ($plugins[$i]{0} == '_') continue;
            $code = FileSystem::read_file($plugins[$i]);
            if (preg_match_all("/@callback:(.*)@/i", $code, $matches)) {
                for ($j=0; $j<count($matches[1]); $j++) {
                    $callback = $matches[1][$j];
                    array_push($callbacks, $callback);
                }
            }
        }
        return $callbacks;
    }
    
    function Decode($str) {
        if (!empty($str)) {
            return urldecode(stripslashes(base64_decode($str)));
        }
        return "";
    }
    
    function Encode($str) {
        if (!empty($str)) {
            return base64_encode(stripslashes(urlencode($str)));
        }
        return "";
    }
    
    function ValidationSelector($selected="") {
        global $Core;
        
        $options = "";
        
        $selected = explode(',', $selected);
        
        $validations = Filter::get($this->ini, 'VALIDATIONS');
        
        if (empty($validations)) {
            $validations = array(
                'notempty'  => 'Not Empty',
                'number'    => 'Number',
                'email'     => 'Email',
                'url'       => 'URL',
                'maxlength' => 'Maximum Length',
                'minlength' => 'Minimum Length',
                'regex'     => 'Regular Expression'
            );
        }
        
        foreach ($validations as $type=>$text) {
            $attrs = array(
                'name'  => 'validation[]', 
                'value' => $type, 
                'type'  => 'checkbox', 
                'class' => 'validation_option'
            );
            if (in_array($type, $selected)) {
                $attrs['checked'] = 'checked';
            }
            $options .= $Core->HTML->MakeElement(
                'li',
                array(),
                $Core->HTML->MakeElement(
                    'input',
                    $attrs,
                    "",
                    0
                ) . "&nbsp;{$text}"
            );
        }

        return $Core->HTML->MakeElement(
            'ul',
            array(),
            $options
        );
    }    
    
    function TypeSelector($selected='') {
        global $Core;
        
        $options = array();
        
        $types = Filter::get($this->ini, 'FIELD_TYPES');
        
        if (empty($types)) {
            $types = array(
                'text'     => 'Text',
                'password' => 'Password',
                'textarea' => 'Text Area',
                'checkbox' => 'Checkbox',
                'radio'    => 'Radio',
                'select'   => 'Select List',
                'file'     => 'File Upload',
                'button'   => 'Button'
            );
        }

        foreach ($types as $type=>$text) {
            $s = $selected == $type ? 1 : 0;
            array_push($options, $Core->MakeOption($text, $type, $s));
        }
        return $Core->SelectList($options, 'fieldtype');
    }
    
    function DeleteFields() {
        global $Core;
                
        $Core->RequireID($this->id, $this->redirect);

        $src = SB_XML_DIR.'fields.xml';
        $allfields = $Core->xmlHandler->ParserMain($src);
        $fields = array();
        foreach ($allfields as $field) {
            if ($field->formid != $this->id)
                $fields[] = $field;
        }

        $xml = $Core->xmlHandler->ObjsToXML($fields, 'fields');
        $Core->WriteFile($src, $xml, 1);
    }
    
    function DeleteItem() {
        global $Core;
        
        $Core->RequireID($this->id, $this->redirect);
        
        $obj = $Core->SelectObj($this->objs, $this->id);
        
        if ($obj && isset($obj->story)) {
            $story = SB_STORY_DIR . $obj->story;
            if (file_exists($story)) {
                unlink($story);
            }
        }
        
        $this->objs = $Core->DeleteObj($this->objs, $this->id);
        $xml = $Core->xmlHandler->ObjsToXML($this->objs, $this->objtype);
        
        $new_bundles = array();
        $old_bundles = $Core->xmlHandler->ParserMain(SB_BUNDLE_FILE);
        $name = "[ID:{$obj->id}]";
        for ($i=0; $i<count($old_bundles); $i++) {
            if (  $old_bundles[$i]->bundletype != $this->objtype
                || strpos($old_bundles[$i]->name, $name) === false) 
            {
                array_push($new_bundles, $old_bundles[$i]);
            }
        }
        $Core->WriteFile(
            SB_BUNDLE_FILE,
            $Core->xmlHandler->ObjsToXML($new_bundles, $this->objtype),
            1
       );
        
        $Core->ExitEvent(
            $Core->WriteFile($this->datasrc, $xml, 1), 
            $this->redirect
       );
    }
    
    function SaveItems($redirect='') {
        global $Core;
        
        if (!empty($redirect)) {
            $this->redirect = $redirect;
        }
        
        $this->BeforeSave();
        
        if ($this->updatesitemap) {
            $Core->UpdateSitemap();
        }
        
        foreach ($_POST as $k=>$v) {
            if ($k != 'submit') {
                if (is_array($v)) {
                    for($i=0; $i<count($v); $i++) {
                        $v[$i] = trim(Filter::get($v, $i, null));
                    }
                    $val = implode(',', $v);
                } 
                else {
                    $val = trim(Filter::get($_POST, $k, null));
                }
                $this->ValidateField($k);
                $arr[$k] = $Core->stripslashes_deep($val);
            }
        }
        
        $obj = $Core->SelectObj($this->objs, $this->id);
        if (!isset($obj->id) || $obj->id == 0) {
            $obj = $Core->ArrayToObj($obj, $arr);
        } 
        else {
            $obj = $Core->UpdateObjFromArray($obj, $arr);
        }
        
        $this->objs = $Core->InsertObj($this->objs, $obj, 'id');
        
        if (!empty($arr['order'])) {
            $this->objs = $Core->OrderObjs(
                $this->objs, $this->id, $arr['order']);
        }
        
        if ($obj->type == "forms") {
            $found = false;
            $bundles = $Core->xmlHandler->ParserMain(SB_BUNDLE_FILE);
            $name = "[ID:{$obj->id}]";
            for ($i=0; $i<count($bundles); $i++) {
                $bundle =& $bundles[$i];
                if ($bundle->bundletype == $this->objtype && 
                    strpos($bundle->name, $name) !== false) 
                {
                    $found = true;
                    $bundle->name = "{$obj->title} [ID:{$obj->id}]";
                    foreach ($obj as $k=>$v) {
                        if (isset($bundle->$k)) {
                            $bundle->$k == $v;
                        }
                    }
                }
            }
            if (!$found) {
                $newObj = new stdClass;
                $newObj->id = $Core->GetNewID($bundles);
                $newObj->bundletype = $this->objtype;
                $newObj->name = "{$obj->title} [ID:{$obj->id}]";
                $newObj->page = "";
                $newObj->region = "";
                $newObj->published = 1;
                $newObj->cantarget = 1;
                $newObj->source = "forms.xml";
                $newObj->engine = "mod.forms.php";
                $newObj->loadas = "xml";
                array_push($bundles, $newObj);
            }
    
            FileSystem::write_file(
                SB_BUNDLE_FILE,
                $Core->xmlHandler->ObjsToXML($bundles, "bundle")
            );
        }
        
        $xml = $Core->xmlHandler->ObjsToXML($this->objs, $this->objtype);
        $Core->ExitEvent(
            FileSystem::write_file($this->datasrc, $xml), 
            $this->redirect
       );
    }

    function OrderFields($objs, $ignoreid) {
        $fobjs = array();

        $order = 1;
        for ($i = 0; $i < count($objs); $i++) {
            $fobjs[$i] = $objs[$i];
            if ($fobjs[$i]->id != $ignoreid) {
                $fobjs[$i]->order = $order++;
            }
        }
        return $fobjs;
    }

    function FormSelector($selected='') {
        global $Core;

        $disableFormSelection = true;
        $forms = $Core->xmlHandler->ParserMain(SB_XML_DIR.'forms.xml');
        if ($disableFormSelection) {
            for ($i=0; $i<count($forms); $i++) {
                if ($forms[$i]->id == $selected)
                    return $forms[$i]->title . ' (id=' . $forms[$i]->id . ')';
            }
        }
        else {
            $opts = array();
            $opts[] = $Core->MakeOption(' -- Select Form -- ', null);
            for ($i=0; $i<count($forms); $i++) {
                $s = $forms[$i]->id == $selected ? 1 : 0;
                $opts[] = $Core->MakeOption($forms[$i]->title, $forms[$i]->id, $s);
            }
            return $Core->SelectList($opts, 'formid');
        }
    }
        
    function PreviewForm() {
        global $Core;
        
        $this->buttons[0]['value'] = 'Back';
        $this->buttons[0]['js'] = '';

        $this->html  = '<tr><td style="border: none; padding: 0px;">'."\r\n";
        $this->html .= '<div id="preview">'."\r\n";
        $this->html .= '<div id="previewform">'."\r\n";
        
        $data = "";
        
        $params = array(
            'forms',
            'body',
            $this->id
        );
        
        $buffer = "";
        
        $functions_file = ACTIVE_SKIN_DIR . 'fragments/forms/functions.php';
        $header_file    = ACTIVE_SKIN_DIR . 'fragments/forms/head.php';
        $body_file      = ACTIVE_SKIN_DIR . 'fragments/forms/body.php';
        
        ob_start();
        if (file_exists($functions_file)) {
            include_once($functions_file);
        }
        if (file_exists($header_file)) {
            include_once($header_file);
        }
        if (file_exists($body_file)) {
            include_once($body_file);
        }
        $buffer = ob_get_contents();
        ob_end_clean();
        
        $this->html .= $buffer;
        
        $this->html .= '</div>'."\r\n";
        $this->html .= '</div>'."\r\n";
        $this->html .= '</td></tr>'."\r\n";
    }
        
}

?>
