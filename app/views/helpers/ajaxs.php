<?php

uses('view/helpers/ajax');
class AjaxsHelper extends AjaxHelper {

	var $editorOptions = array('okText', 'okButton', 'cancelText', 'cancelLink', 'savingText', 'formId', 'externalControl', 'rows', 'cols', 'size', 'highlightcolor', 'highlightendcolor', 'savingClassName', 'formClassName', 'submitOnBlur', 'loadTextURL', 'loadingText', 'callback', 'ajaxOptions', 'clickToEditText', 'collection', 'value', 'emptyText', 'calendar', 'editValue');

	/**
	 * Adds "class='formButton'" to all submit buttons that don't have their class specified
	 *
	 * @param string $title 
	 * @param string $options 
	 * @return void
	 * @author David Roberts
	 */

	function submit($title = 'Submit', $options = array()) {
		if(!isset($options['class'])) $options['class'] = "formButton";
		return parent::submit($title, $options);
	}



	function editable($id, $value, $field, $model, $controller, $container = 'div', $options = array())
	{
		$output = '';
		
		$class = 'inlineEdit';
		if(isset($options['class'])){
			$class .= ' ' . $options['class'];
		}
		
		$style = '';
		if(isset($options['style'])){
			$style = $options['style'];
		}
		
		$output .= "<$container id='".$field."_".$id."' class='$class' style='$style'>" . $value . "</$container>";
		$url = '/'.$controller.'/saveField/'.$model.'/'.$field.'/'.$id;
		
		if(isset($options['collection'])) $url .= "/1";
		
		$output .= $this->cleanEditor($field.'_'.$id, $url, $options);
		
		return $output;
	}
	



	function cleanEditor($id, $url, $options = array())
	{
		$options = array_merge($options, array('okButton' => 'false', 'okText' => 'OK', 'value' => 0, 'cancelLink' => 'false', 'submitOnBlur' => 'true', 'highlightcolor' => 'none'));
		if(isset($options['collection'])) $options['okButton'] = true;
		return $this->editor($id, $url, $options);
	}
	
	
	
	
	function editor($id, $url, $options = array()) {
		$url = $this->url($url);
		$options['ajaxOptions'] = $this->__optionsForAjax($options);

		foreach ($this->ajaxOptions as $opt) {
			if (isset($options[$opt])) {
				unset($options[$opt]);
			}
		}

		if (isset($options['callback'])) {
			$options['callback'] = 'function(form, value) {' . $options['callback'] . '}';
		}

		$type = 'InPlaceEditor';
		if (isset($options['collection']) && is_array($options['collection'])) {
			//$options['collection'] = $this->Javascript->object($options['collection']);
			$options['collection'] = $this->jsArray($options['collection']);
			$type = 'InPlaceCollectionEditor';
		}

		$var = '';
		if (isset($options['var'])) {
			$var = 'var ' . $options['var'] . ' = ';
			unset($options['var']);
		}

		$options = $this->_optionsToString($options, array('okText', 'cancelText', 'savingText', 'formId', 'externalControl', 'highlightcolor', 'highlightendcolor', 'savingClassName', 'formClassName', 'loadTextURL', 'loadingText', 'clickToEditText'));
		$options = $this->_buildOptions($options, $this->editorOptions);
		return $this->Javascript->codeBlock("{$var}new Ajax.{$type}('{$id}', '{$url}', {$options});");
	}
	
	
	function jsArray($array)
	{
		$string = '';
		if(count($array) > 0)
		{
			foreach($array as $key => $value) 
			{
				$string .= "[$key,'$value'],";
				//$string .= "'$value',";
			}
			
			// Get rid of the last comma
			$string = substr($string, 0, -1);
		}
		
		return "[" . $string . "]";
	}
	
	/**
	 * The options array can contain the following parameters
	 * viewUrl
	 * addUrl (these three URLs must all end in / so that the id of the object can be added on the end of them)
	 * removeUrl
	 * addList (an associative array of options to be presented in the add dropdown in the format id => name)
	 * update (the id of the element to be updated after an Ajax call - if null, will use the generated container)
	 * emptyText (string to display if the list is empty)
	 *
	 * @param string $list 
	 * @param string $options 
	 * @return void
	 * @author David Roberts
	 */
	
	function editableList($list, $options = array())
	{	
		$id = 'editlist' . rand();
		
		$output = '';
		$output .= "<div id='$id' class='list'>";
		
		if(isset($list) && count($list) > 0) 
		{
			foreach($list as $itemId => $itemValue) 
			{
				$output .= "<span id='".$id."_".$itemId."' class='listItem'>";
				if(isset($options['viewUrl']))
				{
					$output .= $this->Html->link($itemValue, $options['viewUrl'] . $itemId);
				} else {
					$output .= $itemValue;
				}
				$output .= "</span>";
			}
		} else {
			if(isset($options['emptyText']))
			{
				//$output .= "<div id='empty$id' class='listItem'>" . $options['emptyText'] . "</div>";
			}
		}
		
		$output .= "</div>";
		$output .= "<div style='clear:both'></div>";
		
		if(!isset($options['update'])) $options['update'] = $id;
		if(!isset($options['unique'])) $options['unique'] = true;
		if(isset($options['addList'])) $options['collection'] = $options['addList'];
		
		$output .= $this->Javascript->codeBlock("new Ajax.InPlaceListEditor('$id', '".$options['removeUrl']."', ".$this->Javascript->object($options).");");

		return $output;
	}
	
}

?>