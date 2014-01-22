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


$images = FileSystem::list_files(SB_MEDIA_DIR, 1);

header('Content-type: application/javascript');

?>
var tinyMCEImageList = new Array(
<?php for ($i=0; $i<count($images); $i++) : ?>
<?php $image = str_replace('../', '', $images[$i]); ?>
    ["<?php echo $image; ?>", "<?php echo $image; ?>"]<?php echo $i < count($images)-1 ? ",\n" : "" ?>
<?php endfor; ?>
);