<?php

class Sector extends AppModel 
{
	var $name = 'Sector';
	
	function generateNameList($col = 'name', $orderBy = 'id ASC', $conditions = null)
	{
		$result = parent::generateNameList($col, $orderBy, $conditions);
		
		// If there is a value with a key of 0, it is the default so put
		// it at the end instead of the beginning
		if($orderBy == 'id ASC' && isset($result[0]))
		{
			$default = $result[0];
			unset($result[0]);
			$result[0] = $default;
		}
		
		return $result;
	}

}
?>