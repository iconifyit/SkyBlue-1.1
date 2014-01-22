<?php

/**
* @version    v1.1 2008-12-12 19:47:43 $
* @package    SkyBlueCanvas
* @copyright  Copyright (C) 2005 - 2008 Scott Edwin Lewis. All rights reserved.
* @license    GNU/GPL, see COPYING.txt
* SkyBlueCanvas is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYING.txt for copyright notices and details.
*/

defined('SKYBLUE') or die(basename(__FILE__));

class ModuleDAO {

    var $data;
    var $directory;

    function __construct() {
        $this->setDirectory(SB_USER_MODS_DIR);
    }

    function ModuleDAO() {
        $this->__construct();
    }
    
    function setDirectory($directory) {
        $this->directory = $directory;
    }
    
    function save($package) {
    
        $Filter = new Filter;
        
        $name = $this->getName($Filter->get($package, 'name', null));
        
        if ($Filter->get($package, 'error', false)) {
            // An HTTP error occurred
            return false;
        }
        else if (empty($name)) {
            // An empty file name was posted
            return false;
        }
        else if ($this->exists($name)) {
            return false;
        }
        
        $ini = FileSystem::read_config(
            SB_MANAGERS_DIR . "installer/config.php"
        );
        $Uploader = new Uploader(
            isset($ini['mimes']) ? $ini['mimes'] : array(),
            array(SB_TMP_DIR)
        );
        
        list($result, $tmpfile) = $Uploader->upload($package, SB_TMP_DIR);
        
        if (intval($result) != 1) {
            // The file was not uploaded
            return false;
        }
        
        if ($this->unzip($tmpfile, $this->directory)) {
            return true;
        }
        unlink($tmpfile);
        return false;
    }
    
    function getName($zip) {
        $name = null;
        $bits = explode('.', $zip);
        return implode('.', array_slice($bits, 0, 3));
    }
    
    function unzip($pkg, $dir) {
        global $Core;

        if (!file_exists($pkg)) {
            return false;
        }
        $unzipOk = $Core->Unzip($pkg, $dir);
        FileSystem::delete_file($pkg);
        return $unzipOk;
    }
        
    function delete($name) {
        if ($this->exists($name)) {
            return FileSystem::delete_file($this->directory . $name, false);
        }
        return false;
    }
    
    function index() {
        $data = FileSystem::list_files($this->directory, 0);
        $this->data = array();
        for ($i=0; $i<count($data); $i++) {
            array_push($this->data, basename($data[$i]));
        }
    }
    
    function getData() {
        return $this->data;
    }
    
    function exists($name) {
        return (!empty($name) && file_exists($this->directory . $name));
    }
    
}

?>