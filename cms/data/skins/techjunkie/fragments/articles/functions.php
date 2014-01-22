<?php defined('SKYBLUE') or die('Bad file request');

function articles_get_link($params) {
    global $Router;
    
	$pid = Filter::get($_GET,   'pid', DEFAULT_PAGE);
	$cid = Filter::get($params, 'cid', null);
	$aid = Filter::get($params, 'aid', null);
    if (defined('USE_SEF_URLS') && USE_SEF_URLS == 1) {
        $params = array();
        if (!empty($cid)) {
            $params['-pg-'] = $pid;
            $params['-c'] = $cid;
        }
        if (!empty($aid)) {
            $params['-'] = $aid;
        }
        $link = $Router->GetLink(Filter::get($_GET, 'pid', DEFAULT_PAGE), $params);
    }
    else {
        $link = "index.php?pid=$pid";
        if (!empty($cid)) {
            $link .= "&cid=$cid";
        }
        if (!empty($aid)) {
            $link .= "&show=$aid";
        }
    }
    return $link;
}

function articles_get_published($items) {
    if (! $items) return array();
    $pub = array();
    foreach ($items as $item) {
        if (!isset($item->published) || $item->published) {
            array_push($pub, $item);
        }
    }
    return $pub;
}

function articles_get_current_items($data, $gid) {
    if (!$data || !$gid) return array();
    $items = array();
    foreach ($data as $item) {
        if (in_array($gid, articles_get_groups($item->groups))) {
            array_push($items, $item);
        }
    }
    return articles_get_published($items);
}

function articles_get_groups($groups) {
    if (empty($groups)) return array();
    $arr = explode(',', $groups);
    for ($i=0; $i<count($arr); $i++) {
        $arr[$i] = trim($arr[$i]);
    }
    return $arr;
}