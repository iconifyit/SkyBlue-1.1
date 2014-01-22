<?php defined('SKYBLUE') or die('Bad file request');

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
 * Creates a JavaScript link tag
 * @param String $source  The path to the script file
 * @return String
 */
function make_script_link($source) {
    return "<script type=\"text/javascript\" src=\"{$source}\"></script>\n";
}

/**
 * Creates a JavaScript element with embedded code
 * @param String  $code   The JavaScript code
 * @return String
 */
function make_script_element($code) {
    return "<script type=\"text/javascript\">{$code}</script>\n";
}

/**
 * Creates a stylesheet link tag
 * @param String $source  The path to the stylesheet file
 * @return String
 */
function make_style_link($href) {
    return "<link rel=\"stylesheet\" type=\"text/css\" href=\"{$href}\" />\n";
}

/**
 * Creates a style element with embedded code
 * @param String  $code   The CSS code
 * @return String
 */
function make_style_element($code) {
    return "<style type=\"text/css\">{$code}</style>\n";
}

/**
 * Uses CSS to hide an element
 * @param String $selector  The CSS selector for the element(s)
 * @return String
 */
function hide_element($selector) {
    return make_style_element($selector . " { display: none; }");
}

/**
 * Uses CSS to show an element
 * @param String $selector  The CSS selector for the element(s)
 * @return String
 */
function show_element($selector) {
    return make_style_element($selector . " { display: block; }");
}

/**
 * Determines if the current page is the home page.
 * @param int  A page ID to test (optional)
 * @return bool
 */
function is_home($pageId=null) {
    global $Core;
    if (is_null($pageId)) {
        $pageId = $Core->GetVar($_GET, 'pid', DEFAULT_PAGE);
    }
    return $pageId === DEFAULT_PAGE;
}

/**
 * Determines if the current page is in a list of supplied page ids
 * @param Array $pids  The array of page ids to search
 * @return boolean
 */
function in_pagelist($pids=array()) {
    global $Core;
    return in_array($Core->GetVar($_GET, 'pid', ''), $pids);
}

/**
 * Gets the data object for the current page
 * @param boolean $refresh  Whether or not to refresh the staticallay stored Page object
 * @return object  A reference to the current Page
 */
function current_page($refresh=false) {
    global $Core;
    static $Page;
    if (!is_object($Page) || $refresh) {
        $pages = get_pages(true);
        $Page = $Core->SelectObj($pages, $Core->GetVar($_GET, 'pid', ''));
    }
    return $Page;
}

/**
 * Gets all the page objects
 * @param boolean $refresh  Whether or not to refresh the statically stored data
 * @return array  An array of Page objects
 */
function get_pages($refresh=false) {
    global $Core;
	static $pages;
	if (!is_array($pages) || $refresh) {
	    $pages = $Core->xmlHandler->ParserMain(SB_XML_DIR . "page.xml");
	}
	return $pages;
}

/**
 * Gets a property of the Page object
 * @param string  The name of the property to get
 * @return mixed  The value of the Page property
 */
function page_info($prop) {
    global $Core;
    return $Core->GetVar(current_page(), $prop, '');
}

/**
 * Reads in and parses an XML file
 * @param String $file  The file path to the XML file
 * @return Array        An array of Objects
 */
function parse_xml($file) {
	global $Core;
	if (!file_exists($file)) {
		trigger_error(
			"{$file} does not exist",
			E_USER_ERROR
		);
	}
	else {
		return $Core->xmlHandler->ParserMain($file);
	}
}

/**
 * Converts an array of Objects to an XML document.
 * @param Array $objects   The Array of objects to convert
 * @return String          The XML document.
 */
function objects_to_xml($objects, $type='') {
	global $Core;
	return $Core->xmlHandler->ObjsToXML($objects, $type);
}

/**
 * Gets the current context (admin, front, etc.)
 */
function get_context() {
    $context = "unknown";
	if (constant('_ADMIN_') == 1) {
	    $context = "admin";
	}
	else {
	    $context = "front";
	}
	return $context;
}

/** 
 * Creates a new Service_JSON object but only once.
 * @return Object
 */
function new_json() {
    static $json;
    if (!is_object($json)) {
        $json = new Services_JSON();
    }
    return $json;
}

/**
 * Encodes an Array or Object as JSON
 * @param Mixed $data  The PHP data structure
 * @return String
 */
function encode_json($data) {
    $json = new_json();
    return $json->encode($data);
}

/**
 * Decodes a JSON string to a PHP data structure
 * @param Mixed $json  The JSON string
 * @return Mixed
 */
function decode_json($data) {
    $json = new_json();
    return $json->encode($data);
}

/**
 * Sorts an array of objects by comparing a member property.
 * @param array   The array of objects to sort
 * @param string  The name of the property to sort on
 * @return void
 */
function sort_objects(&$objects, $sort_field) {
	$sort = Core::LoadPlugin('quicksort');
	$sort->_sort($objects, $sort_field);
}

