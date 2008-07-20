<?php 
	if(!is_array($error))
	{
		echo $error;
	} else { 
		$allLangs = $language->getAll();

		foreach($error as $key => $msg) 
		{
			if(!is_numeric($key)) echo "$msg<br />";
		}

		// Display any language-related errors with their language
		foreach($allLangs as $lang) 
		{
			if(isset($error[$lang['id']]))
			{
				if(count($allLangs) > 1) echo $html->link($lang['code'], '#', array('onclick' => 		"LDF.changeFieldLangs('".$lang['id']."');return false;")) . ": ";
				echo $error[$lang['id']] . "<br />";
			}
		}
	}
	?>