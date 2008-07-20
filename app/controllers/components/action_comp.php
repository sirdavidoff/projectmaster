<?php
class ActionCompComponent extends Object
{
	
	function startup(&$controller) 
	{	
		// Make the controller available in other methods
		$this->controller =& $controller;
	}
	
	/**
	 * Takes actions without dates off the front of the passed array and returns them
	 *
	 * @param string $actions 
	 * @return void
	 * @author David Roberts
	 */
	
	function stripDatelessActions(&$actions)
	{
		$datelessActions = Array();
		
		foreach($actions as $action) 
		{
			if(!isset($action['deadline_date']))
			{
				$datelessActions[] = array_shift($actions);
			} else {
				return $datelessActions;
			}
		}
		
		return $datelessActions;
	}
}
?>