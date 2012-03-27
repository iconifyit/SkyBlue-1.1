<?php

function check_address($config) {
    $check = 
        $config['contact_address'] 
        . $config['contact_city'] 
        . $config['contact_state'] 
        . $config['contact_zip']
        . $config['contact_phone'];
    return (empty($check) ? 0 : 1);
}

function the_contact($contacts, $config) {
    global $Core;
    $contact = $Core->SelectObj($contacts, Filter::get($_POST, 'cid', null));
    return isset($contact->email) ? $contact->email : $config['contact_email'];
}

function the_form_action() {
    global $Router;
    return $Router->GetLink(Filter::get($_GET, 'pid', ''));
}

function the_message() {
    $message = Filter::get($_SESSION, 'contact_form_message', array());
    if (empty($message)) return null;
    unset($_SESSION['contact_form_message']);
    return get_message_string($message);
}

function get_message_string($message) {
    return 
    "<div class=\"" . Filter::get($message, 'class', 'none') . "\">\n" . 
    "<h2>" . Filter::get($message, 'title', null) . "</h2>\n" . 
    "<p>" . Filter::get($message, 'string', null) . "</p>\n" .
    "</div>\n";
}

function the_action() {
    return strToLower(Filter::get($_POST, 'action', ''));
}

function set_message($class, $title, $string) {
     $_SESSION['contact_form_message'] = array(
         'class'  => $class,
         'title'  => $title,
         'string' => $string
     );
}

function handle_contact_form($mailto) {
    global $Core;
    
    $form = array();
    $form['name']        = Filter::get($_POST, 'name', '');
    $form['email']       = Filter::get($_POST, 'email', '');
    $form['subject']     = Filter::get($_POST, 'subject', '');
    $form['message']     = Filter::get($_POST, 'message', '');
        
    $errors = array();
    foreach ($form as $k=>$v) {
      if ($v == '') array_push($errors, $k);
    }
    if (count($errors)) {
        set_message(
            'error',
            'Your message cannot be sent.<br />Please complete the following fields:',
            implode(', ', $errors)
        );
    } 
    else {
        $headers  = "MIME-Version: 1.0\n";
        $headers .= "Content-type: text/html; charset=iso-8859-1\n";
        $headers .= "From: ".$form['name']." <".$form['email'].">\n";
        $headers .= "Reply-To: <".$form['email'].">\n";
        $headers .= "X-Priority: 3\r\n";
        $headers .= "X-MSMail-Priority: Low\n";
        $headers .= "X-Mailer: WebServer\n";
        
        $txtvers  = date('d M\, Y l h:i:s A')."\n\n";
        $txtvers .= 'To: '.$mailto."\n";
        $txtvers .= 'From: '.$form['name']."\n";
        $txtvers .= 'Email: '.$form['email']."\n\n";
        $txtvers .= 'Subject: '.$form['subject']."\n\n";
        $txtvers .= $form['message']."\n";
        
        FileSystem::write_file(
            'data/email/~'.$form['email'].'.'.time().'.txt', 
            $txtvers
        );

        if (bashMail($form['subject'], $txtvers, $mailto)) {
            set_message(
                'success',
                'Your message has been sent',
                'We will be in touch shortly'
            );
        }
        else {
            set_message(
                'error',
                'Your message could not be sent',
                'An unknown error occurred.'
            );
        }
    }
}

function bashMail($sbj, $msg, $to, $cc='', $bc='') {
    $cmd = 'echo "'.$msg.'" | mail -s "'.$sbj.'" '.$to;
    exec($cmd, $err);
    $res = count($err) == 0 ? 1 : 4 ;
    return $res;
}