<?php defined('SKYBLUE') or die('Bad File Request');

/**
 * @version		1.1 r247 2010-02-23 20:10:00 $
 * @package		SkyBlueCanvas
 * @copyright	Copyright (C) 2005 - 2008 Scott Edwin Lewis. All rights reserved.
 * @license		http://opensource.org/licenses/gpl-license.php GNU Public License
 * SkyBlueCanvas is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYING.txt for copyright notices and details.
 */
 
/**
* SKYBLUE, _SBC_ROOT_ and BASE_PAGE must all be defined before including 
* this file.
*
* SKYBLUE - A security check to make sure any core files are being included 
*           locally.
*
* _SBC_ROOT_ - The relative path to the root from the including file.
*
* BASE_PAGE  - the name of the FrontController page (index.php|admin.php)
*/

defined('_SBC_ROOT_') or die('_SBC_ROOT_ not defined');
defined('BASE_PAGE')  or die('BASE_PAGE not defined');

/**
 * r247: fix default timezone error
 */
@date_default_timezone_set(date_default_timezone_get());

if (function_exists('ini_set') && is_callable('ini_set')) {
	ini_set('error_reporting', E_ALL);
	ini_set('display_errors', 'On');
	error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);
	ini_set('error_log', _SBC_ROOT_ . 'php_errors.log');
	
	ini_set(
		'include_path', 
		ini_get('include_path') . ':' . dirname(_SBC_ROOT_) . ':'
	);
}

/**
* This file will allow you to include all the required SBC files. 
* Before including this file, you must define _SBC_ROOT_ to the 
* relative path to the SBC root directory from your file's location.
* For instance, if your file is in /skyblue/mydir/myfile.php,
* you will define _SBC_ROOT_ to '../'
*/

require_once(_SBC_ROOT_ . 'includes/xml.parser.php');
require_once(_SBC_ROOT_ . 'includes/router.php');
require_once(_SBC_ROOT_ . 'includes/object.class.php');
require_once(_SBC_ROOT_ . 'includes/observer.class.php');
require_once(_SBC_ROOT_ . 'includes/error.class.php');
require_once(_SBC_ROOT_ . 'includes/conf.functions.php');
require_once(_SBC_ROOT_ . 'includes/core.php');
require_once(_SBC_ROOT_ . 'includes/skin.class.php');
require_once(_SBC_ROOT_ . 'includes/factory.bundle.php');
require_once(_SBC_ROOT_ . 'includes/filesystem.php');
require_once(_SBC_ROOT_ . 'includes/cache.php');
require_once(_SBC_ROOT_ . 'includes/filter.php');
require_once(_SBC_ROOT_ . 'includes/request.php');
require_once(_SBC_ROOT_ . 'includes/uploader.php');
require_once(_SBC_ROOT_ . 'includes/manager.class.php');
/**
 * r247: New core and helper files
 */
require_once(_SBC_ROOT_ . 'includes/hooks.php');
require_once(_SBC_ROOT_ . 'includes/JSON.php');