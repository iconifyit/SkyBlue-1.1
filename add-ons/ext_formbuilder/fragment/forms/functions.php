<?php

defined('SKYBLUE') or die(basename(__FILE__));

/*
* Processes the submitted form
* @param object  The form object
* @param array   The form field objects
* @return mixed
*/

function handle_formbuilder_action(&$form, &$fields, $Request) {
    global $Core;
    $action = Filter::get($form, 'action');
	if ($Core->isRegistered($form->callback_event)) {
	    $Core->trigger(
	    	$form->callback_event, 
	    	array(
				'request' => $Request,
				'form' => $form,
				'fields' => $fields
			)
	    );
	}
    return true;
}

/*
* Gets the fields for the form indicated by $formid
* @param int     The unique ID of the form
* @param array   All of the fiedls from storage
* @return array  The filtered list of fields for the current form
*/

function formbuilder_get_fields($formId, $fields) {
    $filtered = array();
    if (empty($fields)) return $filtered;
    foreach ($fields as $field) {
        if ($field->formid == $formId) {
            array_push($filtered, $field);
        }
    }
    return $filtered;
}

/*
* Determine the action triggered by the user.
* @param  object  The Request object
* @return string  The name of the action
*/

function formbuilder_action($Request) {
    return strToLower(Filter::get($Request, 'action', ''));
}

/*
* Validates the posted form field values
* @param object  The current form data object
* @param array   The fields associated with the current object
* @param object  The HTTP Request object
* @return array  An array of validation errors
*/

function formbuilder_validate($fields, $Request) {
    global $Core;
    $errors = array();
    $errorFields = array();
    foreach ($fields as $field) {
        if (!empty($field->validation)) {
            $validations = explode(',', $field->validation);
            for ($i=0; $i<count($validations); $i++) {
                $validations[$i] = trim($validations[$i]);
                if (empty($validations[$i])) continue;
                $option = "";
                if ($validations[$i] == "minlength") {
                    $option = Filter::get($field, 'minlength');
                }
                if ($validations[$i] == "maxlength") {
                    $option = Filter::get($field, 'maxlength');
                }
                if ($validations[$i] == "regex") {
                    $option = Filter::get($field, 'regex');
                    if (!empty($option)) {
                        $option = base64_decode($option);
                    }
                }
				if (!formbuilder_do_validation($Request->get($field->title), $validations[$i], $option)) {
				    $fieldName = $field->title;
				    if (in_array($fieldName, $errorFields)) continue;
				    array_push($errorFields, $fieldName);
				    $Request->$fieldName = "";
					$label = urldecode(base64_decode($field->label));
					$errorMessage = "Please enter valid data for '$label'";
					$fieldErrorMessage = Filter::get($field, 'validation_error_message');
					if (!empty($fieldErrorMessage)) {
					    $errorMessage = base64_decode($fieldErrorMessage);
					}
					array_push($errors, array('errStr'=>$errorMessage, 'fieldName'=>$fieldName));
				}
            }
        }
    }
    return $errors;
}

/*
* Performs the field validation
* @param string The field value
* @param string The validation type
* @return boolean Whether or not the field value matches the validation type
*/

function formbuilder_do_validation($value, $validation, $option="") {
	switch ($validation) {
		case 'notempty':
		case 'notnull':
			return trim($value) == "" ? false : true ;
			break;
		case 'number':
			return ereg(SB_REGEX_NUM, $value);
			break;
		case 'email':
			return eregi(SB_REGEX_EMAIL, $value);
			break;
		case 'url':
			return preg_match(SB_REGEX_URL, $value);
			break;
		case 'minlength':
		    return strlen($value) >= $option ;
		    break;
		case 'maxlength':
			return strlen($value) <= $option ;
		    break;
		case 'regex':
		    return preg_match($option, $value);
		    break;
		default:
			return true;
			break;
	}
}

/*
* Builds the label element for a field
* @param object   The field object
* @return string  The HTML label element
*/

function formbuilder_label(&$field, $errors=array()) {
    global $Core;
    
    $attrs = array();
    $errorMarker = "";
    for ($i=0; $i<count($errors); $i++) {
        if ($errors[$i]['fieldName'] == Filter::get($field, 'title')) {
            $errorMarker = " <span class=\"field_error_marker\"><span>Error!</span></span>";
            $attrs['class'] = 'field_error_label';
        }
    }
    if (Filter::get($field, 'fieldtype') == "button") return null;
    $marker = Filter::get($field, 'validation') != '' ? '<span class="required">*</span>' : '' ;
    $label = Filter::get($field, 'label');
    $field_label = urldecode(base64_decode($label)) . " $marker";
    if (!empty($errorMarker)) {
        $field_label = "<span class=\"field_error_text\">" 
        . urldecode(base64_decode($label)) . " $marker $errorMarker" 
        . "</span>";
    }
    if (!empty($label)) {
        echo $Core->HTML->makeElement(
            'label',
            $attrs,
            $field_label
       );
    }
    echo '';
}

/*
* Determines if any of the fields are of type 'file' so the form type can be properly set
* @param array The form fields array
* @return boolean Whether or not any of the fields are of type 'file'
*/

function formbuilder_has_file_field($fields) {
    foreach ($fields as $field) {
        if ($field->fieldtype == "file") {
            return true;
        }
    }
    return false;
}

/*
* Prints the Form blurb the web page
* @param object  The form object
* @return void
*/

function formbuilder_blurb(&$form) {
    $blurb = Filter::get($form, 'blurb');
    if (!empty($blurb)) {
    	echo urldecode(base64_decode($blurb));
    }
}

/*
* Builds the input element for a field
* @param object   The field object
* @return string  The HTML input element
*/

function formbuilder_field($field, $Request) {
	global $Core;
	
	$label     = Filter::get($field, 'label');
	$name      = Filter::get($field, 'title');
	$fieldtype = Filter::get($field, 'fieldtype');
	$options   = Filter::get($field, 'options');
	$cols      = Filter::get($field, 'cols', 53);
	$rows      = Filter::get($field, 'rows', 6);
	$validate  = Filter::get($field, 'validation');
	$class     = Filter::get($field, 'class');
	
	$html = "";
	
	$list = array();
	if (trim($options) != "") {
		$tmp = explode(';', $options);
		for ($i=0; $i<count($tmp); $i++) {
		    if (trim($tmp[$i]) == "") continue;
		    array_push($list, formbuilder_split_option($tmp[$i]));
		}
	}
	
	switch ($fieldtype) {
		case 'textarea':
			$html .= $Core->HTML->makeElement(
                'textarea',
                array(
                    'rows'  => $rows,
                    'cols'  => $cols, 
                    'name'  => $name, 
                    'class' => $class
                ),
                $Request->get($name)
            ) . "\n" ;
			break;
		case 'radio':
		    $options = "";
		    for ($i=0; $i<count($list); $i++) {
		        $inputValue = Filter::get($list[$i], 'value');
		        $inputText = Filter::get($list[$i], 'text');
		        $attrs = array(
					'type'  => 'radio',
					'name'  => $name,
					'value' => $inputValue, 
					'class' => $class
				);
				if ($Request->get($name) == $inputValue) {
				    $attrs['checked'] = 'checked';
				}
				$options .= $Core->HTML->MakeElement(
				    'li',
				    array(),
				    $Core->HTML->MakeElement(
				        'input',
				        $attrs,
				        "",
				        0
				    ) . "&nbsp;" . ucwords($inputText) . "\n"
				) . "\n" ;
			}
		    $html .= $Core->HTML->MakeElement(
		        'ul',
		        array('class'=> $class),
		        $options
		    ) . "\n" ;
			break;
		case 'select':
		    $options = "";
		    for ($i=0; $i<count($list); $i++) {
		        $inputValue = Filter::get($list[$i], 'value');
		        $inputText = Filter::get($list[$i], 'text');
		        
		        $attrs = array('value' => $inputValue);
		        if ($Request->get($name) == $inputValue) {
		            $attrs['selected'] = 'selected';
		        }
		        $options .= $Core->HTML->makeElement(
		        	'option',
		        	$attrs,
		        	ucwords($inputText)
		        ) . "\n" ;
		    }
		    $attrs = array(
				'type'  => 'select', 
				'name'  => $name,
				'class' => $class
			);
		    if (Filter::get($field, 'multi_select') == "1") {
				$attrs['multiple'] = 'multiple';
			}
			$size = Filter::get($field, 'size', "1");
			if (!is_numeric($size)) $size = "1";
			if ($size == "0") $size = "1";
			if (!empty($size)) {
				$attrs['size'] = $size;
			}
		    $html .= $Core->HTML->makeElement(
		    	'select',
		    	$attrs,
		    	$options
		    ) . "\n" ;
			break;
		case 'checkbox':
		    $options = "";
		    for ($i=0; $i<count($list); $i++) {
		        $inputValue = Filter::get($list[$i], 'value');
		        $inputText = Filter::get($list[$i], 'text');
		        $attrs = array(
					'type'  => 'checkbox',
					'name'  => "{$name}[]",
					'value' => $inputValue, 
					'class' => $class
				);
				$vals = $Request->get($name);
				if (is_array($vals)) {
					if (in_array($inputValue, $vals)) {
						$attrs['checked'] = 'checked';
					}
				}
				$options .= $Core->HTML->MakeElement(
				    'li',
				    array(),
				    $Core->HTML->MakeElement(
				        'input',
				        $attrs,
				        "",
				        0
				    ) . "&nbsp;" . ucwords($inputText) . "\n"
				) . "\n" ;
			}
		    $html .= $Core->HTML->MakeElement(
		        'ul',
		        array('class'=>'checkbox_list'),
		        $options
		    ) . "\n" ;
			break;
		case 'file':
			$html .= $Core->HTML->makeElement(
				'input',
				array(
					'type'  => 'file',
					'name'  => $name,
					'value' => '',
					'clase' => $class
				),
				'',
				0
			) . "\n" ;
			break;
		case 'button':
			if (trim($label) != "") {
		        $label = urldecode(base64_decode($label));
		    }
			$attrs = array('name' => $name);
			if (!empty($class)) {
			    $attrs['class'] = $class;
			}
			$html .= $Core->HTML->makeElement(
			    'button',
			    $attrs,
			    $label,
			    1
			) . "\n" ;
		    break;
		case 'password':
		    $html .= $Core->HTML->MakeElement(
		        'input',
		        array(
		            'type'  => 'password',
		            'name'  => $name, 
		            'value' => "",
		            'class' => $class
		        ),
		        "",
		        0
		    ) . "\n" ;
		    break;
		case 'text':
		default:
			$html .=  $Core->HTML->makeElement(
                'input',
                array(
                    'type'  => 'text',
                    'name'  => $name,
                    'value' => $Request->get($name),
                    'class' => $class
               ),
                "",
                0
            ) . "\n"; 
			break;
	}
	echo $html;
}

/*
* Splits the select, radio or checkbox options into value -> text
* @param string  The option text of format foo:Foo Bar Baz;
* @return array  An array of format array('value' => 'foo', 'text' => 'Foo Bar Baz')
*/

function formbuilder_split_option($option) {
    if (is_array($option)) Core::Dump($option);
    $bits = explode(":", $option);
    if (count($bits) == 2) {
        $split = array('value' => $bits[0], 'text' => $bits[1]);
    }
    else {
    	$split = array('value' => $option, 'text' => $option);
    }
    return $split;
}


?>