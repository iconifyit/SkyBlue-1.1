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

class articles extends manager
{

    function __construct() 
    {
        $this->Init();
    }
    
    function articles()
    {
        $this->__construct();
    }
    
    function AddEventHandlers()
    {
        $this->AddEventHandler('OnBeforeLoad','InitObjTypes');
        $this->AddEventHandler('OnBeforeShow','DefineButtons');
        $this->AddEventHandler('OnBeforeSave', 'PrepareForSave');
    }
    
    function PrepareForSave()
    {
        global $Core;
        
        $this->AddFieldValidation('name','notnull');
        if ($this->objtype == 'articles')
        {
            $this->AddFieldValidation('intro','notnull');
             $this->AddFieldValidation('author','notnull');
             $this->AddFieldValidation('date','notnull');
            $this->SaveStoryText();
            
            $_POST['intro'] = base64_encode(stripslashes(urldecode($_POST['intro'])));
            unset($_POST['text']);
            if ($Core->GetVar($_POST, 'image', null) != null)
            {
                $_POST['image'] = 
                    str_replace(SB_SITE_DATA_DIR, null, $Core->GetVar($_POST, 'image', null));
            }
        }
    }
    
    function SaveStoryText()
    {
        global $Core;
        $this->SaveStory(
            $Core->GetVar($_POST,'story', $this->GetStoryFileName()), 
            stripslashes(urldecode($_POST['text'])));
        unset($_POST['text']);
    }
    
    function InitObjTypes()
    {
        $this->SetProp('objtypes', array('articles', 'articlesgroups'));
    }
    
    function InitProps() 
    {
        if (empty($this->button) ||
             strpos($this->button, 'view') !== FALSE)
        {
            if ($this->objtype == 'articles')
            {
                $this->SetProp('headings', array('Name', 'Tasks'));
                $this->SetProp('tasks', array('edit', 'delete'));
                $this->SetProp('cols', array('name'));
            } 
            else 
            {
                $this->SetProp('headings', array('Category', 'Tasks'));
                $this->SetProp('tasks', array('edit', 'delete'));
                $this->SetProp('cols', array('name'));
            }
        }
    }
    
    function ResetTableHeadings()
    {
        $this->SetProp('headings', null);
        $this->SetProp('tasks', null);
        $this->SetProp('cols', null);
        $this->InitProps();
    }
    
    function GroupIDtoName()
    {
        global $Core;
        
        $file = SB_XML_DIR.'articlesgroups.xml';
        if (file_exists($file))
        {
            $groups = $Core->xmlHandler->ParserMain($file);
            for ($i=0; $i<count($this->objs); $i++)
            {
                if (!isset($this->objs[$i]->articlesgroups))
                {
                    $this->objs[$i]->articlesgroups = null;
                }
                $myGroups = explode(',', $this->objs[$i]->articlesgroups);
                $this->objs[$i]->articlesgroups = null;
                $grpnames = array();
                for ($j=0; $j<count($myGroups); $j++)
                {
                    $myGroups[$j] = trim($myGroups[$j]);
                    $grp = $Core->SelectObj($groups, $myGroups[$j]);
                    if (isset($grp->name))
                    {
                        $grpnames[] = $grp->name;
                    } else {
                        $grpnames[] = $myGroups[$j];
                    }
                }
                $this->objs[$i]->articlesgroups = implode(', ', $grpnames);
            }
        }
    }

    function DefineButtons()
    {
        global $Core;
        if ($this->showcancel != 1)
        {
            if ($this->objtype == 'articles')
            {
                $add = ' Item';
                $view = ' Groups';
            } 
            else 
            {
                $add = ' Group';
                $view = ' Items';
            }
            $this->AddButton('Add'.$add);
            $this->AddButton('View'.$view);
        }
    }
    
    function Trigger()
    {
        global $Core;
        
        $this->InitProps();
        
        switch ($this->button) 
        {
            case 'addgroup':
            case 'addarticlesgroups':
            case 'editarticlesgroups':
                $this->UpdateReferences('articlesgroups');
                $this->showcancel = 1;
                $this->AddButton('Save');
                $this->InitSkin();
                $this->InitEditor();
                $this->Edit();
                break;
                
            case 'additem':
            case 'editarticles':
            case 'edititem':
            case 'add':
            case 'edit':
                $this->UpdateReferences('articles');
                $this->showcancel = 1;
                $this->AddButton('Save');
                $this->InitSkin();
                $this->InitEditor();
                $this->Edit();
                break;
                
            case 'save':
                $this->UpdateReferences(
                    $Core->GetVar($_POST,'objtype',$this->objtype));
                $this->SaveItems();
                break;
                
            case 'delete':
            case 'deleteitems':
            case 'deletearticles':
                $this->UpdateReferences('articles');
                $this->DeleteItem();
                break;
                
            case 'deletearticlesgroups':
                $this->UpdateReferences('articlesgroups');
                $this->DeleteItem();
                break;
                
            case 'cancel':
                $this->UpdateReferences(
                    $Core->GetVar($_POST,'objtype',$this->mgr));
                $this->Cancel();
                break;
                
            case 'viewgroups':
                $this->showcancel = 0;
                $this->UpdateReferences('articlesgroups');
                $this->ResetTableHeadings();
                $this->ViewItems();
                break;
            
            case 'view':
            case 'viewitems':
            case 'viewarticles':
            default:
                $this->UpdateReferences('articles');
                $this->GroupIDtoName();
                $this->showcancel = 0;
                $this->ViewItems();
                break;
                
            default:
                $this->UpdateReferences(
                    $Core->GetVar($_GET,'objtype',$this->mgr));
                if ($this->objtype == 'articles')
                {
                    $this->GroupIDtoName();
                }
                $this->showcancel = 0;
                $this->ViewItems();
                break;
        }
    }
    
    function InitObjType()
    {
        global $Core;
        
        // Set $this->objtype to its lowest priority value 'mgr'
        // At the very least, $this->objtype will have a default value
        
        $this->objtype = $this->mgr;
        
        // Next, check the query string
        
        $this->objtype = $Core->GetVar($_GET, 'objtype', $this->mgr);
        
        // The button pressed has highest priority
        // Check the $objtypes first for matches with the button
        // and over-ride any previously set values
        
        if (strpos($this->button, 'group') !== FALSE)
        {
            $this->objtype = 'articlesgroups';
        } 
        else if ($this->button == 'Cancel'|| $this->button == 'save') 
        {
            $this->objtype = $Core->GetVar(
                $_POST, 'objtype', $this->objtype);
        } 
        else 
        {
            $this->objtype = 'articles';
        }
        
    }
    
    function ResetRedirect($objtype, $sub=null)
    {
        $this->redirect  = BASE_PAGE.'?mgroup='.$this->mgroup;
        $this->redirect .= '&mgr='.$this->mgr;
        $this->redirect .= '&objtype='.$objtype;
        $this->redirect .= !empty($sub) ? '&sub='.$sub : null ;
    }
    
    function InitSkin()
    {
        global $Core;
        $this->skin = $Core->OutputBuffer(
            SB_MANAGERS_DIR.$this->mgr.'/html/form.'.$this->objtype.'.html');
    }
    
    function InitEditor() 
    {
        global $Core;
        
        // Set the form message
        
        $this->SetFormMessage('title', 'Article');
        /*
        
        if (strpos($this->button, 'edit') !== FALSE || 
             strpos($this->button, 'add') !== FALSE)
        {
            $objtype = null;
            if ($this->objtype == 'articles')
            {
                $objtype = 'New Item';
            } 
            else if ($this->objtype == 'articlesgroups')
            {
                $objtype = 'New Group';
            }
            $itemname = 
                isset($this->obj->name) ? $this->obj->name : $objtype ;
            if (!empty($itemname))
            {
                $Core->MSG = '<h2 class="message">'.$itemname.'</h2>';
            }
        }
        */
        
        // Initialize the object properties to empty strings or
        // the properties of the object being edited
        
        $_OBJ = $this->InitObjProps($this->skin, $this->obj);
        
        // This step creates a $form array to pass to BuildForm().
        // BuildForm() merges the $obj properites with the form HTML.
        
        $form['ID']   = $this->GetItemID($_OBJ);
        $form['NAME'] = $this->GetObjProp($_OBJ, 'name', null);
        $form['ORDER'] = $this->GetObjOrder($_OBJ, 'name');
        
        if ($this->objtype == 'articles')
        {
            if (!isset($_OBJ['groups']))
            {
                $_OBJ['groups'] = null;
            }
            $form['AUTHOR'] = $this->GetObjProp($_OBJ, 'author', null);
            $form['DATE']   = $this->GetObjProp($_OBJ, 'date', date('d-m-Y H:i:s',time()));
            $form['URL']    = $this->GetObjProp($_OBJ, 'url', null);
            $form['INTRO']  = $this->Decode($_OBJ,'intro');
            $form['TEXT']   = $this->GetStoryContent($_OBJ); // "";
            $form['GROUPS'] = $this->ArticleGroupSelector($_OBJ['groups']);
            $form['STORY']  = $this->GetStoryFileName();
        } 
        else 
        {
            $form['ITEMS'] = $this->ArticleItemList();
        }

        $this->BuildForm($form);
    }
    
    function GetStoryContent($obj)
    {
        global $Core;
        if (!isset($obj['story'])) return null;
        if (!file_exists(SB_STORY_DIR . $obj['story']) || empty($obj['story'])) return null;
        return $Core->SBReadFile(SB_STORY_DIR . $obj['story']);
    }

    function ArticleItemList()
    {
        global $Core;
        
        $objs = array();
        $file = SB_XML_DIR.'articles.xml';
        if (!file_exists($file))
        {
            return null;
        }
        $objs = $Core->xmlHandler->ParserMain($file);
        
        $items = '<ul>'."\r\n";
        foreach($objs as $obj)
        {
            if (!isset($obj->groups))
            {
                $obj->groups = null;
            }
            $grps = explode(',', $obj->groups);
            for ($i=0; $i<count($grps); $i++)
            {
                if (trim($grps[$i]) == $this->id)
                {
                    $items .= '<li>'.$obj->name.'</li>'."\r\n";
                }
            }
        }
        $items .= '</ul>'."\r\n";
        return $items;
    }
    
    function ArticleGroupSelector($groups=null)
    {
        global $Core;
        
        $groups = explode(',', $groups);
        for ($i=0; $i<count($groups); $i++)
        {
            $groups[$i] = trim($groups[$i]);
        }
        
        $objs = array();
        $file = SB_XML_DIR.'articlesgroups.xml';
        if (!file_exists($file))
        {
            return null;
        }
        
        $objs = $Core->xmlHandler->ParserMain($file);
        
        if (!count($objs))
        {
            return NO_GROUPS_STRING;
        }
        
        $selector = '<ul>'."\r\n";
        foreach ($objs as $obj)
        {
            $selector .= '<li>'."\r\n";
            $selector .= '<input type="checkbox" ';
            $selector .= 'name="groups[]" ';
            $selector .= 'value="'.$obj->id.'" ';
            if (in_array($obj->id, $groups))
            {
                $selector .= 'checked="checked" ';
            }
            $selector .= '/>&nbsp;';
            $selector .= $obj->name."\r\n";
            $selector .= '</li>'."\r\n";
        }
        $selector .= '</ul>'."\r\n";
        return  $selector;
    }
    
}

?>