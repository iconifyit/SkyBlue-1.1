<?php  defined('SKYBLUE') or die('Bad file request');

function get_published_links(&$items) {
    $pub = array();
    foreach ($items as $item) {
        if (!isset($item->published)) {
            array_push($pub, $item);
        }
        else if ($item->published) {
            array_push($item->published);
        }
    }
    return $pub;
}

function get_links(&$group, $items) {
    $these = array();
    foreach ($items as $item) {
        if (in_array($group->id, get_link_groups($item))) {
            array_push($these, $item);
        }
    }
    return $these;
}

function get_link_groups($item) {
    $groups = array();
    if (isset($item->groups)) {
        $groups = array_map('trim', explode(',', $item->groups));
    }
    return $groups;
}

function get_rel(&$item) {
	if (isset($obj->relationship)) {
		echo " rel=\"{$obj->relationship}\"";
	}
    echo null;
}

?>