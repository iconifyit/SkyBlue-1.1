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

class Portfolio extends manager
{

    var $hasmodule = false;
    var $storyfile = null;
    
    function __construct() 
    {
        $this->Init();
    }
    
    function Portfolio()
    {
        $this->__construct();
    }
    
    function AddEventHandlers()
    {
        $this->AddEventHandler('OnBeforeLoad', 'InitDirs');
        $this->AddEventHandler('OnBeforeLoad', 'InitObjTypes');
        $this->AddEventHandler('OnBeforeShow', 'DefineButtons');
        $this->AddEventHandler('OnBeforeSave', 'PrepareForSave');
    }
    
    function PrepareForSave()
    {
        $this->AddFieldValidation('title','notnull');
        $this->SaveDescription();
    }
    
    function InitDirs()
    {
        global $Core;
        if (!$Core->InitDir(SB_MEDIA_DIR.'portfolio/'))
        {
            $Core->FileNotFound(
                SB_MEDIA_DIR.'portfolio/', 
                __LINE__, 
                __FILE__.'::InitEditor()' 
            );
        }
        if (!$Core->InitDir(SB_MEDIA_DIR.'thumbnails/'))
        {
            $Core->FileNotFound(
                SB_MEDIA_DIR.'thumbnails/', 
                __LINE__, 
                __FILE__.'::InitEditor()' 
            );
        }
    }
    
    function getReferrer()
    {
        global $Core;
        return str_replace(
            '&amp;', '&', $Core->GetVar($_POST, 'referrer', FALSE));
    }
    
    function InitObjFilter()
    {
        $this->SetProp('filter', 'show');
        $this->SetProp('filterprop', 'category');
    }
    
    function addFilterVar()
    {
        global $Core;
        $filterVar = NULL;
        if (isset($this->filter) && !empty($this->filter))
        {
            $filter = $Core->GetVar($_GET, $this->filter, NULL);
            if (!empty($filter))
            {
                $filterVar = '&'.$this->filter.'='.$filter;
            }
        }
        return $filterVar;
    }
    
    function InitObjTypes()
    {
        $this->SetProp(
            'objtypes', 
            array('portfolio', 'category', 'settings')
        );
    }
    
    function InitProps() 
    {
        if (empty($this->button) ||
             strpos($this->button, 'view') !== FALSE)
        {
            if ($this->objtype == 'portfolio')
            {
                $this->SetProp('headings', array('Name', 'Category', 'Tasks'));
                $this->SetProp('tasks', array('edit', 'delete'));
                $this->SetProp('cols', array('title', 'category'));
            } 
            else 
            {
                $this->SetProp('headings', array('Category', 'Tasks'));
                $this->SetProp('tasks', array('edit', 'delete'));
                $this->SetProp('cols', array('title'));
            }
        }
    }
    
    function categoryIDtoName()
    {
        global $Core;

        $file = SB_XML_DIR.'portfolio/category.xml';
        if (file_exists($file))
        {
            $categories = $Core->xmlHandler->ParserMain($file);
            for ($i=0; $i<count($this->objs); $i++)
            {
                $cat = 
                    $Core->SelectObj($categories, $this->objs[$i]->category);
                if (isset($cat->id))
                {
                      $this->objs[$i]->category = $cat->title;
                }
            }
        }
    }
    
    function InitDataSource()
    {
        global $Core;
        
        $dir = SB_XML_DIR.strtolower(__CLASS__).'/';
        if (!is_dir($dir))
        {
            mkdir($dir);
        }
        
        $this->datasrc = $dir.$this->objtype.'.xml';
        if (!file_exists($this->datasrc))
        {
            $xml = 
                $Core->xmlHandler->ObjsToXML($this->objs, $this->objtype);
            $Core->WriteFile($this->datasrc, $xml, 1);
        }
    }
    
    function DefineButtons()
    {
        global $Core;
        if ($this->showcancel != 1)
        {
            if ($this->objtype == 'portfolio')
            {
                $add = ' Item';
                $view = ' Categories';
            } 
            else 
            {
                $add = ' Category';
                $view = ' Items';
            }
            
            $this->AddButton('Add'.$add);
            $this->AddButton('View'.$view);
            
            if ($this->objtype == 'portfolio')
            {
                $this->AddButton('Settings');
            }
        }
    }

    function Trigger()
    {
        global $Core;
        
        $this->InitProps();
        
        switch ($this->button) 
        {
            case 'settings':
                $this->UpdateReferences('settings');
                $this->showcancel = 1;
                $this->AddButton('Save');
                $this->InitSkin();
                $this->InitConfigeditor();
                $this->Edit();
                break;
        
            case 'addcategory':
            case 'editcategory':
                $this->UpdateReferences('category');
                $this->showcancel = 1;
                $this->AddButton('Save');
                $this->InitSkin();
                $this->InitEditor();
                $this->Edit();
                break;
                
            case 'additem':
            case 'edititem':
            case 'editportfolio':
            case 'add':
            case 'edit':
                $this->UpdateReferences('portfolio');
                $this->showcancel = 1;
                $this->AddButton('Save');
                $this->InitSkin();
                $this->InitEditor();
                $this->Edit();
                break;
                
            case 'save':
                if (DEMO_MODE) $Core->ExitDemoEvent($this->redirect);
                $this->UpdateReferences(
                    $Core->GetVar($_POST,'objtype',$this->mgr));
                if ($this->objtype == 'settings')
                {
                    $this->redirect = MGR_DEFAULT_REDIRECT;
                }
                $this->redirect .= $this->addFilterVar();
                $this->SaveItems();
                break;
                
            case 'delete':
            case 'deleteitems':
            case 'deleteportfolio':
                if (DEMO_MODE) $Core->ExitDemoEvent($this->redirect);
                $this->UpdateReferences('portfolio');
                $this->redirect .= $this->addFilterVar();
                $this->DeleteItem();
                break;
                
            case 'deletecategory':
                if (DEMO_MODE) $Core->ExitDemoEvent($this->redirect);
                $this->UpdateReferences('category');
                $this->redirect .= $this->addFilterVar();
                $this->DeleteItem();
                break;
                
            case 'cancel':
                if ($Core->GetVar($_POST,'objtype',$this->mgr) != 'settings')
                {
					$this->UpdateReferences(
						$Core->GetVar($_POST,'objtype',$this->mgr));
					$this->redirect .= $this->addFilterVar();
                }
                else
                {
                    $this->redirect = MGR_DEFAULT_REDIRECT;
                }
                $this->Cancel();
                break;
                
            case 'viewcategory':
            case 'viewcategories':
                $this->showcancel = 0;
                $this->UpdateReferences('category');
                $this->Cancel(-1);
                $this->ViewItems();
                break;
                
            case 'viewitems':
                $this->UpdateReferences('portfolio');
                $this->filterObjs();
                $this->categoryIDtoName();
                $this->showcancel = 0;
                $this->ViewItems();
                break;
                
            default:
                $this->UpdateReferences(
                    $Core->GetVar($_GET,'objtype',$this->mgr));
                if ($this->objtype == 'portfolio')
                {
                    $this->filterObjs();
                    $this->categoryIDtoName();
                }
                $this->showcancel = 0;
                $this->ViewItems();
                break;
        }
    }
    
    function InitSkin()
    {
        global $Core;
        $this->skin = $Core->OutputBuffer(
            SB_MANAGERS_DIR . 'portfolio/html/form.'.$this->objtype.'.html'
        );
    }
    
    function InitConfigeditor() 
    {
        global $Core;
        
        if (count($this->objs))
        {
            $this->obj = $this->objs[0];
        }
        
        $Core->MSG = str_replace('{msg}', 'Gallery Settings', SB_MSG_EDIT);
        
        // Initialize the object properties to empty strings or
        // the properties of the object being edited
        
        $_OBJ = $this->InitObjProps($this->skin, $this->obj);
        
        // This step creates a $form array to pass to BuildForm().
        // BuildForm() merges the $obj properites with the form HTML.
        
        $form['ID'] = $_OBJ['id'];
        $form['REFERRER'] = $this->getQueryString();
        $form['NAVIGATION'] = $this->getNavSelector(
            $this->GetObjProp($_OBJ, 'navigation', 'thumbnails'));
        $form['STORY'] = $this->GetStoryFileName();
        $form['THUMBCOLS'] = $this->GetObjProp($_OBJ, 'thumbcols', 3);
        $form['SKIN'] = $this->getSkins($this->GetObjProp($_OBJ, 'skin', null));

        $this->BuildForm($form);
    }
    
    function getQueryString()
    {
        global $Core;
        
        $query = NULL;
        
        $i=0;
        foreach ($_GET as $k=>$v)
        {
            $amp = $i > 0 ? '&' : NULL ;
            $query .= $amp.$k.'='.$Core->GetVar($_GET, $k, NULL);
            $i++;
        }
        return BASE_PAGE.'?'.$query;
    }
    
    function getHTTPReferrer()
    {
        global $Core;
        return $_SERVER['REQUEST_URI'];
    }
    
    function getSkins($selected=NULL)
    {
        global $Core;
        
        $dir = ACTIVE_SKIN_DIR.'portfolio/';
        if (file_exists($dir))
        {
            $files = $Core->ListFiles($dir);
            if (count($files))
            {
                return $this->skinSelector($files, $selected);
            }
        } else {
            return NULL;
        }
    }
    
    function skinSelector($files, $selected=NULL)
    {
        global $Core;
        
        $options = array();
        $options[] = $Core->MakeOption(' -- Select Skin -- ', NULL);
        for ($i=0; $i<count($files); $i++)
        {
            $files[$i] = str_replace(SB_SITE_DATA_DIR, null, $files[$i]);
            $s = basename($selected) == basename($files[$i]) ? 1 : 0 ;
            $options[] = $Core->MakeOption(basename($files[$i]), $files[$i], $s);
        }
        return $Core->SelectList($options, 'skin');
    }
    
    function getNavSelector($selected)
    {
        global $Core;
        
        $opts = array(
        'thumbnails'=>'Thumbnails',
        'slideshow'=>'Slideshow',
        'numbers'=>'Numbered Links',
        'title'=>'Item Titles'
       );
        return $Core->SelectList($Core->SelectorOptions($opts, $selected), 'navigation');
    }
    
    function InitEditor() 
    {
        global $Core;

        // Set the form message
        
        $this->SetFormMessage(
			'title', 
			ucwords($this->objtype) == 'Portfolio' ? 
				'Gallery Item' : ucwords($this->objtype)
		);
        
        // Initialize the object properties to empty strings or
        // the properties of the object being edited
        
        $_OBJ = $this->InitObjProps($this->skin, $this->obj);
        
        // This step creates a $form array to pass to BuildForm().
        // BuildForm() merges the $obj properites with the form HTML.
        
        $form['ID'] = 
            isset($_OBJ['id']) ? 
            $_OBJ['id'] : $Core->GetNewID($this->objs);
        $form['TITLE'] = 
            isset($_OBJ['title']) ? $_OBJ['title'] : NULL ;
        
        $js = ' onchange="showImagePreview(\'previewthumb\', this);" ';
        $form['THUMBNAIL'] = 
            $Core->ImageSelector(
                'thumbnail', 'thumbnails/', 
                $_OBJ['thumbnail'], $js);
        $form['THUMBIMG'] = 
            $this->previewImage(
                $_OBJ['thumbnail'], 'previewthumb');
        
        $js = ' onchange="showImagePreview(\'previewart\', this);" ';
        $form['ARTWORK'] = 
            $Core->ImageSelector(
                'artwork', 'portfolio/', $_OBJ['artwork'], $js);
        $form['ARTIMG'] = 
            $this->previewImage(
                $_OBJ['artwork'], 'previewart');
        
        $form['DESCRIPTION'] = $this->GetStoryContent($_OBJ); // "";
		$form['STORY'] = $this->GetStoryFileName();
        
        if ($this->objtype == 'portfolio')
        {
            $_OBJ['category'] = 
                isset($_OBJ['category']) ? $_OBJ['category'] : NULL ;
            $form['CATEGORY'] = 
                $this->categorySelector($_OBJ['category']);
            $_OBJ['parent'] = 
                isset($_OBJ['parent']) ? $_OBJ['parent'] : NULL ;
            $form['PARENT'] = 
                $this->getParentSelector($_OBJ);
            $form['LINK'] = 
                isset($_OBJ['link']) ? $_OBJ['link'] : NULL ;
        }

        $form['ORDER'] = 
            $Core->OrderSelector2($this->objs, 'title', $_OBJ['title']);
        $this->BuildForm($form);
    }
    
    function _GetStoryContent($obj)
    {
        global $Core;
        if (!isset($obj['story'])) return null;
        if (!file_exists(SB_STORY_DIR . $obj['story']) || empty($obj['story'])) return null;
        return $Core->SBReadFile(SB_STORY_DIR . $obj['story']);
    }

    function SaveDescription()
    {
        global $Core;
        $this->SaveStory(
            $Core->GetVar($_POST,'story', $this->GetStoryFileName()), 
            stripslashes(urldecode($_POST['description'])));
        unset($_POST['description']);
    }
    
    function getParentSelector($item)
    {
        global $Core;
        
        $options = array();
        $options[] = $Core->MakeOption(' -- Select Parent -- ', NULL);
        foreach ($this->objs as $obj)
        {
            if ($obj->id != $item['id'] &&
                 (!isset($obj->parent) || empty($obj->parent))
              )
            {
                $s = isset($item['parent']) && 
                        $item['parent'] == $obj->id ? 1 : 0 ;
                $options[] = $Core->MakeOption($obj->title, $obj->id, $s);
            }
        }
        return $Core->SelectList($options, 'parent');
    }
    
    function previewImage($src, $id)
    {
        global $Core;
        
        $attrs = array();
        $attrs['src'] = $src;

        if (!file_exists($attrs['src']) || is_dir($attrs['src']))
        {
            $attrs['src'] = CAMERA_ICON_GIF;
        }
        $attrs['id']      = $id;
        list($attrs['width'], $attrs['height']) = 
                    $Core->ImageDimsToMaxDim(
                    array($Core->ImageWidth($attrs['src']),
                           $Core->ImageHeight($attrs['src'])) ,
                           72, 72);

        $attrs['alt']     = 'preview of '.basename($attrs['src']);
        return $Core->HTML->MakeElement('img', $attrs, '');
    }
    
    function photoSelector($photo='')
    {
        global $Core;
        return $Core->ImageSelector('photo', 'pages', $photo);
    }
    
    function categorySelector($selected='')
    {
        global $Core;
        
        $objs = array();
        $file = SB_XML_DIR.'portfolio/category.xml';
        if (file_exists($file))
        {
            $objs = $Core->xmlHandler->ParserMain($file);
        }
        
        $options = array();
        $options[] = $Core->MakeOption(' -- Select Category -- ', NULL);
        foreach ($objs as $obj)
        {
            $s = $obj->id == $selected ? 1 : 0 ;
            $options[] = $Core->MakeOption($obj->title, $obj->id, $s);
        }
        return  $Core->SelectList($options, 'category');
    }
    
}

?>

