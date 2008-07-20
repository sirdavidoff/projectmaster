<?php

class Note extends AppModel 
{
	var $name = 'Note';
	
	var $belongsTo = array('Contact' => array('className' => 'Contact', 'foreignKey' => 'contact_id')
						   /*'Creator' => array('className' => 'User', 'foreignKey' => 'created_by'),
						   'Updater' => array('className' => 'User', 'foreignKey' => 'updated_by')*/);
	
	function beforeValidate()
	{
		
		$this->validate = array(
							'text' => array(
								'required' => array('rule' => VALID_NOT_EMPTY, 'message' => "You must enter the text of your note")
							)
						);
		
		return parent::beforeValidate();
	}

}
?>