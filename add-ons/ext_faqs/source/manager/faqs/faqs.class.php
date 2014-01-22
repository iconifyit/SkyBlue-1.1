<?php defined('SKYBLUE') or die('Bad File Request');

/**
* @version		v1.1 2009-04-12 11:50:00 $
* @package		SkyBlueCanvas
* @copyright	Copyright (C) 2005 - 2009 Scott Edwin Lewis. All rights reserved.
* @license		GNU/GPL, see COPYING.txt
* SkyBlueCanvas is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYING.txt for copyright notices and details.
*/

class faqs extends manager {

    function __construct() {
        $this->Init();
    }
    
    function faqs() {
        $this->__construct();
    }
    
    function AddEventHandlers() {
        $this->AddEventHandler('OnBeforeLoad','InitObjTypes');
        $this->AddEventHandler('OnBeforeLoad','InitObjFilter');
        $this->AddEventHandler('OnBeforeShow','DefineButtons');
        $this->AddEventHandler('OnBeforeSave','PrepareForSave');
        $this->AddEventHandler('OnBeforeViewItems', 'GroupIdToName');
    }

    function GroupIdToName() {
        global $Core;
        if ($this->objtype != 'faqs') return;
        $src = SB_XML_DIR.'faqgroups.xml';
        $groups = $this->GetObjects($src);
        for ($i=0; $i<count($this->objs); $i++) {
            $group = $Core->SelectObj($groups, $this->objs[$i]->group);
            $obj = &$this->objs[$i];
            $obj->group =  @$group->name;
        }
    }
    
    function PrepareForSave() {
        $this->AddFieldValidation('name','notnull');
		if (isset($_POST['answer'])) {
            $_POST['answer'] = $this->encode($_POST['answer']);
		}
    }
        
    function InitObjFilter() {
        $this->SetProp('filter', 'gid');
        $this->SetProp('filterprop', 'group');
    }
    
    function InitObjTypes() {
        $this->SetProp('objtypes', array('faqs', 'faqgroups'));
    }
    
    function InitProps() {
        if (empty($this->button) || strpos($this->button, 'view') !== false) {
            if ($this->objtype == 'faqs') {
                $this->SetProp('headings', array('FAQ', 'Group', 'Tasks'));
                $this->SetProp('tasks', array(
						'up:up_arrow.gif', 
						'down:down_arrow.gif',
						TASK_SEPARATOR, 
						'edit', 
						'delete'
					)
				);
                $this->SetProp('cols', array('name', 'group'));
            } 
            else {
                $this->SetProp('headings', array('Group', 'Tasks'));
                $this->SetProp('tasks', array('edit', 'delete'));
                $this->SetProp('cols', array('name'));
            }
        }
    }

    function DefineButtons() {
        global $Core;
        if ($this->showcancel != 1) {
            if ($this->objtype == 'faqs') {
                $add = ' FAQs';
                $view = ' FAQ Groups';
            } 
            else {
                $add = ' FAQ Groups';
                $view = ' FAQs';
            }
            $this->AddButton('Add'.$add);
            $this->AddButton('View'.$view);
        }
    }
    
    function Trigger() {
        global $Core;
        
        switch ($this->button) {
            case 'addfaqgroups':
            case 'editfaqgroups':
                $this->UpdateReferences('faqgroups');
                $this->AddButton('Save');
                $this->InitSkin();
                $this->InitEditor();
                $this->Edit();
                break;
                
            case 'addfaqs':
            case 'add':
            case 'edit':
            case 'editfaqs':
                $this->AddButton('Save');
                $this->InitSkin();
                $this->InitEditor();
                $this->Edit();
                break;
                
            case 'save':
                $this->UpdateReferences(
				    Filter::get($_POST, 'objtype', $this->objtype)
			    );
                $this->SaveItems();
                break;
                
            case 'delete':
            case 'deletefaqs':
                $this->UpdateReferences('faqs');
                $this->DeleteItem();
                break;
                
            case 'deletefaqgroups':
                $this->UpdateReferences('faqgroups');
                $this->DeleteItem();
                break;
                
            case 'cancel':
                $this->UpdateReferences(
				    Filter::get($_POST, 'objtype', $this->objtype)
				);
                $this->Cancel();
                break;
                
            case 'viewfaqgroups':
                $this->UpdateReferences('faqgroups');
                $this->InitProps();
                $this->ViewItems();
                break;
                
            case 'viewfaqs':
            case '':
                $this->UpdateReferences('faqs');
                $this->InitProps();
                $this->ViewItems();
                break;
                
            default:
                parent::Trigger();
                break;
        }
    }
    
    function initSkin() {
        global $Core;
        if ($this->objtype == 'faqs') {
            $this->skin = $Core->outputBuffer(
				str_replace('{objtype}',$this->objtype, SB_SKIN_FILE_PATH)
			);
        }
        else {
            $this->skin = $Core->outputBuffer(
                SB_MANAGERS_DIR . 'faqs/html/form.faqgroups.html'
		    );
        }
    }
    
    function InitEditor() {
        global $Core;
        global $config;
        
        // Set the form message
        
        $str = 'FAQ';
        if (strpos($this->objtype, 'group') !== false) {
            $str .= ' Group';
        }
        
        $this->SetFormMessage('name', $str);
        
        // Initialize the object properties to empty strings or
        // the properties of the object being edited
        
        $_OBJ = $this->InitObjProps($this->skin, $this->obj);
        
        // This step creates a $form array to pass to buildForm().
        // buildForm() merges the $obj properites with the form HTML.
        
        $form['ID']   = $this->GetItemID($_OBJ);
        $form['NAME'] = $this->GetObjProp($_OBJ,'name');
        
        if ($this->objtype == 'faqs') {
            $form['QUESTION'] = $this->GetObjProp($_OBJ,'question');
            $form['ANSWER']   = $this->getDecoded($_OBJ,'answer');
            $form['GROUP']    = $this->InitGroupSelector($_OBJ['group']);
        } 

        $form['ORDER'] = $Core->OrderSelector2($this->objs, 'name', $_OBJ['name']);
        
        $this->BuildForm($form);
    }
    
    function getDecoded($_OBJ, $prop, $default=null) {
        if (isset($_OBJ[$prop]) && !empty($_OBJ[$prop])) {
            return $this->decode($_OBJ[$prop]);
		}
		return $default;
	}

    function InitGroupSelector($group='') {
        global $Core;
        global $config;
        
        $src = SB_XML_DIR . 'faqgroups.xml';
        $groups = array();
        if (file_exists($src)) {
            $groups = $Core->xmlHandler->ParserMain($src);
        }
        $options = array();
		array_push($options, $Core->makeOption(' -- Select Group -- ', ''));
        foreach ($groups as $g) {
			array_push(
				$options, 
				$Core->makeOption($g->name, $g->id, $g->id == $group ? 1 : 0)
			);
        }
        return $Core->selectList($options, 'group');
    }
}