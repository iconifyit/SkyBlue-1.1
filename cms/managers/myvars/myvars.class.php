<?php

/**
* @version		RC 1.0.3.2 2008-04-24 15:03:43 $
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

class myvars extends manager {

    function __construct() {
        $this->init();
    }

    function myvars() {
        $this->__construct();
    }
    
    function AddEventHandlers() {
        $this->AddEventHandler('OnBeforeSave','PrepareForSave');
        $this->AddEventHandler('OnBeforeViewItems', 'PrepareListView');
    }
    
    function PrepareListView() {
        if (count($this->objs)) {
            for ($i=0; $i<count($this->objs); $i++) {
                $obj =& $this->objs[$i];
				if (!empty($obj->name)) {
					$obj->name = base64_decode($obj->name);
				}
				if (!empty($obj->value)) {
					$obj->value = base64_decode($obj->value);
				}
            }
        }
    }
    
    function PrepareForSave() {
        $this->AddFieldValidation('name','notnull');
        $this->AddFieldValidation('value', 'notnull');
        $this->AddFieldValidation('var_type', 'notnull');
        $this->EncodeValues();
    }
    
    function EncodeValues() {
        $_POST['name']  = base64_encode($_POST['name']);
        $_POST['value'] = base64_encode($_POST['value']);
    }
    
    function InitProps() {
        $this->SetProp('headings', array('Name', 'Value', 'Type', 'Tasks'));
        $this->SetProp('cols', array('name', 'value', 'var_type'));
        $this->SetProp('tasks', array('edit', 'delete'));
    }

    function InitEditor() {
        global $Core;
        
        // Set the form message
        
        $name = Filter::get($this->obj, 'name', '');
        if (!empty($name)) {
            $name = base64_decode($name);
            $this->obj->name = $name;
        }
        
        $this->SetFormMessage('name', 'Variable');
        
        // Initialize the object properties to empty strings or
        // the properties of the object being edited
        
        $_OBJ = $this->InitObjProps($this->skin, $this->obj);
        
        // This step creates a $form array to pass to buildForm().
        // buildForm() merges the $obj properites with the form HTML.
        
        $form['ID']            = $this->GetItemID($_OBJ);
        $form['NAME']          = $name; // $this->DecodeValue($_OBJ, 'name');
        $form['VALUE']         = $this->DecodeValue($_OBJ, 'value');
        $form['VAR_TYPE']      = $this->GetTypes($this->GetObjProp($_OBJ, 'var_type'));
        $this->BuildForm($form);
    }
    
    function DecodeValue($_OBJ, $key) {
        $value = $this->GetObjProp($_OBJ, $key);
        if (!empty($value)) {
            return base64_decode($value);
        }
        return null;
    }
    
    function GetTypes($selected) {
        global $Core;
        
        $options = "";
        
        $attrs = array('value'=>'variable');
        if ($selected == 'variable') {
            $attrs['selected'] = 'selected';
        }
        $options .= $Core->HTML->MakeElement(
                'option',
                $attrs,
                ' Variable '
        );
        
        $attrs = array('value'=>'string');
        if ($selected == 'regex') {
            $attrs['selected'] = 'selected';
        }
        $options .= $Core->HTML->MakeElement(
                'option',
                $attrs,
                ' String '
        );
        
        $attrs = array('value'=>'regex');
        if ($selected == 'regex') {
            $attrs['selected'] = 'selected';
        }
        $options .= $Core->HTML->MakeElement(
                'option',
                $attrs,
                ' Regular Expression '
        );
        return $Core->HTML->MakeElement(
            'select',
            array(
                'name' => 'var_type'
            ),
            $Core->HTML->MakeElement(
            	'option',
            	array(
            	    'value' => ''
            	),
            	' -- Choose Type -- '
            ) . 
            $options
        );

    }
    
}

?>
