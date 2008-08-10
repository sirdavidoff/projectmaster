<?php
//uses('view/helpers/text');

///uses('view/helpers/time');
class CustomjsHelper extends Helper {

	var $helpers = array('Html', 'Forms', 'Javascript');


	function dynamicOptionList($containerId, $list, $options = array())
	{
		// See if there are any pre-existing rows that we need to create
		$rows = array();
		if(isset($options['rows'])) {
			$rows = $options['rows'];
			unset($options['rows']);
		}
		
		// Generate the code for new rows, if it's not already specified
		if(!isset($options['innerCode'])) {
			$options['innerCode'] = $this->_dynamicOptionRowCode($options['innerId'], $list);
		}
		
		// Create the object
		$output = "";
		$output .= "var sortable = new Ajax.DynamicSortable('$containerId', ";
		$output .= $this->Javascript->object($options);
		$output .= ");";
		
		$output .= "\n";
		
		// Generate any pre-existing rows
		if(count($rows) > 0) 
		{
			foreach($rows as $value)
			{
				$output .= "\n";
				$output .= "sortable.addRow('" . $this->Javascript->escapeString($this->_dynamicOptionRowCode($options['innerId'], $list, array('selected' => $value))) . "')";
			}
		}
		
		return $this->Javascript->codeBlock($output);
	}
	
	
	function _dynamicOptionRowCode($name, $selectOptions, $options = array())
	{
		$selected = null;
		if(isset($options['selected'])) $selected = $options['selected'];
		
		$output  = "<div class='fieldButtons'>";
		$output .= " " . $this->_handleCode();
		$output .= " " . $this->_removeLinkCode();
		$output .= "</div>";
		
		$output .=  str_replace("\n", "", $this->Forms->select($name, $selectOptions, $selected, null, null));
		
		// Add the extra [] needed for multiple-value fields
		$output = str_replace("[$name]", "[$name][]", $output);
		
		return $output;
	}
	
	function _handleCode($class = 'handle')
	{
		return '<span class="handle">drag</span>';
	}
	
	function _removeLinkCode()
	{
		//return '<a href="#" onclick="this.parentNode.style.display = \'none\';return false">remove</a>';
		return '<a href="#" onclick="this.parentNode.parentNode.parentNode.removeChild(this.parentNode.parentNode);return false">remove</a>';
	}
	
	function dynamicOptionListAddLink($content = 'Add row')
	{
		return '<a href="#" onclick="sortable.addRow();return false">'.$content.'</a>';
	}
}

?>