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

ini_set('error_reporting', E_ALL);
ini_set('display_errors', 'On');

define('SKYBLUE', 1);
define('BASE_PAGE', 'index.php');
define('SETUP_PATH_TO_ROOT', '../');

define('SETUP_USERNAME_LENGTH', 4);
define('SETUP_PASSWORD_LENGTH', 4);

define('SETUP_PASSWORD_NOT_SAVED', 
    'You username and password could not be saved.');

define('SETUP_USERNAME_NULL',      
    'You did not provide a username.');

define('SETUP_USERNAME_TOO_SHORT',      
    'Your username must be at least ' . SETUP_USERNAME_LENGTH . ' characters long.');

define('SETUP_PASSWORD_NULL',      
    'You did not provide a password.');

define('SETUP_PASSWORD_TOO_SHORT',      
    'Your password must be at least ' . SETUP_PASSWORD_LENGTH . ' characters long.');

define('SETUP_PASSWORD_MISMATCH',  
    'Your password confirmation did not match the password you entered.');

define('SETUP_URL_LOGIN',       '../admin.php');
define('SETUP_HTML_SKIN',       '../ui/admin/html/skin.index.html');
define('SETUP_HTML_NO_INSTALL', "html/no.install.html");
define('SETUP_HTML_CONFIG_TABLE', "html/config.table.html");
define('SETUP_HTML_URL',        'html/address.html');
define('SETUP_HTML_PASSWORD',   'html/password.html');
define('SETUP_HTML_FINISH',     'html/finish.html');
define('SETUP_TOKEN_ERROR',     '{error}');
define('SETUP_KEY_USERNAME',    'username');
define('SETUP_KEY_PASSWORD',    'password');
define('SETUP_KEY_SAVEPASS',    'savepassword');
define('SETUP_KEY_SAVEURL',     'saveurl');
define('SETUP_KEY_SHOWURL',     'urlpage');
define('SETUP_KEY_FINISH',      'finish');
define('SETUP_KEY_ERROR',       'error');
define('SETUP_KEY_MSG',         'message');
define('SETUP_KEY_EVENT',       'event');
define('SETUP_KEY_START',       'start');
define('SETUP_STR_LOGIN',       'login');
define('SETUP_TYPE_PASSWORD',   'login');
define('SETUP_KEY_CONFIRM_PASSWORD', 'confirmpassword');

define('SETUP_URL_START', 
    BASE_PAGE
);


define('SETUP_URL_PASSWORD', 
    BASE_PAGE . '?' . SETUP_KEY_EVENT . '=' . SETUP_KEY_PASSWORD
);

define('SETUP_URL_FINISH',   
    BASE_PAGE . '?' . SETUP_KEY_EVENT . '=' . SETUP_KEY_FINISH
);
    
define('SETUP_URL_URL', 
    BASE_PAGE . '?' . SETUP_KEY_EVENT . '=' . SETUP_KEY_SHOWURL
);

require_once(SETUP_PATH_TO_ROOT . 'includes/object.class.php');
require_once(SETUP_PATH_TO_ROOT . 'includes/observer.class.php');
require_once(SETUP_PATH_TO_ROOT . 'includes/error.class.php');
require_once(SETUP_PATH_TO_ROOT . 'includes/conf.functions.php');
require_once(SETUP_PATH_TO_ROOT . 'includes/filter.php');
require_once(SETUP_PATH_TO_ROOT . 'includes/filesystem.php');
require_once(SETUP_PATH_TO_ROOT . 'includes/core.php');
require_once(SETUP_PATH_TO_ROOT . 'includes/skin.class.php');
require_once(SETUP_PATH_TO_ROOT . 'includes/factory.bundle.php');
require_once(SETUP_PATH_TO_ROOT.'includes/request.php');

$Core   = new Core(array('path'=>SETUP_PATH_TO_ROOT));
$config = $Core->LoadConfig();

new SetupWizard();

class SetupWizard {
    var $event;
    var $html;
    var $error;
    var $config;
    var $IsError;
    var $configTable;
    var $safeMode;
    var $hasPosix;
    
    function __construct() {
        global $Core;
        
            $this->hasPosix = $this->hasPosixEnabled();
        
			$this->event = $Core->GetVar($_REQUEST, SETUP_KEY_EVENT, null);
	
			if ($this->event !== SETUP_KEY_FINISH && $this->CheckExistingInstall()) {
				$this->ShowNoInstall(); 
				exit(0);
			}

			$this->GetLastError();
			
			switch ($this->event) {
				case SETUP_KEY_SAVEPASS:
					$this->SavePassword();
					break;
				case SETUP_KEY_FINISH:
					$this->Finish();
					break;
			    case SETUP_KEY_SAVEURL:
			        $this->SaveUrl();
			        break;
			    case SETUP_KEY_SHOWURL:
					if (!$this->DoConfigCheck()) {
						$Core->SBRedirect(SETUP_URL_START);
					}
			        $this->UrlPage();
			        $this->ShowPage();
			        break;
				case SETUP_KEY_PASSWORD:
				case SETUP_KEY_START:
					if (!$this->DoConfigCheck()) {
						$Core->SBRedirect(SETUP_URL_START);
					}
					$this->StartPage();
					$this->ShowPage();
					break;
			    case 'login':
			        $Core->SBRedirect(SETUP_URL_LOGIN);
			        break;
			    default:
					$this->ShowConfigTable();
			        break;
			}
    }
    
    function SetupWizard() {
        $this->__construct();
    }
    
    function hasPosixEnabled() {
        return (function_exists('posix_geteuid') && is_callable('posix_geteuid'));
    }
    
    function makeRequiredDirs() {
        $required = array(
            SETUP_PATH_TO_ROOT . 'data/ads',
			SETUP_PATH_TO_ROOT . 'data/gadgets',
			SETUP_PATH_TO_ROOT . 'cache'
        );
        for ($i=0; $i<count($required); $i++) {
            if (!is_dir($required[$i])) {
				FileSystem::make_dir($required[$i]);
			}
        }
    }
    
    function DoConfigCheck() {
        global $Core;
        
        $this->makeRequiredDirs();
        
        $file_flag = 1;
        $dir_flag  = 1;
    
        $safemode = ini_get('safe_mode');
        $this->safeMode = $safemode;
                
        $files = $Core->ListFiles(SETUP_PATH_TO_ROOT . 'data/xml/', array());
		$dirs  = array(
		    SETUP_PATH_TO_ROOT . 'data/',
			SETUP_PATH_TO_ROOT . 'data/ads/',
			SETUP_PATH_TO_ROOT . 'data/gadgets/',
			SETUP_PATH_TO_ROOT . 'data/media/',
			SETUP_PATH_TO_ROOT . 'data/plugins/',
			SETUP_PATH_TO_ROOT . 'data/skins/',
			SETUP_PATH_TO_ROOT . 'data/xml/'
		);
		
		$dir_list = array();
		for ($i=0; $i<count($dirs); $i++) {
		    $writable = FileSystem::writable($dirs[$i]);
			if ($writable) {
				$dir_flag = 0;
			}
			array_push($dir_list, array($dirs[$i], $writable));
		}
		
		$file_list = array();
		for ($i=0; $i<count($files); $i++) {
		    $writable = FileSystem::writable($files[$i]);
			if ($writable) {
				$file_flag = 0;
			}
			array_push($file_list, array($files[$i], $writable));
		}
		
		$this->configTable = array(
		    'safe_mode' => $safemode,
		    'dir_list'  => $dir_list,
		    'file_list' => $file_list
		);
		
		if ($dir_flag == 1 || $file_flag == 1) {
		    return false;
		}
		return true;
    }
    
    function getExpectedPerms($file) {
    
        if (!$this->hasPosix) {
            return "775";
        }
    
		$pgid = FileSystem::process_gid();
		$puid = FileSystem::process_uid();
		$sgid = FileSystem::file_group($file);
		$suid = FileSystem::file_uid($file);
		$snam = FileSystem::file_owner($file);
		$pmem = FileSystem::process_members();
		
		if ($puid == $suid) {
			return "644";
		}
		else if ($pgid == $sgid) {
			return "775";
		}
		else if (in_array($suid, $pmem) || in_array($snam, $pmem)) {
			return "775";
		}
		return "777";
    }
    
    function ShowConfigTable() {
    
        $flag = $this->DoConfigCheck() == 0;
    
        $safemode = $this->configTable['safe_mode'];
        $dir_list = $this->configTable['dir_list'];
        $file_list = $this->configTable['file_list'];
        
        $caution_flag = 0;
        
        global $Core;
        $this->html = str_replace(
            '{page:content}',
            FileSystem::read_file(SETUP_HTML_CONFIG_TABLE),
            FileSystem::read_file(SETUP_HTML_SKIN)
        );
        $this->html = str_replace('"ui/', '"../ui/', $this->html);
        
        $dontShow = array(
            '{page:dashboard}',
            '{analytics}',
            '{inc:wysiwygeditor}',
            '{inc:scripts}'
        );
        
        $class = 'pass';
        $value = 'Off';
        if ($safemode == 1) {
			$class = 'fail';
			$value = 'On';
        }
        
        $server_settings = 
        	"<tr><td class=\"$class\">Safe Mode</td>" 
            . "<td class=\"$class\" align=\"center\">{$value}</td>" 
            . "<td class=\"expected\" align=\"center\">Off</td></tr>";
        
        $class = 'pass';
        if (!$this->hasPosix) {
            $class = 'fail';
        }
        
        $posix = "Enabled";
        if (!$this->hasPosix) {
            $posix = "Disabled";
        }
		$server_settings .= 
			"<tr><td class=\"$class\">Posix Library</td>" 
			. "<td class=\"$class\" align=\"center\">{$posix}</td>" 
			. "<td class=\"expected\" align=\"center\">Enabled</td></tr>";

        $this->html = str_replace(
            '{config_test:settings}',
            $server_settings,
            $this->html
        );
        
        $class = '';
        $value = '';
        
        $rows = null;
        for ($i=0; $i<count($dir_list); $i++) {
            $dirname = str_replace('../', '', $dir_list[$i][0]);
            
            $perms = @substr(decoct(fileperms($dir_list[$i][0])), -3);
            $expected = $this->getExpectedPerms($dir_list[$i][0]);
            
            if ($expected == "777") {
                $caution_flag = 1;
            }

             $class = 'pass';
            if (!$this->perm_compare($expected, $perms)) {
                $flag  = 1;
                $class = "fail";
            }
            
            $rows .= "<tr><td class=\"$class\">{$dirname}</td>"
                . "<td  align=\"center\" class=\"$class\">{$perms}</td>" 
                . "<td class=\"expected\" align=\"center\">$expected</td></tr>\n";
        }
        $this->html = str_replace(
            '{config_test:dirs}',
            $rows,
            $this->html
        );
        
        $rows = null;
        for ($i=0; $i<count($file_list); $i++) {
            $filename = str_replace('../', '', $file_list[$i][0]);
            
            $perms = @substr(decoct(fileperms($file_list[$i][0])), -3);
            $expected = $this->getExpectedPerms($file_list[$i][0]);
            
            if ($expected == "777") {
                $caution_flag = 1;
            }

            $class = "pass";
            if (!$this->perm_compare($expected, $perms)) {
                $flag  = 1;
                $class = "fail";
            }
            
            $rows .= "<tr><td class=\"$class\">{$filename}</td>"
                . "<td  align=\"center\" class=\"$class\">{$perms}</td>" 
                . "<td class=\"expected\" align=\"center\">$expected</td></tr>\n";
        }
        $this->html = str_replace(
            '{config_test:files}',
            $rows,
            $this->html
        );
        
        
        $configStyle  = "div#no-posix, #config-pass, #config-warn {display:none;}";
        
        if (!$flag && $caution_flag) {
            $configStyle  = "div#no-posix, #config-pass, #config-fail {display:none;}";
        }
        else if (!$flag) {
            $configStyle  = "div#no-posix, #config-fail, #config-warn {display:none;}";
        }
        
        if (!$this->hasPosix) {
            $configStyle = "#config-pass, #config-warn, #config-fail {display:none;}";
        }
        
        $this->html = str_replace('/*config-style*/', $configStyle, $this->html);
        $this->html = str_replace($dontShow, null, $this->html);
        $this->html = str_replace('{skyblue:name}',    SB_PROD_NAME, $this->html);
        $this->html = str_replace('{skyblue:version}', SB_VERSION,   $this->html);
        $this->html = str_replace('{page:title}', 'Configuration Details',   $this->html);
        echo $this->html;
    }
    
    function perm_compare($perm1, $perm2) {
        if (strlen($perm1) != 3) return false;
        if (strlen($perm2) != 3) return false;
        if (intval($perm1{0}) > intval($perm2{0})) {
            return false;
        }
        if (intval($perm1{1}) > intval($perm2{1})) {
            return false;
        }
        if (intval($perm1{2}) > intval($perm2{2})) {
            return false;
        }
        return true;
    }
    
    function CheckExistingInstall() {
        global $Core;

        if (file_exists(SB_LOGIN_FILE) &&
            file_exists(SB_CONFIG_XML_FILE) && 
            file_exists(SB_PAGE_FILE) &&
            file_exists(SB_MENU_GRP_FILE))
        {
            return 1;
        }
        return 0;
    }
    
    function ShowNoInstall() {
        global $Core;
        $this->html = str_replace(
            '{page:content}',
            FileSystem::read_file(SETUP_HTML_NO_INSTALL),
            FileSystem::read_file(SETUP_HTML_SKIN)
        );
        $this->html = str_replace('"ui/', '"../ui/', $this->html);
        
        $dontShow = array(
            '{page:dashboard}',
            '{analytics}',
            '{inc:wysiwygeditor}',
            '{inc:scripts}'
        );
        $this->html = str_replace($dontShow, null, $this->html);
        $this->html = str_replace('{skyblue:name}',    SB_PROD_NAME, $this->html);
        $this->html = str_replace('{skyblue:version}', SB_VERSION,   $this->html);
        $this->html = str_replace('{page:title}', 'Create Your Password',   $this->html);
        echo $this->html;
    }
    
    function Finish() {
        global $Core;
        $this->html = str_replace(
            '{page:content}',
            FileSystem::read_file(SETUP_HTML_FINISH),
            FileSystem::read_file(SETUP_HTML_SKIN)
        );
        $this->html = str_replace('"ui/', '"' . SETUP_PATH_TO_ROOT .  'ui/', $this->html);
        $dontShow = array(
            '{page:dashboard}',
            '{analytics}',
            '{inc:wysiwygeditor}',
            '{inc:scripts}'
        );
        $this->html = str_replace($dontShow, null, $this->html);
        $this->html = str_replace('{skyblue:name}',    SB_PROD_NAME, $this->html);
        $this->html = str_replace('{skyblue:version}', SB_VERSION,   $this->html);
        $this->html = str_replace('{page:title}', 'Congratulations!',   $this->html);
        echo $this->html;
    }
        
    function GetLastError() {
        if (isset($_SESSION[SETUP_KEY_ERROR]) &&
            !empty($_SESSION[SETUP_KEY_ERROR]))
        {
            $error = $_SESSION[SETUP_KEY_ERROR];
            unset($_SESSION[SETUP_KEY_ERROR]);
            $this->error = 
            "<div class=\"msg-error\">\n" . 
            "<h2>Error</h2>\n" . 
            "<p>" . $error[SETUP_KEY_MSG] . "</p>\n" . 
            "</div>\n";
        }
    }
       
    function InitLoginFile() {
        global $Core;
        if (file_exists(SB_LOGIN_FILE)) {
            $this->CheckExistingInstall();
        }
        $xml = $Core->xmlHandler->ObjsToXML(array(), SETUP_TYPE_PASSWORD);
        $Core->WriteFile(SB_LOGIN_FILE, $xml);
    }
    
    function SetError($message) {
        $_SESSION[SETUP_KEY_ERROR] = array(
            SETUP_KEY_MSG  => $message
        );
    }
    
    function ConfirmAuthInfo($username, $password, $confirm) {
        if (empty($username)) {
            $this->IsError = 1;
            $this->SetError(SETUP_USERNAME_NULL);
            return;
        }
        else if (strlen($username) < SETUP_USERNAME_LENGTH) {
            $this->IsError = 1;
            $this->SetError(SETUP_USERNAME_TOO_SHORT);
            return;
        }
        else if (empty($password)) {
            $this->IsError = 1;
            $this->SetError(SETUP_PASSWORD_NULL);
            return;
        }
        else if (strlen($password) < SETUP_PASSWORD_LENGTH) {
            $this->IsError = 1;
            $this->SetError(SETUP_PASSWORD_TOO_SHORT);
            return;
        }
        else if ($password !== $confirm) {
            $this->IsError = 1;
            $this->SetError(SETUP_PASSWORD_MISMATCH);
            return;
        }
    }
    
    function SavePassword() {
        global $Core;
        global $config;
        
        $username = $Core->GetVar($_POST, SETUP_KEY_USERNAME, null);
        $password = $Core->GetVar($_POST, SETUP_KEY_PASSWORD, null);
        $confirm  = $Core->GetVar($_POST, SETUP_KEY_CONFIRM_PASSWORD, null);
        
        $this->ConfirmAuthInfo($username, $password, $confirm);

        if ($this->IsError) {
            $Core->SBRedirect(SETUP_URL_PASSWORD);
            exit(0);
        }
            
        $_SESSION[SETUP_KEY_ERROR] = null;
		$this->InitLoginFile();
		
		$login = new stdClass;
		$login->id = 1;
		$login->username = md5(
			SB_PASS_SALT.$Core->GetVar($_POST, SETUP_KEY_USERNAME, null)
	    );
		$login->password = md5(
			SB_PASS_SALT.$Core->GetVar($_POST, SETUP_KEY_PASSWORD, null)
		);

		$xml = $Core->xmlHandler->ObjsToXML(array($login), SETUP_STR_LOGIN);
		
		if (!$Core->WriteFile(SB_LOGIN_FILE, $xml, 1)) {
			$this->IsError = 1;
			$this->SetError(SETUP_PASSWORD_NOT_SAVED);
			$Core->SBRedirect(SETUP_URL_PASSWORD);
		} 
		$Core->SBRedirect(SETUP_URL_FINISH);
    }
    
    function SaveUrl() {
        global $Core;
        global $config;
        
        $url = $Core->GetVar($_POST, 'url', null);
        
        $arr = $this->sbc_parse_url($url);
        
        if (!isset($arr['host']) || empty($arr['host'])) {
            $this->IsError = 1;
            $this->SetError(
                "You must specify a fully-qualified domain.<br />" 
                . "Example: http://www.mydomain.com"
            );
            $Core->SBRedirect(SETUP_URL_URL);
            exit(0);
        }
        
        $_SESSION[SETUP_KEY_ERROR] = null;
		
		$config = $Core->xmlHandler->ParserMain(SB_CONFIG_XML_FILE);
		$config = $config[0];
		
		$config->site_url = $url;
		
		$xml = $Core->xmlHandler->ObjsToXML(array($config), "configuration");
		
		if (!$Core->WriteFile(SB_CONFIG_XML_FILE, $xml, 1)) {
			$this->IsError = 1;
			$this->SetError("Your web site address could not be saved");
			$Core->SBRedirect(SETUP_URL_URL);
		} 
		$Core->SBRedirect(SETUP_URL_PASSWORD);
    }
    
    function sbc_parse_url($url) {
		if (empty($url)) return array();
		return @parse_url($url);
    }
    
    function ShowPage() {
        echo str_replace(SETUP_TOKEN_ERROR, $this->error, $this->html);
    }
    
    function UrlPage() {
        global $Core;
        $this->html = str_replace(
            '{page:content}',
            FileSystem::read_file(SETUP_HTML_URL),
            FileSystem::read_file(SETUP_HTML_SKIN)
        );
        $this->html = str_replace('"ui/', '"' . SETUP_PATH_TO_ROOT .  'ui/', $this->html);
        $dontShow = array(
            '{page:dashboard}',
            '{analytics}',
            '{inc:wysiwygeditor}',
            '{inc:scripts}'
        );
        $this->html = str_replace($dontShow, null, $this->html);
        $this->html = str_replace('{skyblue:name}',    SB_PROD_NAME, $this->html);
        $this->html = str_replace('{skyblue:version}', SB_VERSION,   $this->html);
        $this->html = str_replace('{page:title}', 'Enter Your Site URL',   $this->html);
    }
    
    function StartPage() {
        global $Core;
        $this->html = str_replace(
            '{page:content}',
            FileSystem::read_file(SETUP_HTML_PASSWORD),
            FileSystem::read_file(SETUP_HTML_SKIN)
        );
        $this->html = str_replace('"ui/', '"' . SETUP_PATH_TO_ROOT .  'ui/', $this->html);

        if ($this->safeMode == 1) {
            $this->html = str_replace(
                '<!--#safemode_flag-->',
                '<div class="msg-warning"><h2>Warning</h2>' . 
                '<p>Safe Mode is enabled on your server. You can continue with the installation ' . 
                'but this may cause SkyBlueCanvas to malfunction.</p></div>',
                $this->html
            );
        }
        
        $dontShow = array(
            '{page:dashboard}',
            '{analytics}',
            '{inc:wysiwygeditor}',
            '{inc:scripts}'
        );
        $this->html = str_replace($dontShow, null, $this->html);
        $this->html = str_replace('{skyblue:name}',    SB_PROD_NAME, $this->html);
        $this->html = str_replace('{skyblue:version}', SB_VERSION,   $this->html);
        $this->html = str_replace('{page:title}', 'Create Your Password',   $this->html);
    }
    
}

?>