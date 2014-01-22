<?php defined('SKYBLUE') or die('Bad file request');

/**
* @version        1.2 Beta 2009-05-23 12:51:00 $
* @package        SkyBlueCanvas
* @copyright      Copyright (C) 2005 - 2010 Scott Edwin Lewis. All rights reserved.
* @license        GNU/GPL, see COPYING.txt
* SkyBlueCanvas is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYING.txt for copyright notices and details.
*/

define('REGEX_SNIPPET_INLINE',  "/({snippet\(([^}]*)\)})/i");
define('REGEX_SNIPPET_COMMENT', "/(<!--#snippet\((.*)\)-->)/i");
define('SNIPPETS_DIR',          SB_SITE_DATA_DIR . "stories/");

global $Core;

$Core->register('OnRenderPage', 'doSnippets');

function doSnippets($html) {
    global $Core;
    $Snippet = SnippetPlugin::getInstance($html);
    $Core->register('snippet.getContent', 'test_snippet_event');
    $Snippet->execute();
    return $Snippet->getHtml();
}

function test_snippet_event($html) {
    return $html; // . '<p>Snippet event firing works.</p>';
}

class SnippetPlugin {

    var $html;

    function getInstance($html) {
        static $instance;
        if (!is_object($instance)) {
            $instance = new SnippetPlugin;
        }
        $instance->setHtml($html);
        return $instance;
    }
    
    function execute() {
        $this->objs = $this->getObjects();
        $this->setHtml(
            $this->parseCommentTokens(
                $this->parseInlineTokens($this->getHtml())
            )
        );
    }
    
    function getHtml() {
        return $this->html;
    }
    
    function setHtml($html) {
        $this->html = $html;
    }
    
    function parseCommentTokens($html) {
        return $this->_parseTokens(
		    $html,
		    REGEX_SNIPPET_COMMENT,
		    "<!--#snippet(_QUERY_)-->"
		);
	}
	
	function parseInlineTokens($html) {
		return $this->_parseTokens(
		    $html,
		    REGEX_SNIPPET_INLINE,
		    "{snippet(_QUERY_)}"
		);
	}
	
	function _parseTokens($html, $token, $replace) {
		global $Core;
		
		preg_match_all($token, $html, $tokens);
		if (count($tokens) < 3) return $html;
		$tokens = @$tokens[2];
		$count = count($tokens);
		for ($i=0; $i<$count; $i++) {
		
		    $output = "";
			
			$queries = array_map('trim', explode(',', $tokens[$i]));
			
			$qCount = count($queries);
			for ($x=0; $x<$qCount; $x++) {

			    $query = $this->_parseParams($queries[$x]);
			    
			    $name = Filter::get($query, 'base');
				$params = Filter::get($query, 'params');
				if (empty($name)) return $html;
				
				$fileName = "{$name}.txt";
				$bits = explode("_", $name);
				if (count($bits) == 2) {
					if ($bits[0] != "snippet" || ! is_numeric($bits[1])) {
						$obj = $this->getSnippetByName($name);
					}
					else {
						$obj = $this->getSnippetById($bits[1]);
					}
				}
				else {
					$obj = $this->getSnippetByName($name);
				}
				
				if (is_object($obj) && isset($obj->id)) {
					$id = $obj->id;
					$fileName = "snippet_{$obj->id}.txt";
		
					$snippet = $Core->trigger(
						'snippet.getContent', 
						$this->_getContent(SNIPPETS_DIR . $fileName, $params)
					);
					
					$href = "";
					if ($Core->GetVar($obj, 'link', '') == "external") {
						$href = $obj->external_link; 
					}
					else if (trim($Core->GetVar($obj, 'link', '')) != "") {
						$linktext = trim($obj->linktext) == "" ? "Link" : $obj->linktext ;
						$href = $Core->GetLink($obj->linktext, $obj->link, '', USE_SEF_URLS);
					}
					$linktext = trim($obj->linktext) == "" ? $href : $obj->linktext ;
					$link = "<span class=\"link\"><a href=\"{$href}\"><span class=\"linktext\">{$linktext}</span></a></span>";
					$output .= "<span class=\"snippet\" id=\"snippet_{$obj->id}\">{$snippet}{$link}</span>";
				}
			}
			$html = str_replace(
				str_replace('_QUERY_', $tokens[$i], $replace),
				$output,
				$html
			);
		}
		return $html;
	}
	
	function getObjects() {
	    global $Core;
	    return $Core->xmlHandler->ParserMain(
	        SB_XML_DIR . "snippets.xml"
	    );
	}
	
	function getSnippetByName($name) {
	    if (!is_array($this->objs)) return null;
	    foreach ($this->objs as $obj) {
	        if ($obj->name == $name) return $obj;
	    }
	    return null;
	}
	
	function getSnippetById($id) {
	    if (!is_array($this->objs)) return null;
	    foreach ($this->objs as $obj) {
	        if ($obj->id == $id) return $obj;
	    }
	    return null;
	}
	
	function _getContent($file, $data=null) {
		$content = FileSystem::read_file($file);
		if (!empty($data)) {
		    foreach ($data as $key=>$value) {
		        $content = str_replace("[[$key]]", $value, $content);
		    }
		}
		return $content;
	}
	
    function _parseParams($token) {
        $arr = explode('?', $token);
        $params = array();
        if (count($arr) > 1) {
            $params = $this->_parseQuery(Filter::get($arr, 1, null));
        }
        return array(
            'base'   => $arr[0],
            'params' => $params
        );
    }
    
    function _parseQuery($str) {
    
        $str = html_entity_decode($str);

        $arr = array();
        
        $pairs = explode('&', $str);
        
        foreach ($pairs as $i) {
            list($name,$value) = explode('=', $i, 2);

            if (isset($arr[$name])) {
                if (is_array($arr[$name])) {
                    $arr[$name][] = $value;
                }
                else {
                    $arr[$name] = array($arr[$name], $value);
                }
            }
            else {
                $arr[$name] = $value;
            }
        }
        return $arr;
    }

}