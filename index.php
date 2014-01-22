<?php @ob_start("ob_gzhandler");

/**
 * @version        1.1 r247 2010-02-23 20:10:00 $
 * @package        SkyBlueCanvas
 * @copyright      Copyright (C) 2005 - 2008 Scott Edwin Lewis. All rights reserved.
 * @license        http://opensource.org/licenses/gpl-license.php GNU Public License
 * SkyBlueCanvas is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYING.txt for copyright notices and details.
 */

$time_start = getmicrotime();

define('DS', DIRECTORY_SEPARATOR);

/**
 * SKYBLUE, _SBC_ROOT_ and BASE_PAGE must all be defined before including 
 * the base.php file.
 *
 * SKYBLUE - A security check to make sure any core files are being included 
 *           locally.
 *
 * _SBC_ROOT_ - The relative path to the root from the including file.
 *
 * BASE_PAGE  - the name of the FrontController page (index.php|admin.php)
 */

define('SKYBLUE', 1);
define('_SBC_ROOT_', './');
define('BASE_PAGE', 'index.php');

require_once(_SBC_ROOT_ . 'base.php');

$Filter = new Filter;

$Router = new Router;
$Router->route();

$Core = new Core(array(
    'path'     => '',
    'events'   => array(
        'OnBeforeInitPage',
        'OnBeforeShowPage',
        'OnAfterShowPage',
        'OnRenderPage',
        'OnAfterLoadStory',
        'OnBeforeUnload'
   )
));

$Core->trigger('OnBeforeLoadConfig');
$config = $Core->LoadConfig();

$Core->trigger('OnBeforeCheckInstall');
$Core->CheckInstall();

if ($Filter->get($config, 'use_cache', 0)) {
    $Core->trigger('OnBeforeInitCache');
    $Cache = new Cache($Router->getFingerprint(), 60);
}

$Core->trigger('OnBeforeLoadPlugins');
$Core->LoadUserPlugins();

$Core->trigger('OnBeforeInitPage');
$Core->DefineDefaultPage();

$html = "";

$errorPage = null;
if ($Filter->get($_GET, 'pid', DEFAULT_PAGE) == NOT_FOUND) {
    $Core->trigger('OnBeforeErrorPage');
    if ($errorPage = $Router->pageNotFound()) {
        $_GET['pid'] = $errorPage->id;
    }
    else {
        header("HTTP/1.0 404 Not Found");
        die(NO_404_PAGE);
    }
}

if ($Filter->get($config, 'use_cache', 0) && $Cache->isCached()) {
    $Core->trigger('OnBeforeGetCache');
    $html = $Cache->getCache();
}

$Skin = new Skin($Filter->get($_GET, 'pid', DEFAULT_PAGE));
if (empty($html)) {
    $html = $Skin->getHtml();
    $html = str_replace(TOKEN_SKYBLUE_INFO_LINK, SKYBLUE_INFO_LINK, $html);
    $html = str_replace(TOKEN_BODY_CLASS, null, $html);
}

$html = $Core->trigger('OnBeforeShowPage', $html);
$html = $Core->trigger('OnRenderPage', $html);

if ($Filter->get($config, 'use_cache', 0)) {
    $Core->trigger('OnBeforeSaveCache');
    $Cache->saveCache($html);
    $html .= "\n<!-- page caching enabled -->\n";
}

if ($errorPage) {
    $Core->trigger('OnBefore404Header');
    header("HTTP/1.0 404 Not Found");
}
echo str_replace(WYM_RELATIVE_PATH, FULL_URL, $html);

$Core->trigger('OnAfterShowPage');

// Just doing some performance measuring
 
$time_taken = round(getmicrotime()-$time_start,4);
echo "\n<!-- Generated in $time_taken seconds -->" ;

function getmicrotime() {
    list($usec,$sec) = explode(" ", microtime());
    return ((float)$usec+(float)$sec);
}

@ob_flush();