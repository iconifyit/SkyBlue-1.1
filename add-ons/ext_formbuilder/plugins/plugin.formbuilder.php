<?php

global $Core;
global $Filter;
global $config;

$Core->register('callback_form_id_2', 'FormBuilderMailer');

function FormBuilderMailer($data) {
	global $Filter;
	global $Core;
	global $config;
	
	if (!class_exists('Postmaster')) return false;
	
	$Request = $Filter->get($data, 'request');
	$Form    = $Filter->get($data, 'form');
	$Fields  = $Filter->get($data, 'fields');

	$Mailer = new Postmaster;
	
	$from_addr = null;
	if ($Filter->get($Request, 'email')) {
		$from_addr = $Filter->get($Request, 'email');
	}
	if (empty($from_addr)) return;
	
	$to_addr = null;
	if ($Filter->get($Form, 'contact')) {
		$to_addr = $Filter->get($Form, 'contact');
	}
	else if ($Filter->get($config, 'contact_email')) {
	    $to_addr = $Filter->get($config, 'contact_email');
	}
	if (empty($to_addr)) return;
	
	$Mailer->errorcheck = 1;
	$Mailer->backup     = 0;
	$Mailer->path       = SB_SITE_EMAIL_DIR;
	
	$Mailer->from       = $from_addr;
	$Mailer->replyto    = $from_addr;
	$Mailer->recepient  = $to_addr;
	$Mailer->subject    = $Form->title . " Response";
	
	$msg = $Filter->get($Form, 'title') . " Response\n\n";
	foreach ($Fields as $field) {
	    $value = Filter::get($Request, $field->title);
	    if (is_array($value)) $value = implode(', ', $value);
	    if (!empty($value)) {
	        $msg .= ucwords(str_replace('_', ' ', $field->title)).': '.$value."\n";
	    }
	}
	$Mailer->SetMessage($msg);
	$result = $Mailer->ssmMail();
	
	FormBuilderResponder($Request, $Form, $Fields);
	
	return $result;
}

function FormBuilderResponder($Request, $Form, $Fields) {
    global $Filter;
    
    $msg = $Filter->get($Form, 'autoresponse');
    if (empty($msg)) return;
    
	if (!empty($msg)) {
	    $msg = urldecode(base64_decode($msg));
	}
	
	foreach ($Fields as $field) {
	    $value = Filter::get($Request, $field->title);
	    if (is_array($value)) $value = implode(', ', $value);
	    if (!empty($value)) {
	        $msg = str_replace('{'.$field->title.'}', $value, $msg);
	    }
	}
	
	if (function_exists('doMyVarsPlugin')) {
	    $msg = doMyVarsPlugin($msg);
	}
	
	if (function_exists('plgSiteVars')) {
		$msg = plgSiteVars($msg);
	}
    
    $Mailer = new Postmaster;
	
	$from_addr = null;
	if ($Filter->get($Form, 'contact')) {
		$from_addr = $Filter->get($Form, 'contact');
	}
	else if ($Filter->get($config, 'contact_email')) {
	    $from_addr = $Filter->get($config, 'contact_email');
	}
	if (empty($from_addr)) return;
	
	$to_addr = $Filter->get($Request, 'email');
	if (empty($to_addr)) return;
	
	$Mailer->errorcheck = 1;
	$Mailer->backup     = 0;
	$Mailer->path       = SB_SITE_EMAIL_DIR;
	
	$Mailer->from       = $from_addr;
	$Mailer->replyto    = $from_addr;
	$Mailer->recepient  = $to_addr;
	$Mailer->subject    = $Form->title . " Automated Response";
	$Mailer->SetMessage($msg);
	$Mailer->ssmMail();
}

?>