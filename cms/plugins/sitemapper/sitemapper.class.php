<?php defined('SKYBLUE') or die('Bad file request');

/**
 * @version		1.1 RC1 2008-11-20 21:18:00 $
 * @package		SkyBlueCanvas
 * @copyright	Copyright (C) 2005 - 2008 Scott Edwin Lewis. All rights reserved.
 * @license		GNU/GPL, see COPYING.txt
 * SkyBlueCanvas is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYING.txt for copyright notices and details.
 */

/**
 * Creates XML Sitemaps for Google SiteMaps.
 */

define('SITEMAP_XML_FILE', SB_SERVER_PATH.'sitemap.xml');

define(
    'SITEMAP_HEAD', 
	"<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n"
	. "<urlset xmlns=\"http://www.google.com/schemas/sitemap/0.84\">\n"
	. "{entries}"
	. "</urlset>\n"
);
define(
    'SITEMAP_LINE', 
      "    <url>\n"
    . "        <loc>{loc}</loc>\n"
    . "        <lastmod>{lastmod}</lastmod>\n"
    . "        <changefreq>monthly</changefreq>\n"
    . "        <priority>0.6</priority>\n"
    . "    </url>\n"
);

class sitemapper {

    var $pages;
    var $xml;
    
    function __construct($channel_data = array()) {
        $this->GetPages();
        $this->BuildMap();
        $this->WriteMap();
    }
    
    function sitemapper($channel_data=array()) {
        $this->__construct($channel_data);
    }
    
    function GetPages() {
        global $Core;
        $this->pages = $Core->xmlHandler->ParserMain(SB_PAGE_FILE);
    }
    
    function BuildMap() {
    
        global $Router;
        
        if (!count($this->pages)) return;
        
        $this->xml = SITEMAP_HEAD;
        
        $lines = null;
        foreach ($this->pages as $page) {
            if (! $this->includeInSiteMap($page)) continue;
            if (! $this->isPublished($page)) continue;
            $line = str_replace('{loc}', $Router->GetLink($page->id), SITEMAP_LINE);
            $line = str_replace('{lastmod}', date('Y-m-d',time()), $line);
            $lines .= $line;
        }
        $this->xml = str_replace('{entries}', $lines, $this->xml);
    }
    
    function isPublished($page) {
        return (! isset($page->published) || $page->published);
    }
    
    function includeInSiteMap($page) {
        if (!isset($page->include_in_sitemap) || trim($page->include_in_sitemap) == "") {
			$page->include_in_sitemap = 1;
		}
		return (isset($page->include_in_sitemap) && $page->include_in_sitemap != 0);
    }
    
    function WriteMap() {
        global $Core;
        if (!empty($this->xml)) {
            FileSystem::write_file(SITEMAP_XML_FILE, $this->xml);
        }
    }
    
}