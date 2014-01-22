<?php

/**
* @version        v1.1 2008-12-12 19:47:43 $
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

class SkinController extends Controller {

    var $model;
    var $action;
    var $view_path = 'managers/installer/mvc/views/';

    function __construct($Request) {
        parent::__construct($Request);
        $this->addActionHandler('index',   'doIndex');
        $this->addActionHandler('delete',  'doDelete');
        $this->addActionHandler('install', 'doSave');
    }
    
    function SkinController($Request) {
        $this->__construct($Request);
    }
    
    function display() {
        $this->view->assign('view.name', 'Dashboard');
        $this->view->setView('dashboard');
        parent::display();
    }

    function doIndex() {
        $this->model->index();
    }
    
    function doDelete($Request) {
        $item = $Request->get('item', null);
        $this->model->index();
        if (count($this->model->getData()) == 1) {
            $this->_setMessage(
                'error',
                'Oops!',
                "You cannot delete the selected skin because it is the only one installed."
            );
        }
        else if ($this->model->delete($item)) {
            $this->_setMessage(
                'success',
                'Success!',
                $item . " Skin was successfully deleted."
            );
        }
        else {
            $this->_setMessage(
                'error',
                'Oops!',
                $item . " Skin could not be deleted."
            );
        }
        Core::SBRedirect(INSTALLER_URL . "&com=skin");
    }
    
    function doSave($Request) {
        $Filter = new Filter;
        $package = @$_FILES['package'];
        $item = $Filter->get($package, 'name', null);
        if ($this->model->save($package)) {
            $this->_setMessage(
                'success',
                'Success!',
                $item . " Skin was successfully installed."
            );
        }
        else {
            $this->_setMessage(
                'error',
                'Oops!',
                $item . " Skin could not be installed."
            );
        }
        Core::SBRedirect(INSTALLER_URL . "&com=skin");
    }

}