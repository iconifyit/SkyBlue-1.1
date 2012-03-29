<?php defined('SKYBLUE') or die('Bad file request'); 

global $Core;

function portfolio_scripts($html) {
    global $config;
    $ACTIVE_SKIN_DIR = Filter::get($config, 'site_url') . ACTIVE_SKIN_DIR;
    return str_replace(
          '</head>', 
          make_script_element('window.skin_path = "' . $ACTIVE_SKIN_DIR . '";')
        . "</head>", 
        $html
    );
}

function slider_scripts($html) {
    global $config;
    $ACTIVE_SKIN_DIR = Filter::get($config, 'site_url') . ACTIVE_SKIN_DIR;
    $FRAGMENT_DIR = basename(dirname(__FILE__));
    return str_replace(
          '</head>', 
          make_script_link($ACTIVE_SKIN_DIR . "fragments/{$FRAGMENT_DIR}/js/slider/easySlider1.7.js")
        . make_script_link($ACTIVE_SKIN_DIR . "fragments/{$FRAGMENT_DIR}/js/slider/slider.js")
        . make_style_link($ACTIVE_SKIN_DIR  . "fragments/{$FRAGMENT_DIR}/css/slider/screen.css")
        . "</head>", 
        portfolio_scripts($html)
    );
}

function get_portfolio_items($data, $params) {
    global $Core;
    $items = $data;
	$category = Filter::get($params, 'category');
	if (!empty($category)) {
		$items = $Core->SelectObjs($data, 'category', $category);
	}
	return $items;
}

function get_portfolio_groups() {
    global $Core;
    if (!file_exists($Core->path . SB_XML_DIR . 'portfolio/category.xml')) return;
    return $Core->xmlHandler->ParserMain(
        $Core->path . SB_XML_DIR . 'portfolio/category.xml'
    );
}

function get_portfolio_item_description($item) {
    $text = "";
    $fileName = Filter::get($item, 'story');
    if (trim($fileName) != '') {
        $text = FileSystem::read_file(SB_STORY_DIR . $fileName);
    }
    return $text;
}
