<?php
namespace VNVE;
class Forms
{
	/*
		default values for inputfields
	*/
	public $formdefaults = array(
		'name' => '',
		'value' => '',
		'width' => '100%',
		'height' => '35px',
		'preview_width' => '200px',
		'preview_heigth' => '200px',
		'rows' => '4',				#number of rows of textarea
		'readonly' => FALSE,
		'collabel' => 'col-md-2',	#size of label
		'colinput' => 'col-md-6',	#size of inputfield
		'col' => 'col-md-3',
		'required' => TRUE,
		'autofocus' => FALSE,	#set cursur on input field
		'uploaderror' => '',
		'inline' => TRUE,			#label and input in one line
		'optionsinline' => TRUE,	#options in radio or checkbox in one line
		'group' => FALSE,			#label and input will not be followed by other items
		'dateformat' => 'yy-mm-dd',	#format of date
		'checkclass' => '',
		'otherclass' => '',
		'error' => 'input error',
		'type' => 'text',
		'popover' => '',			#text for a popover
		'placeholder' => '',		#placeholder input field
		'choose' => 'maak een keuze',	#keuze tekst
		'onchange' => '',			#jscript function after changing the input
		'confirm' => '',			#confirm message.
		'submit' => FALSE,		# submit after changing input
		'other' => ''				#other choice in radio form
	);
	#
	# button variables
	public $buttons = array();
	public $buttonclass =  "prana-button";
	public $buttoncol = "col-md-12";
	public $status = '';
	public $uploaderror;			# error after uploading a file
	/**
	 * calcfield - Add calculated field to avoid spamming
	 * als resultaat die gevalideerd moet worden wijzigt, wijzig dan ook forms.js
	 */
	public function CalcField()
	{
		$html = '';
		$html .= $this->Text(array("label"=>"Wat is drie plus vijf (in letters)", "id"=>"calcfield","width"=>"500px;","checkclass"=>"checkcalcfield","error"=>"vul het juiste getal in"));
		return($html);
	}
	public function ValidateCalcField()
	{
		if($_POST['calcfield'] == "acht") { return TRUE; }
		return FALSE;
	}
	public function Captcha()
	{
		$html = '';
		$html .= '<script src="https://www.google.com/recaptcha/api.js" async defer></script>';
		$html .= '<div class="g-recaptcha" data-sitekey="6LdryPESAAAAAOJU7PC5gHUPMRiV025xR9LzvLzJ"></div>';

		return($html);  
	}
	/**
	 * validate captcha/recaptcha V2
	 */
	public function ValidateCaptcha()
	{
		 // reCAPTCHA validation
		$verifyurl = 'https://www.google.com/recaptcha/api/siteverify';
		$secretAPIkey = '6LdryPESAAAAAA8cAVunUAfUBWk_obJlLWKep9rT';	#google secret API
		if(isset($_POST['g-recaptcha-response']) && !empty($_POST['g-recaptcha-response'])) 
		{
			# reCAPTCHA response verification
			$verifyResponse = file_get_contents($verifyurl . '?secret='.$secretAPIkey.'&response='.$_POST['g-recaptcha-response']);

			/**
			The response is a JSON object:

			{
				"success": true|false,      whether this request was a valid reCAPTCHA token for your site
				"score": number             the score for this request (0.0 - 1.0)
				"action": string            the action name for this request (important to verify)
				"challenge_ts": timestamp,  timestamp of the challenge load (ISO format yyyy-MM-dd'T'HH:mm:ssZZ)
				"hostname": string,         the hostname of the site where the reCAPTCHA was solved
				"error-codes": [...]        optional
			}
			*/
			$response = json_decode($verifyResponse);
			if($response->success) return true;
		}
		return false;
	}
/**
 * Captcha via joomla pluginkrijg nog niet aan de praat
 */
/*		$html = '';
		require_once(C2M_VENDOR_DIR . 'recaptchalib.php');
		$publickey = "6LdryPESAAAAAA8cAVunUAfUBWk_obJlLWKep9rT"; // you got this from the signup page
		$html .= recaptcha_get_html($publickey);


	public function Captcha()
	{
		$html = '';
		\JPluginHelper::importPlugin('captcha');
		$captcha_plugin = \JFactory::getConfig()->get('captcha',false);
		print_r($captcha_plugin);
if (!empty($captcha_plugin)){
	$html .= 'captcha';
    $captcha = \JCaptcha::getInstance($captcha_plugin);
    $field_id = 'captcha';
    print $captcha->display($field_id, $field_id, 'g-recaptcha');
}

return($html);
	}
		\JPluginHelper::importPlugin('captcha');
		$dispatcher = \Joomla\CMS\Factory::getApplication()->getDispatcher();
		$event = new \Joomla\Event\Event('onInit', ['dynamic_recaptcha_1']);
		print_r($event);
		$res = $dispatcher->dispatch($event);
		#$dispatcher = \Dispatcher::getInstance();
		#$dispatcher->trigger('onInit','dynamic_recaptcha_1');
		
		//html code inside form tag
		$html .= '<div id="dynamic_recaptcha_1"></div>';
		return($html);
	}
	*/
	public function Honeypot()
	{
		#
		# honeypot 
		#
		# The honeypot technique is a fast, effective way to prevent spam bots from submitting your forms. 
		# Spam bots love form fields and when they encounter a form field they will fill it out, even if the field is hidden from the user interface. 
		# To leverage this, you can create a form field that should be left blank, but hide it from human users. 
		# When the form is submitted you can check to see if there’s a value for the field and block the form submission. 
		#
		$html = '';
		$html .= '<div style="display: none">';
		$html .= '</label for="honeypot" </label>';
		$html .= '<input type="text" name="honeypot" id="honeypot" tabindex="40" placeholder="Leave Blank If Human" autocomplete="off"/>';
		$html .= '</div>';
		return($html);
	}
	#
	# button
	#
	public function DisplayButtons()
    {
        $html = '';
		#$html .= '<div class="' . $this->buttoncol . '">';	
		foreach ($this->buttons as $m)
        {
			$id = $m['id'];
            $value = $m['value'];
			$status = isset($m['status']) ? $m['status'] : $this->status;
			$onclick= isset($m['onclick']) ? $m['onclick'] : "";
			$class= isset($m['class']) ? $m['class'] : "";
			$html .= '<button id="' . $id . '" class="' . $this->buttonclass . ' ' . $class . '" name="' . $id . '" value="' . $id . '"';
			if($onclick) { $html .= 'onclick="' . $onclick . ';"'; }
			$html .= ' ' . $status . '>' . $value;
			$html .= '</button>';
			$html .= '&nbsp';
		}
		#$html .= '</div>';
        return $html;
    }
	/**
	 * Text - invoerveld voor tekst
	 * @param array $args[
	 * 'label' => (string) Label of inputfield
	 * 'id' => (string) element id
	 * 'name' => (string) element name
	 * 'value' => (string) beginwaarde van het invoerveld
	 * 'type' => type of input (default is 'text'
	 * 'required' => (Boolean) Input is required (default) or not
	 * 'readonly' => (Boolean) field is readonly
	 * 'collabel' => bootstrap position of label (in case of inline=TRUE)
	 * 'col' => bootstrap position (inline = FALSE)
	 * 'colinput' => bootstrap position of inputfield (inline = FALSE)
	 * 'inline' => (Boolean) label and field on one line
	 * 'group' => (Boolean) textbox is a group (only when sinline = TRUE)
	 * 'width' => size of inputfield
	 * 'checkclass' => (string) classname for checking content (see: forms.js)
	 * 'error' => (string) message in case of error
	 * 'popover' => (string) text for a popover
	 * 'placeholder' => placeholder for the inputfield
	 * ]
	 */
	public function Text($args)
	{
		$args = $this->parse_args( $args, $this->formdefaults );
		$html='';
		if(!$args["name"]) { $args["name"] = $args["id"]; }		# als name niet gedefinieerd is name = id
		if($args['required']) { $args["label"] .= "*"; }
		if($args['inline']== TRUE) 
		{
			if($args['group'] == FALSE) { $html .= '<div class="form-group row">'; }
			$html .= 	'<div class="' . $args["collabel"] .'">';
		}
		else 
		{ 
			$html .= '<div class="' . $args["col"] . '">';
			$html .= '<div class="control-label">';
		}
		$html .= '<label for="' . $args["id"] . '"';
		/*
		if($args["popover"]) 
		{
			$html .= 'data-toggle="popover" data-placement="top" title="popover on top" data-content="content"';
		}
		*/
		#{ $html .= ' class="hasPopover"  title="' . $args["popover"] . '"'; }
		$html .= '>' .  $args["label"] . '</label>';
		$html .= '</div>';	#end of label
		if($args['inline'] == TRUE) { $html .= '<div class="' . $args['colinput'] . '">'; }
		else { $html .= '<div class="controls">';}
		$args['name'] = $args['name'] ? $args['name'] : $args['id']; # name is id if not defined
		$html .= 	'<input class="form-control ' . $args['checkclass'] .'" type="' . $args["type"] . '" id="' . $args["id"] . '" name="' . $args["name"] . '" value="' . $args['value'] . '"';
		$html .= ' style="width:' . $args['width'] . '; height:' . $args['height'] . ';"';
		if($args['required']== TRUE) { $html .= ' required="required"'; }
		if($args['readonly'] == TRUE) { $html .= ' readonly="readonly"'; }
		if($args['autofocus'] == TRUE) { $html .= ' autofocus="autofocus"'; }
		if($args["placeholder"]) $html .= 'placeholder="' . $args["placeholder"] . '"';
		if($args['submit'] == TRUE) { $html .= ' onchange="submit()"'; }  #submit after change the input
		$html .= '>';
		$html .= '<span class="error_hide">'.$args['error'].'</span>'; #span for error field see: forms.js
		$html .= '</div>';
		if($args['group'] == FALSE) { $html .= '</div>'; }
		return($html);
	}
	/**
	 * TextArea - invoerbox voor tekst
	 * @param array $args[
	 * 'label' => (string) Label of inputfield
	 * 'id' => (string) element id
	 * 'name' => (string) element name
	 * 'value' => (string) beginwaarde van het invoerveld
	 * 'type' => type of input (default is 'text'
	 * 'required' => (Boolean) Input is required (default) or not
	 * 'readonly' => (Boolean) field is readonly
	 * 'collabel' => bootstrap position of label (in case of inline=TRUE)
	 * 'col' => bootstrap position (inline = FALSE)
	 * 'colinput' => bootstrap position of inputfield (inline = FALSE)
	 * 'inline' => (Boolean) label and field on one line
	 * 'width' => size of inputfield (...px)
	 * 'rows' => number of rows
	 * 'checkclass' => (string) classname for checking content (see: forms.js)
	 * 'error' => (string) message in case of error
	 * 'popover' => (string) text for a popover (todo)
	 * 'placeholder' => placeholder for the inputfield
	 * 
	 */
	public function TextArea($args)
	{
		$args = $this->parse_args( $args, $this->formdefaults );
		if(!$args["name"]) { $args["name"] = $args["id"]; }		# als name niet gedefinieerd is name = id
		$html='';
		if($args['required']) { $args["label"] .= "*"; }
		$html .= '<div class="form-group row">';
		$html .= 	'<div class="' . $args["collabel"] .'">';
		$html .= 		'<label for="' . $args["id"] . '"';
		$html .= '>' .  $args["label"] . '</label>';
		$html .= '</div>';
		$html .= 	'<div class="' . $args["colinput"] . '">';
		$html .= 	'<textarea class="form-control' . $args["checkclass"] .'" type="text" id="' . $args["id"] . '" name="' . $args["id"] . '"';
		$html .= ' rows="' . $args["rows"] .'"';
		$html .= ' style="width:' . $args["width"] . ';"';
		if($args["required"]) { $html .= ' required="required"'; }
		if($args["readonly"]) { $html .= ' readonly="readonly"'; }
		if($args["placeholder"]) $html .= 'placeholder="' . $args["placeholder"] . '"';
		$html .= '>';
		$html .= $args["value"];
		$html .= '</textarea>';
		$html .= 	'<span class="error_hide">'.$args["error"].'</span>';
		$html .= 	'</div>';
		$html .= '</div>';
		return($html);
	}
	/**
	 * Radio - maak een keuze mbv radio buttons.
	 * @param array $args[
	 * label => (string) Label of inputfield
	 * id => (string) element id
	 * value => (string) beginwaarde van het invoerveld
	 * pairs => array of key->values to choose e.g. array[labelvalue1=>$value1,labelvalue2=$value2,...]
	 * options => array() e.g. ["appel","peer","banaan"]
	 * other => choose another option if not in the list of options
	 * otherclass => class for other option (see forms.js-> CheckInput))
	 * othererror => Error when otherclass detects an error
	 * required => (Boolean) Input is required (default) or not
	 * readonly => (Boolean) field is readonly
	 * collabel => bootstrap position of label (in case of inline=TRUE)
	 * col' => bootstrap position (inline = FALSE)
	 * colinput => bootstrap position of inputfield (inline = FALSE)
	 * inline => (Boolean) label and field on one line
	 * optionsinline => All options in one line (paramater col should also be larger!!)
	 * ]
	 * related functions: forms.js -> DisplayElement()
	 * public function Checkboxes($args)
	 */
	public function Radio($args)
	{
		$html='';
		$args = $this->parse_args( $args, $this->formdefaults );
		if($args['required']) { $args["label"] .= "*"; }
		$id=$args["id"];
		$html='';
		#
		# label and contet in one line or label above the content
		#
		if($args["inline"] == TRUE) 
		{
			$html .= '<div class="form-group row">'; 						#open radio div in one row
			$html .= '<div class="' . $args["collabel"] .'">';				#open label div
		}
		else 
		{ 
			$html .= '<div class="' . $args["col"] . '">';					#open radio div label above content
			$html .= '<div class="control-label">';							#open label div
		}
		$html .= 	'<label for="radios">' . $args["label"] . '</label>';	#set label
		$html .= 	'</div>';												#close label div
		$html .= 	'<div class="' . $args['colinput'] . '">';				#open input div
		$otherid = $args["id"] . "other";
		#
		# pairs or options??
		#
		if(isset($args["options"]) && $args["options"])			#In case of options, key is the same as the value
		{
			$pairs = array_combine($args["options"],$args["options"]); #convert to assoc array
		}
		else
		{
			$pairs=$args["pairs"];		#this should be defined when there are no options
		}
		$isselected = FALSE;
		foreach($pairs as $key => $value)
		{
			$html .= $args['optionsinline'] ? '<div class="form-check form-check-inline">' : '<div class="form-check">';
			$selected="";
			if($value == $args['value']) 
			{	$selected = " checked";
				$isselected=TRUE;
			}
			$rid = $args["id"] . '_' . $value;
			if($args["optionsinline"] == FALSE) { $html .= '&nbsp;&nbsp;';} #anders komt de te veel naar links.
			$html .= 	'<input class="form-check-input" type="radio" id="' . $rid . '" name="' . $args['id'] . '" value="' . $value . '"' . $selected;
			if($args['required']) { $html .= ' required'; }
			if($args['readonly']) { $html .= ' disabled="disabled"'; }
			if($args['other']) { $html .= ' onclick="HideElement(\''.$otherid.'\')"';}
			$html .= '>';
			$html .= '<label class="form-check-label" for="'. $args['id'] . '">' . '&nbsp;&nbsp;' . $key . '&nbsp;&nbsp;</label>';
			$html .=		'</div>';
		}
		#
		# element for other selection
		#
		if($args["other"]) 
		{
			$html .= $args['optionsinline'] ? '<div class="form-check form-check-inline">' : '<div class="form-check">';
			$othervalue="";
			if($args["value"] && $isselected == FALSE)
			{
				$othervalue = $args["value"];
				$selected = " checked";
			}
			$option = "other";
			$rid = $args["id"] . '_' . $option;
			$otherid = $args["id"] . $option;	
			if($args["optionsinline"] == FALSE) { $html .= '&nbsp;&nbsp;';} #anders komt de te veel naar links.
			$html .= 	'<input class="form-check-input" type="radio" id="' . $rid . '" name="' . $id . '" value="' . $option . '"';
			if($args['required']) { $html .= ' required'; }
			if($args['readonly']) { $html .= ' disabled="disabled"'; }
			$html .= ' onclick="DisplayElement(\''.$otherid.'\')"';
			$html .= '>';
			$html .= '<label class="form-check-label" for="'. $args['id'] . '">' . '&nbsp;&nbsp;' . $args['other'] . '</label>'; #set label
			#
			# input field for another option
			#
			$html .=  '&nbsp;&nbsp;';
			$html .= '<input class="' . $args['checkclass'] . ' type="text" id="' . $otherid .'" name="' . $otherid . '" value="' . $othervalue;
			$html .= $othervalue ? '" style="display:block"' : '" style="display:none"';
			$html .= '>';
			if(isset($args['error']))
			{
				$html .= '<span class="error_hide">'.$args['error'].'</span>'; #span for error field see: forms.js
			}
			$html .=		'</div>';
		}
		$html .=		'</div>';	#close input div
		$html .=		'</div>';	#close radio div
		return($html);
	}
	/**
	 * Check - make checkbox
	 * @param array $args[
	 * 'label' => (string) Label of inputfield
	 * 'id' => (string) element id
	 * 'name' => (string) element name
	 * 'value' => (string) beginwaarde van het invoerveld
	 * 'checked' => (Boolean) default is checked
	 * 'collabel' => bootstrap position of label (in case of inline=TRUE)
	 * 'col' => bootstrap position (inline = FALSE)
	 * 'colinput' => bootstrap position of inputfield (inline = FALSE)
	 * 'inline' => (Boolean) label and field on one line
	 * 'error' => (string) message in case of error
	 * ]
	 */
	public function Check($args)
	{
		$args = $this->parse_args( $args, $this->formdefaults );
		
		$html='';
		$args['name'] = $args['name'] ? $args['name'] : $args['id']; # name is id if not defined
		$html .= '<div class="form-group row">';
		$html .= 	'<div class="' . $args["collabel"] .'">';
		$html .= 		'<label for="' . $args["id"] . '"';
		$html .= '>' .  $args["label"] . '</label>';
		$html .= '</div>';
		$html .= 	'<div class="' . $args["colinput"] .'">&nbsp;&nbsp;&nbsp;&nbsp;';
		$html .= '<input class="form-check-input" type="checkbox" id="' . $args["id"] . '" name="' . $args["name"] . '" value="' . $args['value'] . '"';
		if($args['checked']== TRUE) { $html .= ' checked'; }
		if($args['confirm']) { $html .= 'onclick="return confirm(\'' . $args["confirm"] . '\')"'; }
		$html .= '>';
		$html .= '<span class="error_hide">'.$args['error'].'</span>'; #span for error field see: forms.js
		$html .= '</div>';
		$html .= '</div>';
		return($html);
	}
	/**
	 * Check - make checkboxes check 0ne or more boxes.
	 * @param array $args[
	 * 'label' => (string) Label of inputfield
	 * 'id' => (string) element id
	 * 'name' => (string) element name
	 * 	$options=array() e.g. ["appel","peer","banaan"]
	 * other => choose another option if not in the list of options
	 * otherclass => class for other option (see forms.js-> CheckInput))
	 * othererror => Error when otherclass detects an error

	 * 'value' => encoded choosen options
	 * 'row' => set boxes in one row
	 * 'collabel' => bootstrap position of label (in case of inline=TRUE)
	 * 'col' => bootstrap position (inline = FALSE)
	 * 'colinput' => bootstrap position of inputfield (inline = FALSE)
	 * 'inline' => (Boolean) label and field on one line
	 * optionsinline => All options in one line (paramater col should also be larger!!)

	 * 'error' => (string) message in case of error
	 * ]
	 */
	
	public function Checkboxes($args)
	{
		#echo 'checkboxes<br>';
		#print_r($args);
		#echo '<br>';
		$args = $this->parse_args( $args, $this->formdefaults );
		if($args["required"]) { $args["label"] .= "*"; } 
		$id=$args["id"];
		$html='';
		if($args["inline"] == TRUE) 
		{
			$html .= '<div class="form-group row">'; 
			$html .= 	'<div class="' . $args["collabel"] .'">';
		}
		else 
		{ 
			$html .= '<div class="' . $args["col"] . '">';
			$html .= '<div class="control-label">';
		}
		$html .= 		'<label for="checkbox">' . $args["label"] . '</label>';
		$html .= 	'</div>';
		$html .= 	'<div class="' . $args["colinput"] . '">';
		$values = [];
		if($args["value"]) { $values = json_decode($args["value"]); }	#get current values]
		foreach ($args["options"] as $option)
		{
			$html .= $args['optionsinline'] ? '<div class="form-check form-check-inline">' : '<div class="form-check">';
			$selected = in_array($option,$values) ? " checked" : '';
			$rid = $args["id"] . '_' . $option;
			if($args["optionsinline"] == FALSE) { $html .= '&nbsp;&nbsp;';} #anders komt de te veel naar links.
			$html .= 	'<input class="form-check-input" type="checkbox" id="' . $rid . '" name="' . $id . '[]" value="' . $option . '" ' . $selected;
			if($args["readonly"]) { $html .= ' disabled="disabled"'; }
			$html .= '>';
			$html .=  '&nbsp;&nbsp;' . $option . '&nbsp;&nbsp;';
			$html .=		'</div>';
		}
		#
		# element for other selection
		#
		if($args["other"]) 
		{ 
			$html .= $args['optionsinline'] ? '<div class="form-check form-check-inline">' : '<div class="form-check">';
			$dvalue = array_diff($values,$args['options']);
			$othervalue = current($dvalue);
			if($othervalue) { $selected = " checked";}
			#echo '<br>othervalue=' . $othervalue . '<br>';
			$option = "other";
			$rid = $args["id"] . '_' . $option;
			$otherid = $args["id"] . $option;
			if($args["optionsinline"] == FALSE) { $html .= '&nbsp;&nbsp;';} #anders komt de te veel naar links.
			$html .= 	'<input class="form-check-input" type="checkbox" id="' . $rid . '" name="' . $id . '[]" value="' . $option . '" ' . $selected;
			#$html .= 	'<input class="form-check-input" type="checkbox" id="' . $rid . '" name="' . $id . '[]" value="' . $option . '" ' . $selected;
			$html .= ' onclick="DisplayElement(\''.$otherid.'\')"';
			$html .= '>';
			$html .=  '&nbsp;&nbsp;' . $args["other"] . '&nbsp;&nbsp;';		#label for other field
			$html .= '<input class="' . $args['checkclass'] . ' type="text" id="' . $otherid .'" name="' . $otherid . '" value="' . $othervalue;
			$html .= $othervalue ? '" style="display:block"' : '" style="display:none"';
			$html .= '>';
			if(isset($args['error'])) { $html .= '<span class="error_hide">'.$args['error'].'</span>';  }#span for error field see: forms.js
			$html .=		'</div>';

		}
		$html .=		'</div>';
		$html .=		'</div>';
		return($html);
	}
	/**
	 * ConfirmCheck - Checkbox to confirm genereal conditions (required to check)
	 * @param array $args[
	 * 'label' => (string) Label of inputfield
	 * 'id' => (string) element id
	 * ]
	 */
	public function ConfirmCheck($args)
	{	
		$html='';
		$args['name'] = isset($args['name']) ? $args['name'] : $args['id']; # name is id if not defined
        $html .= '<div class="form-group row">'; 
        $html .= '<div class="col-md-1">';
		$html .= '<input type="checkbox" id="' . $args["id"] . '" name="' . $args["name"] . '" required value="check">';
        $html .= '</div>';
        $html .= '<div class="col-md-11">';
        $html .= '<label for="' . $args["id"] . '">&nbsp;' . $args["label"] . '</label>';
        $html .= '</div>';
        $html .= '</div>';
		return($html);
	}

	/**
	 * Date - invoerveld voor een datum
	 * @param array $args[
	 * label => (string) Label van het invoerveld
	 * value => (string) beginwaarde van het invoerveld
	 * format => (string) datum formaat
	 * colinput => (string) size of inputfield
	 * width => size of inputfield (...px)
	 * ]
	 * datepicker gedefinieerd in forms.js
	 */
	public function Date($args)
	{
		$args = $this->parse_args( $args, $this->formdefaults );
		if($args['required']) { $args["label"] .= "*"; } 
		$checkclass = isset($args["check"]) ? ' ' . $args["check"] : ''; # add check class if given so that javascript can test the content
		
		$html='';
		if($args['inline']== TRUE) 
		{
			if($args['group'] == FALSE) { $html .= '<div class="form-group row">'; }
			$html .= 	'<div class="' . $args["collabel"] .'">';
		}
		else 
		{ 
			$html .= '<div class="' . $args["col"] . '">';
			$html .= '<div class="control-label">';
		}		
		$html .= 		'<label for="' . $args["id"] . '"';
		#if(isset($args["popover"])) { $html .= ' class="hasPopover"  title="' . $args["popover"] . '"'; }
		$html .= 		'>' .  $args["label"] . '</label>';
		$html .=	'</div>';
		$html .= 	'<div class="' . $args['colinput'] . '">';
		$html .= '<input class="form-control ' . $args["checkclass"] . ' datepicker" type="text" id="' . $args["id"] . '" name="' . $args["id"]. '" style="width:' . $args["width"] . '" value="' . $args["value"] .'"';
		#$html .= '<input class="form-control" type="date" placeholder="dd-mm-yyyy" min="1900-01-01" max="2022-01-01" id=" ' . $args["id"] . '" name="' . $args["id"]. '" style="width:' . $args["width"] . '" value="' . $args["value"] .'"';
		#
		# set event on classes checkclass and datepicker in jquery !!
		#
		if($args['required']) { $html .= ' required="required"'; }
		if($args['readonly']) { $html .= ' readonly="readonly"'; }
		if(isset($args["placeholder"])) { $html.= ' placeholder="' . $args["placeholder"] . '"'; }
		
		$html .= 	'>';
		$html .= 	'<span class="error_hide">'.$args['error'].'</span>';
		$html .= 	'</div>';
		if($args['group'] == FALSE) { $html .= '</div>'; }
		return($html);
	}
	
	/**
	 * Dropdown - maak een keuze mbv een dropdown box
	 * @param array $args[
	 * 'label' => (string) Label of inputfield
	 * 'id' => (string) element id
	 * 'name' => (string) element name
	 * 'value' => (string) beginwaarde van het invoerveld
	 *  options = array of options objects to choose e.g. array[labelvalue1=>$value1,labelvalue2=$value2,...]
	 * 'required' => (Boolean) Input is required (default) or not
	 * 'readonly' => (Boolean) field is readonly
	 * 'collabel' => bootstrap position of label (in case of inline=TRUE)
	 * 'col' => bootstrap position (inline = FALSE)
	 * 'colinput' => bootstrap position of inputfield (inline = FALSE)
	 * 'inline' => (Boolean) label and field on one line
	 * 'choose' => keuze tekst bv Maak een keuze
	 * ]
	 */
	public function Dropdown($args)
	{
		$args = $this->parse_args( $args, $this->formdefaults );
		if(!$args["name"]) { $args["name"] = $args["id"]; }		# als name niet gedefinieerd is name = id
		if($args['required']) { $args["label"] .= '*'; } 
		$html='';
		if($args['inline']== TRUE) 
		{
			if($args["group"] == FALSE) { $html .= '<div class="form-group row">'; }
			$html .= 	'<div class="' . $args["collabel"] .'">';
		}
		else 
		{ 
			$html .= '<div class="' . $args["col"] . '">';
			$html .= '<div class="control-label">';
		}
		$html .= 		'<label for="checkbox">' . $args["label"] . '</label>';
		$html .= 	'</div>';
		if($args['inline'] == TRUE) { $html .= '<div class="' . $args['colinput'] . '">'; }
		else { $html .= '<div class="controls">';}
		$options = "";
		$options .= '<option value="" selected>' . $args["choose"] . '</option>';   # keuze tekst
		foreach($args["options"] as $key => $value)
		{
			$selected = $value == $args["value"] ? " selected=selected" : "";
			$options .= '<option value="' . $value . '" ' . $selected . '>' . $key . '</option>';
		}
		$html .= '<select id="' . $args['id'] . '" name="' . $args['name'] . '" style="padding:0px;width:' . $args['width'] . ';height:' . $args['height'] . ';"';
		if($args['required']) { $html .= ' required'; }
		if($args['readonly']) { $html .= ' readonly="readonly"'; }
		if($args['submit'] == TRUE) { $html .= ' onchange="submit()"'; }  #submit after change the input
		$html .= '>';
		$html .= $options;
		$html .= '</select>';
		$html .= '</div>';
		$html .= '</div>';

		if($args["group"] == FALSE) { $html .= '</div>'; }
		return($html);
	}

	/**
	 * File - Read a file.
	 * @param array $args[
	 * 'label' => (string) Label of inputfield
	 * 'id' => (string) element id
	 * 'name' => (string) element name
	 * 'value' => (string) beginwaarde van het invoerveld
	 *  options = array of options objects to choose e.g. array[labelvalue1=>$value1,labelvalue2=$value2,...]
	 * 'required' => (Boolean) Input is required (default) or not
	 * 'readonly' => (Boolean) field is readonly
	 * 'collabel' => bootstrap position of label (in case of inline=TRUE)
	 * 'col' => bootstrap position (inline = FALSE)
	 * 'colinput' => bootstrap position of inputfield (inline = FALSE)
	 * 'inline' => (Boolean) label and field on one line
	 * 'accept' =>  comma-separated list of one or more file types, describing which file types to allow
	 * 'width' , 'height' => size of inputfield (...px)
	 * ]
	 */
	public function File($args)
	{
		$args = $this->parse_args( $args, $this->formdefaults );
		if(!$args["name"]) { $args["name"] = $args["id"]; }		# als name niet gedefinieerd is name = id
		if($args['required']) { $args["label"] .= '*'; } 
		$html='';
		$r='';
		$checkclass = isset($args["check"]) ? ' ' . $args["check"] : ''; # javascript test input on this class
		$html .= '<div class="form-group row">';
		$html .= 	'<div class="' . $args["collabel"] .'">';
		$html .= 		'<label for="' . $args["id"] . '"';
		$html .= 		'>' .  $args["label"] . '</label>';
		$html .=	'</div>';
		$html .= 	'<div class="' . $args["colinput"] . '">';
		$html .= '<input type="file" id="' . $args["id"] . $checkclass .'" name="' . $args['name'] . '" value="' . $args["value"] . '"';
		$html .= 'style="width:' . $args["width"] . '; height:' . $args['height'] . ';"';
		if($args["required"]) { $html .= ' required="required"'; }
		if($args["readonly"]) { $html .= ' readonly="readonly"'; }
		if($args["onchange"]) { $html .= ' onchange="' . $args["onchange"] . '"'; }
		if($args["accept"]) { $html .= ' accept="' . $args["accept"] . '"'; }
		$html .= $args["placeholder"] ? '' : ' placeholder="' . $args["placeholder"] . '"';
		$html .= 	'>';
		$html .= 	'</div>';
		$html .= '</div>';
		return($html);
	}

	#
	# Image
	# upload an image and show it directly
	# $args["uploads"] - upload map of images
	# $args["value"] - current image
	# $args["label"] = label of text box
	# $args["id"] = id and name
	# $args["width"] = width of image
	# $args["heigth"] = width of image
	# $args["required"] = 1 if the box is required
	# $args["collabel"] = bootstrap position label 
	# $args["accept"] = Only accept certain files (e.g. ".jpg,.jpeg")
	#

	public function Image($args)
	{
		$html='';
		$args = $this->parse_args( $args, $this->formdefaults );
		if(!$args["name"]) { $args["name"] = $args["id"]; }		# als name niet gedefinieerd is name = id
		if($args['required']) { $args["label"] .= "*"; }
		if($args['inline']== TRUE) 
		{
			if($args['group'] == FALSE) { $html .= '<div class="form-group row">'; }
			$html .= 	'<div class="' . $args["collabel"] .'">';
		}
		else 
		{ 
			$html .= '<div class="' . $args["col"] . '">';
			$html .= '<div class="control-label">';
		}
		$html .= '<label for="' . $args["id"] . '"';
		$html .= '>' .  $args["label"] . '</label>';
		$html .= '</div>';	#end of label
		if($args['inline'] == TRUE) { $html .= '<div class="' . $args['colinput'] . '">'; }
		else { $html .= '<div class="controls">';}

				#
		# image element to place image in it
		#
		$uploads = $args['uploads'];
		$photo_url = \JURI::base() . $uploads  . '/' . $args["value"];
		$photo_file = JPATH_SITE  . '/' . $uploads  . '/' . $args["value"];
		$html .= '<img id="showimage" src="' . $photo_url . '?' . filemtime($photo_file) .'" width="' . $args["preview_width"] .'" height="' . $args["preview_heigth"] . '" alt="foto">';
		$html .= '<input type="file" id="' . $args["id"] . '" class="form-control showimage" name="' . $args["id"] . '" value="' . $args["value"] . '"';
		$html .= ' style="' . $args["width"];
		if($args["required"]) { $html .= ' required="required"'; }
		if(isset($args["accept"])) { $html .= ' accept="' . $args["accept"] . '"'; }
		$html .= '>';
		$html .= '</div>';
		if($args['group'] == FALSE) { $html .= '</div>'; }
		return($html);
	}
	/*
	* upload the selected file
	* @param array $args[
	* targetdir = directory to put the file in
	* file = file element in $_FILES
	* filetypes = legal filetypes seperated by , e.g.: doc,docx,pdf
	* maxkb = maximum size of file in Kb
	* overwrite=1 (overwrite existing file allowed)
	* filename = filename (without extension), if not defined the original filename of the uploaded file is given
	*			extension is extension of original file
	* prefix=unique prefix to force unique filename (optional)
	* return value TRUE or FALSE
	* If FALSE message in uploaderror:
	*  Bad filetype
	*  file exists
	*  file too big
	*  File cannot be uploaded
	*/
	public function UploadFile($args) : bool
	{
		if(!isset($args["file"])) { $this->uploaderror = "file attribute not defined"; return(FALSE); }
		$file = $args["file"];
		$prefix = isset($args["prefix"]) ? $args["prefix"] : "";
		$overwrite = isset($args["overwrite"]) ? $args["overwrite"] : FALSE;
		$ext = pathinfo($file['name'], PATHINFO_EXTENSION);
		$filename = isset($args["filename"]) ? $args["filename"] . '.' . $ext : basename($file["name"]);
		$targetfile = $args["targetdir"] . '/' . $prefix . $filename;
		if(isset($args["filetypes"]))
		{
			$types=explode(",",$args['filetypes']);
			$found = FALSE;
			foreach($types as $t) { if($t == $ext) { $found=TRUE; } }
			if($found == FALSE) { $this->uploaderror = "bad filetype"; return(FALSE); }
		}
		
		if(isset($args["maxkb"]))
		{
			$fileSize = $file["size"];
			$maxsize = $args["maxkb"] * 1000;
			if($fileSize > $maxsize) { $this->uploaderror = " file too big"; return(FALSE); }
		}
		if($overwrite == FALSE && file_exists($targetfile)) { $this->uploaderror = "file exists"; return(FALSE); }
		if (!move_uploaded_file($file['tmp_name'], $targetfile)) { $this->uploaderror = "cannot upload"; return(FALSE); }
		return(TRUE);
	}
	#
	# download a file
	#
	public function DownloadFile($file)
	{
		$html = '';
		if(!file_exists($file))
		{
			$error = sprintf('bestand %s bestaat niet',$file);
			$html .= '<div class="isa_error" >' . $error . '</div>';
			return($html);
		}
		$pathinfo=pathinfo($file);
		#print_r($pathinfo);
		$extension=$pathinfo['extension'];
		$basename =$pathinfo['basename'];
		#echo "extension=" . $extension;
		#$form .= '<br>basename='.$basename.'extension="'.$extension.'"';
		if($extension == "txt") { $type = "Content-type: text/plain"; }
		elseif($extension == "pdf") { $type = "Content-type:application/pdf"; }
		else					{ $type = "application/octet-stream"; }
		#$form .= '<br>basename='.$basename.'extension="'.$extension.'"'.'type=' . $type;
		//ob_end_clean(); - Potential fix underneath.
		if (ob_get_contents()) ob_end_clean();
		if(PRANA_CMS == "joomla") { $app = \JFactory::getApplication(); }
		header('Pragma: public');
		header('Expires: 0');
		# caching doesnot know if important
		header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
		header('Cache-Control: public');
		header('Content-Description: File Transfer');
		header('Content-Disposition: attachment; filename="'.$basename.'"');
		header($type);
		header('Content-Length: '.filesize($file));
		#ob_clean();
		#ob_end_flush();		
		readfile($file);
		if(PRANA_CMS == "joomla") { $app->close(); }
		return($html);
	}
	#
	# StoreImage
	# store the image in the map 
	# $args["uploads"] - upload map of images
	# $args["id"] = id and name
	# $args["name"] = name of image
	# $args["width"] = wiidth of image
	public function StoreImage($args)
	{
		$map = ABSPATH . $args["uploads"];
		echo '<br>map=' . $map . 'id=' . $args['id'];
		echo '<br>';
		#print_r($_FILES);
		if(isset($_FILES[$args["id"]]))
		{
			echo '<br>startupload';
			if (move_uploaded_file($_FILES[$args["id"]]['tmp_name'], $map))
			{
				return(TRUE);
			}
			return(FALSE);
		}
		return(FALSE);
	}
	#
	# resize the image 
	#
	public function resize_image($file, $w, $h, $crop=FALSE) 
	{
		list($width, $height) = getimagesize($file);
		$r = $width / $height;
		if ($crop) 
		{
			if ($width > $height) 
			{
				$width = ceil($width-($width*abs($r-$w/$h)));
			} 
			else 
			{
				$height = ceil($height-($height*abs($r-$w/$h)));
			}
			$newwidth = $w;
			$newheight = $h;
		} 
		else 
		{
			if ($w/$h > $r) 
			{
				$newwidth = $h*$r;
				$newheight = $h;
			} 
			else 
			{
				$newheight = $w/$r;
				$newwidth = $w;
			}
		}
		$src = imagecreatefromjpeg($file);
		$dst = imagecreatetruecolor($newwidth, $newheight);
		imagecopyresampled($dst, $src, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);
		imagejpeg( $dst, $file );
		return;
	}
	/**
     * Make a form
	 * $vargs['form'] = xml file of form
	 * $vargs['values'] = current value list
	 * Fields almost the same as in https://docs.joomla.org/Form_field
	 * 
     */
	public function MakeForm($vargs)
	{
		$html = '';
		if(isset($vargs["values"])) { $values = $vargs["values"]; }
        foreach($vargs['form'] as $fieldset)
        {
			$s = $fieldset->attributes();
			$inrow=FALSE;
            foreach($fieldset as $field)
            {
				$a = $field->attributes();
				# width in form-form is in mm in pixels: 5*
				$width = isset($a->size) ? $a->size * 5 . "px;": $this->formdefaults['width']; 	#width of inputfield is 10 * variablesize or 300
				$args = array();
				$args += ["width"=>$width];
				$args += ["label"=>(string)$a->label];
				$args += ["id"=>(string)$a->name];
				#$args += ["required"=>(string)$a->required];
				if(isset($a->rows)) { $args += ["rows" => $a->rows]; }		#used for textarea
				if(isset($a->options)) { $args += ["options" => explode(',',$a->options)]; }
				if(isset($a->pairs)) { $args += ["pairs" => json_decode($a->pairs)]; }
				if(isset($a->globaloptions)) { $args += ["options" => explode(',',$GLOBALS[$a->globaloptions])]; }
				if(isset($a->globaldirectory)) { $args += ["uploads" => $GLOBALS[$a->globaldirectory]]; }
				if(isset($a->directory)) { $args += ["uploads" => $a->directory]; }			#upload map for images
				if(isset($a->preview_width)) { $args += ["preview_width" => $a->preview_width]; }			# size of image
				if(isset($a->preview_heigth)) { $args += ["preview_width" => $a->preview_heigth]; }
				if(isset($a->other)) { $args += ["other" => (string)$a->other]; }
				if(isset($a->accept)) { $args += ["accept" => $a->accept]; }		#accepted files for formfield file
				if(isset($a->required))
				{
					if($a->required == "false") {$args += ["required"=>FALSE];}
					if($a->required == "true") {$args += ["required"=>TRUE];}
				}
				if(isset($a->readonly))
				{
					if($a->readonly == "false") {$args += ["readonly"=>FALSE];}
					if($a->readonly == "true") {$args += ["readonly"=>TRUE];}
				}
				if(isset($a->optionsinline))
				{
					if($a->optionsinline == "false") {$args += ["optionsinline"=>FALSE];}
					if($a->optionsinline == "true") {$args += ["optionsinline"=>TRUE];}
				}
				if(isset($a->colinput)) { $args += ["colinput"=>$a->colinput];}
				if(isset($a->collabel)) { $args += ["collabel"=>$a->collabel];}
				if($s->option == "inrow")							#multiple fields in one line
				{
					if($inrow == FALSE) $html .= '<div class="row">';
					$args += ["group"=>TRUE];
					$inrow=TRUE;
				}
				if(isset($values[(string)$a->name]) && $values[(string)$a->name]) { $args += ["value"=>(string)$values[(string)$a->name]]; }
				elseif(isset($a->default)) { $args += ["value"=>(string)$a->default]; }
				if(isset($a->class))			#used to define the client-side validation
				{
					$checkclass = 'check' . (string)$a->class;
					$args += ["checkclass"=>$checkclass];
				}
				if(isset($a->classerror))			#used to define the client-side validation
				{
					$args += ["error"=>$a->classerror];
				}
				#$html .= print_r($args,TRUE);
				switch ($a->type)
				{
					case "text":
					$html .= $this->Text($args);
					break;

					case "textarea":
					$html .= $this->TextArea($args);
					break;
	
					case "radio":
					$html .= $this->Radio($args);
					break;
					
					case "checkboxes":
					$html .= $this->Checkboxes($args);
					break;

					case "date":
					#$html .= print_r($args,TRUE);
					$html .= $this->Date($args);
					break;

					case "file":
					$html .= $this->File($args);
					break;

					case "image":
					$html .= $this->Image($args);

					break;
	
				}
            }
			if($s->option == "inrow") { $html .= '</div><br>';}
			else { $html .= '<hr style="border: none; border-bottom: 3px solid black;">'; }
        }
        return($html);
	}
	
	/**
	 * Print the result of a completed form
	 * $args['xml'] = xml form
	 * $args['output'] = outputform list,table or csv file
	 * $args['file'] = the fiule in case of csv
	 */
	public function PrintForm($args)
	{
		$html = '';
		switch ($args["output"])
		{
			case "list":
			foreach($args['form'] as $fieldset)
			{
				foreach($fieldset as $field)
				{
					$a = $field->attributes();
					$label = $a->label;
					$content = $this->FormContent($a);
					$html .= sprintf("%30s : %s<br>",$label,$content);
				}
			}
			break;

			case "table":
			$html .= '<table class="compacttable">';
			foreach($args['form'] as $fieldset)
			{
				foreach($fieldset as $field)
				{
					$html .= '<tr class="compacttr">';
					$a = $field->attributes();
					$label = $a->label;
					$content = $this->FormContent($a);
					$html .= sprintf('<td class="compacttd">%s</td><td class="compacttd">%s</td>',$label,$content);
				}
				$html .= '</tr>';
			}
			$html .= '</table>';
			break;

			case "pdf":
			require_once(VNVE_VENDOR_DIR . 'fpdf186/fpdf.php' );
			#
			# define constants
			#
			$topmargin=10;
			$leftmargin=10;
			$linespace=8;
			$halflinespace=5;
			$fontsize_h2 = 14;
			$fontsize_normal = 12;
			#
			$pdf = new FPDF('P','mm','A4');
			$pdf->AddPage();
			$y=$topmargin;
			#
			# header
			#
			$pdf->setXY($leftmargin,$y);
			$pdf->SetFont('Arial','B',$fontsize_h2);
			$pdf->Cell(160,10,$args["header"]);
			#
			# formitems
			$pdf->SetFont('Arial','B',$fontsize_normal);
			$y += $linespace;
			foreach($args['form'] as $fieldset)
			{
				foreach($fieldset as $field)
				{
					$a = $field->attributes();
					$label = $a->label;
					$content = $this->FormContent($a);
					$pdf->setXY($leftmargin,$y);
					#
					# weet niet hoe in pdf regels automatisch afgebroken kunnen worden zoals in tabellen.
					# dus doen we het zelf 
					#
					$splitlabels = pranaStrSplit($label,40,' ');
					$splitcontent = pranaStrSplit($content,40,',');
					$pdf->Cell(80,10,$splitlabels[0]);
					$pdf->Cell(5,10,' ');
					$pdf->Cell(120,10,$splitcontent[0]);
					$firstlabel = 1;
					$linecontent=1;
					foreach($splitlabels as $label)
					{
							if($firstlabel)
							{
								$firstlabel=0;
								continue;
							}
							$y += $halflinespace;
							$pdf->setXY($leftmargin,$y);
							$pdf->Cell(100,10,$label);
							if(isset($splitcontent[$linecontent]))
							{
								$pdf->Cell(5,10,' ');
								$pdf->Cell(120,10,$splitcontent[0]);
								$linecontent++;
							}
					}
					#
					# zijn er nog afgebroken content regels
					#
					while(isset($splitcontent[$linecontent]))
					{
						$y += $halflinespace;
						$pdf->setXY($leftmargin,$y);
						$pdf->Cell(80,10,' ');
						$pdf->Cell(5,10,' ');
						$pdf->Cell(120,10,$splitcontent[$linecontent]);
						$linecontent++;
					}
					$y += $linespace;
				}
			}
			$pdf->Output($args["file"],'F');
			break;

			case "csv":
			$fp = fopen($args["file"],"w") or die("Unable to open file!");	#open file for writing csv records
			foreach($args['form'] as $fieldset)
			{
				foreach($fieldset as $field)
				{
					$a = $field->attributes();
					$label = $a->label;
					$content = $this->FormContent($a);
					$lbuf = $label . ';' . $content .  "\n";
					fwrite ($fp,$lbuf);
				}
			}
			break;
		}
		return($html);
	}
	/**
	 * Get the content of a formfield in order to get otherfields in radio and checkboxes
	 */
	public function FormContent($a)
	{
		$id = (string)$a->name;
		$type = (string)$a->type;
		if(!isset($_POST[$id])) { return("empty"); }
		#
		# get the correct content in case of other fields in radio and checkboxes fields
		#
		$content = $a->type == 'checkboxes' ? json_encode($_POST[$id]) : $_POST[$id]; # in case of checkboxes encode the content
		$other = $id . 'other';
		if(isset($_POST[$other])) #oher option is defined
		{
			if($a->type == 'radio')
			{
				$content = $_POST[$other];		#if other field is used take that field
			}
			if($a->type == 'checkboxes')	#add other field to checkbox
			{
				$values = array();
				foreach($_POST[$id] as $item)
				{
					if($item != "other")
					{
						array_push($values,$item);
					}
					if($item == "other")
					{
						array_push($values,$_POST[$other]);		#If other field is checked , take content other
					}
				}
				$content = json_encode($values);
			}
		}
		return($content);
	}
	function parse_args($nargs,$default)
	{
		$args=$default;
		foreach ($nargs as $arg=>$value)
		{
			$args[$arg] = $value;
		}
		return($args);
	}
}

?>