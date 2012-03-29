<?php

function compare_by_date($item1, $item2) {
    $date1 = @strtotime($item1->date);
    $date2 = @strtotime($item2->date);
    
    if ($date1 == $date2) {
        return 0;
    }
    else if ($date1 < $date2) {
        return 1;
    }
    return -1;
}

function get_link($params=array()) {
    global $Router;
    global $Filter;
    
	$pid = $Filter->get($_GET,   'pid', DEFAULT_PAGE);
	$aid = $Filter->get($params, 'aid', null);
    if (defined('USE_SEF_URLS') && USE_SEF_URLS == 1) {
        $params = array();
        if (!empty($aid)) {
            $params['-pg-'] = $pid;
            $params['-'] = $aid;
        }
        return $Router->GetLink($Filter->get($_GET, 'pid', DEFAULT_PAGE), $params);
    }
    else {
        $link = "index.php?pid=$pid";
        if (!empty($aid)) {
            $link .= "&show=$aid";
        }
        return $link;
    }
}

function get_intro($item) {
    if (empty($item->intro)) return null;
    return base64_decode($item->intro);
}

function get_story($item) {
	if (file_exists(SB_STORY_DIR . $item->story)) {
		return FileSystem::read_file(SB_STORY_DIR . $item->story); 
	}
	return null;
}

function get_published($items) {
    if (!$items) return array();
    $pub = array();
    foreach ($items as $item) {
        if (!isset($item->published) || $item->published) {
            array_push($pub, $item);
        }
    }
    return $pub;
}

?>