<?php
uses('view/helpers/text');

class RenderingHelper extends TextHelper {

	var $helpers = array('Html', 'Javascript', 'Ajax', 'Time');
	
	function contactHasPhone($c)
	{
		if(isset($c['Contact']['tel']) && $c['Contact']['tel'] != '') return true; 
		
		if(isset($c['Person'])) 
		{
			foreach($c['Person'] as $p) 
			{
				if(isset($p['tel']) && $p['tel'] != '') return true; 
				if(isset($p['mobile']) && $p['mobile'] != '') return true; 
			}
		}
		
		return false;
	}
	
}

?>