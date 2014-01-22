<?php

defined('SKYBLUE') or die(basename(__FILE__));

$Core->RegisterEvent('OnRenderPage', 'doMyVarsPlugin');

function doMyVarsPlugin($html) {

    global $Core;
    
    if (trim($html) == "") return;
    
    if (!file_exists(SB_XML_DIR . "myvars.xml")) return $html;
    
    $myvars = $Core->xmlHandler->ParserMain(
        SB_XML_DIR . "myvars.xml"
    );
    
    if (count($myvars) == 0) return $html;
    
    foreach ($myvars as $var) {
        $variable = $var->name;
        $value = $var->value;
        
        // If the variable is empty, just return the $html
        
        if (empty($variable)) return $html;
        
        $variable = base64_decode($var->name);
        
        // The value can potentially be empty. We only 
        // try to decode it if it is not empty
        
        if (!empty($value)) {
        	$value = base64_decode($var->value);
        }
        
        if ($var->var_type == "variable") {
            $html = str_replace("[[" . trim($variable) . "]]", $value, $html);
        }
        else if ($var->var_type == "string") {
            $html = str_replace($variable, $value, $html);
        }
        else if ($var->var_type == "regex") {
            $html = preg_replace("$variable", "$value", $html);
        }
    }
    return $html;
}

?>