<?php

define('SKYBLUE', 1);
define('_SBC_ROOT_', str_repeat('../', 3));
defined('BASE_PAGE') or define('BASE_PAGE', '');

require_once(_SBC_ROOT_ . 'base.php');

$Filter = new Filter;

/*
* We don't need the Router:
* $Router = new Router;
*/

$Core = new Core(array(
    'path'     => _SBC_ROOT_,
    'lifetime' => 3600,
    'events'   => array(
        'OnBeforeInitPage',
        'OnBeforeShowPage',
        'OnAfterShowPage',
        'OnRenderPage',
        'OnAfterLoadStory',
        'OnBeforeUnload'
   )
));

$config = $Core->LoadConfig();


$pages = $Core->xmlHandler->ParserMain(
    SB_XML_DIR . "page.xml"
);

$Router = new Router(_SBC_ROOT_);

$links = array();
if (!count($pages)) return;
foreach ($pages as $page) {
    array_push($links, str_replace(FULL_URL, '', $Router->GetLink($page->id)));
}

header('Content-type: application/javascript');

?>
var tinyMCELinkList = new Array(
<?php for ($i=0; $i<count($links); $i++) : ?>
<?php $link = str_replace('../', '', $links[$i]); ?>
    ["<?php echo basename($link); ?>", "<?php echo $link; ?>"]<?php echo $i < count($links)-1 ? ",\n" : "" ?>
<?php endfor; ?>
);