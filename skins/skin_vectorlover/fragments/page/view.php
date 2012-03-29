<?php
defined('SKYBLUE') or die('Unauthorized file request');
$Filter = new Filter;
foreach ($data as $item) {
    if ($Filter->get($_GET, 'pid', DEFAULT_PAGE) == $item->id) {
        echo $Core->trigger(
            'OnAfterFragments',
            FileSystem::read_file(SB_STORY_DIR . $item->story)
        );
    }
}
?>