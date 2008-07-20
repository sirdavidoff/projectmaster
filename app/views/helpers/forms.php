<?php
/**
 * Helper methods for generating advanced HTML (and AJAX) forms.
 *
 * @package ppc
 * @subpackage ppc.helpers
 * @author David Roberts
 */

/**
 * Helper methods for generating advanced HTML (and AJAX) forms.
 *
 * @package ppc
 * @subpackage ppc.helpers
 * @author David Roberts
 */

uses('view/helpers/form');
class FormsHelper extends FormHelper {

	var $helpers = array('Html', 'Javascript', 'Ajax');
	
	/**
	 * Variable that tracks whether the LDF Javascript file has been included, and the
	 * details of the available languages have been passed to it (see _initLDF())
	 *
	 * @var string
	 */
	
	var $LDFScriptIncluded = false;

	/**
	 * Starts a form. Creates the <form> tag and includes some
	 * javascript necessary for FormsHelper::error() to work properly.
	 * This function also adds hidden javascript if the form is a login
	 * form and we want to test whether javascript is enabled in Cauth
	 *
	 * @param string $model 
	 * @param string $options 
	 * @return void
	 * @author David Roberts
	 */

	function create($model = null, $options = array())
	{
		$tag = parent::create($model, $options);

		if(!isset($options['controller'])) $options['controller'] = $this->params['controller'];
		if(!isset($options['action'])) $options['action'] = $this->params['action'];
		
		
		$jsCheckCode = '';
		
		// Check whether we are creating a login form
		$cauth = $this->params['cauth'];
		if(isset($cauth) && $cauth->_normalizeUrl($cauth->loginAction) == $cauth->_normalizeUrl($options['controller'] . "/" . $options['action']))
		{
			// We might need to add some extra code to check that javascript
			// is working/enabled
			if(isset($cauth) && $cauth->checkJs == 'onLogin')
			{
				// Add some code to check for javascript
				$jsCheckCode = "<input type='hidden' class='hidden' id='jsCheck' name='data[jsCheck]' value='0' />" .
				"<script type='text/javascript'>" .
					"inp = document.getElementById('jsCheck');" .
					"inp.value = '1';" .
				"</script>";
			}
		}

		return $tag . $jsCheckCode . $this->Javascript->includeScript("validate");
	}
	
	
	
	/**
	 * Adds "class='formButton'" to all submit buttons that don't have their class specified
	 *
	 * @param string $caption 
	 * @param string $options 
	 * @return void
	 * @author David Roberts
	 */
	
	
	function submit($caption = null, $options = array()) {
		if(!isset($options['class'])) $options['class'] = "formButton";
		return parent::submit($caption, $options);
	}



	/**
	 * Same as FormHelper::text(), but supports $options['focus'] (see _genFocusCode()) and
	 * language-dependent fields
	 *
	 * @param string $fieldName 
	 * @param string $options 
	 * @return void
	 * @author David Roberts
	 */
	
	function text($fieldName, $options = array(), $isLDF = null)
	{	
		$this->setEntity($fieldName);

		if(isset($options['defaultValue']) && !isset($this->data[$this->model()][$this->field()]))
		{
			$options['value'] = $options['defaultValue'];
		}
			
		$code = parent::text($fieldName, $options);
		$js = $this->_genFocusCode(Inflector::camelize($this->model()) . Inflector::camelize($this->field()), $options);
		
		return $code . $js;
	}
	
	/**
	 * Same as FormHelper::textarea(), but supports $options['focus'] (see _genFocusCode()) and
	 * language-dependent fields
	 *
	 * @param string $fieldName 
	 * @param string $options 
	 * @return void
	 * @author David Roberts
	 */
	
	function textarea($fieldName, $options = array(), $isLDF = null)
	{	
		$this->setEntity($fieldName);
		
		$code = parent::textarea($fieldName, $options);
		$js = $this->_genFocusCode(Inflector::camelize($this->model()) . Inflector::camelize($this->field()), $options);
		return $code . $js;
	}
	
	
	/**
	 * Same as FormHelper::password(), except supports $options['focus'] (see _genFocusCode())
	 *
	 * @param string $fieldName 
	 * @param string $options 
	 * @return void
	 * @author David Roberts
	 */
	
	function password($fieldName, $options = array())
	{	
		$code = parent::password($fieldName, $options);
		$js = $this->_genFocusCode(Inflector::camelize($this->model()) . Inflector::camelize($this->field()), $options);
		return $code . $js;
	}
	
	function includeCalendar()
	{
		return $this->Javascript->link('calendar_stripped.js') . $this->Javascript->link('calendar-en.js') . $this->Javascript->link('calendar-setup.js');
	}
	
	
	function calendarText($fieldName, $options = array())
	{
		if(!isset($options['class'])) $options['class'] = 'calendarText';
		if(!isset($options['maxlength'])) $options['maxlength'] = 8;
		
		$input = $this->text($fieldName, $options);
		
		$id = /*$this->model() .*/ $this->domId();
		
		$code  = "<script type='text/javascript'>";
		$code .=   "Calendar.setup(";
		$code .=     "{";
		$code .=       "inputField  : '$id', ";
		$code .=       "ifFormat    : '%d/%m/%y'";
		$code .=     "}";
		$code .=   ");";
		$code .= "</script>";
		
		return $input . $code;
	}

	
	/**
	 * Generates Javascript code to set the focus to an element
	 *
	 * @param string $id The name of the field, e.g. Accounts.first_name
	 * @return void
	 * @author David Roberts
	 */
	
	function setFocus($id)
	{
		$parts = explode(".", $id);
		return $this->_genFocusCode(Inflector::camelize($parts[0]) . Inflector::camelize($parts[1]), array('focus' => true));
	}

	/**
	 * Takes the ID for a form element, and if $options['focus'] is set, generates
	 * code to set the focus to that element when the page is loaded
	 *
	 * @param string $id The ID of the form element
	 * @param string $options Will only generate the code if $options['focus'] is set
	 * @return void
	 * @author David Roberts
	 */
	
	function _genFocusCode($id, $options)
	{
		// Don't generate any code if $options['focus] isn't set
		if(!isset($options['focus']) || $options['focus'] == false)
		{
			return;
		}
	
		return "<script type='text/javascript'>document.getElementById('$id').focus();</script>";
	}
	
	/**
	 * Formats an error message for display on a page - if there are multiple errors in an array,
	 * will output them line by line, with the appropriate language (if we're talking about an LDF field)
	 *
	 * @param string $error 
	 * @return void
	 * @author David Roberts
	 */
	
	function _formatError($error)
	{
		if(!is_array($error)) return $error;
		
		$lang = $this->params['lang'];
		$allLangs = $lang->getAll();

		$output = "";
		
		foreach($error as $key => $msg) 
		{
			if(!is_numeric($key)) $output .= "$msg<br />";
		}

		// Display any language-related errors with their language
		foreach($allLangs as $lang) 
		{
			if(isset($error[$lang['id']]))
			{
				if(count($allLangs) > 1) $output .= $this->Html->link($lang['code'], '#', array('onclick' => "LDF.changeFieldLangs('".$lang['id']."');return false;")) . ": ";
				$output .= $error[$lang['id']] . "<br />";
			}
		}

		return $output;
	}
	

	/**
	 * Same as the equivalent form helper function, but always creates an error div. If there is no
	 * error message, the div is hidden. Adds an observer that will make AJAX calls to the 
	 * validateField action (usually defined in AppController) in order to validate the associated
	 * field and update the error message accordingly.
	 *
	 * @param string $field A field name, like "Modelname.fieldname"
	 * @param string|array $text Any default error messages (they are usually defined in the model though)
	 * @param array $options Rendering options for <div /> wrapper tag
	 * @param string $check How to perform the AJAX calls. Options are 'false' (never), 'onblur' (default) or an integer specifiyng the interval between calls
	 * @return string The code for the div to display errors for the specified field
	 */

	function error($field, $text = null, $options = array(), $check = 'onblur', $isLDF = null) 
	{
		$this->setEntity($field);
		$options = am(array('wrap' => true, 'class' => 'error-message', 'escape' => true), $options);

		if ($error = $this->tagIsInvalid()) 
		{
			if (is_array($text) && is_numeric($error) && $error > 0) $error--;

			if (is_array($text) && isset($text[$error])) 
			{
				$text = $text[$error];
			} elseif (is_array($text)) {
				$text = null;
			}

			if ($text != null) 
			{
				$error = $text;
			} elseif (is_numeric($error)) {
				$error = 'Error in field ' . Inflector::humanize($this->field());
			}
			if ($options['escape']) 
			{
				$error = h($error);
			}	
		}
	
		// Put the error messages into javascript, if there are any
		$js = '';
		if($text)
		{
			if(is_array($text))
			{
				foreach ($text as $key => $value) {
					$js .= "Validate.addField('".$this->model()."', '".$this->field()."', '$key', '$value');\n";
				}
			} else {
				$js .= "Validate.addField('".$this->model()."', '".$this->field()."', '', '$text');\n";
			}
		}
		
		// Wrap the generated js code in <script> tags
		if($js) $js = $this->Javascript->codeBlock($js);
	
		$id = "error" . $this->domId($field);
		$divOptions = array('id' => $id);
		if(!$error) $divOptions['style'] = 'display:none';
	
		$div = $this->Html->div($options['class'], $this->_formatError($error), $divOptions);
	
		return $js . $div . $this->validationObserver($field, $check, $id, $isLDF);
	
	}
	
	
	function areErrors($model)
	{
		return isset($this->validationErrors) && isset($this->validationErrors[$model]);
	}
	
	
	function validationObserver($field, $check, $errorDivId, $isLDF = null)
	{
		$this->setEntity($field);
		
		// Just a normal field
		return $this->_validationObserverCode($this->domId($field), $this->model(), $this->field(), $errorDivId, $check);
	}
	
	function _validationObserverCode($domId, $model, $field, $errorDivId, $check)
	{
		$id = $errorDivId;
		
		if($check == false || low($check) == 'false')
		{
			// Never validate the field
			return '';
		} elseif(low($check) == 'onblur') {
			// Validate the field when it loses focus
			return $this->Javascript->codeBlock("new Event.observe('".$domId."', 'blur', function(){".$this->Ajax->remoteFunction(array('url' => 'validateField/'.$model.'/'.$field, 'frequency' => 1, 'with' => 'Form.serialize($("'.$domId.'").form)', 'success' => 'Validate.processError(request, "'.$model.'", "'.$field.'", "'.$id.'")'))."})");
		} else {
			// Validate the field at regular intervals, providing it has changed
			return $this->Ajax->observeField($domId, array('url' => 'validateField/'.$model.'/'.$field, 'frequency' => $check, 'with' => 'Form.serialize($("'.$domId.'").form)', 'success' => 'Validate.processError(request, "'.$model.'", "'.$field.'", "'.$id.'")'));
		}
	}
}

?>