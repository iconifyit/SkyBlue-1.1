<?php
	
defined('SKYBLUE') or die('Bad File Request');

global $Router; 
global $Filter;

$forms = $Core->xmlHandler->ParserMain(
	SB_XML_DIR . 'forms.xml'
);

if (!count($forms)) return;

$form_id = $forms[0]->id;
if (is_array($params) && count($params) == 3 && is_numeric($params[2])) {
    $form_id = $params[2];
}

$form = $Core->SelectObj($forms, $form_id);

$fields = $Core->xmlHandler->ParserMain(
	SB_XML_DIR . 'fields.xml'
);

$fields = formbuilder_get_fields($form->id, $fields);

$scripts = array();
foreach($fields as $field) {
    $script_file = Filter::get($field, 'button_callback_file');
    if (!empty($script_file) && !in_array($script_file, $scripts)) {
        array_push($scripts, $script_file);
    }
}

?>
<?php for ($i=0; $i<count($scripts); $i++) : ?>
<script type="text/javascript" src="<?php echo ACTIVE_SKIN_DIR; ?>js/<?php echo $scripts[$i]; ?>"></script>
<?php endfor; ?>