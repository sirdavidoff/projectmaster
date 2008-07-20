<?php

class Action extends AppModel 
{
	var $name = 'Action';
	
	var $belongsTo = array('User' => array('className' => 'User', 'foreignKey' => 'user_id'),
						   'Contact' => array('className' => 'Contact', 'foreignKey' => 'contact_id'));
	
	var $sanitize = array(
						'deadline_time' => array(array('action' => 'stripdefaultvalue', 'default' => 'hh:mm')),
						'deadline_date_readable' => array(array('action' => 'stripdefaultvalue', 'default' => 'dd/mm/yy'))
					);
	
	function beforeValidate()
	{		
		$this->validate = array(
							'text' => array(
								'required' => array('rule' => VALID_NOT_EMPTY, 'message' => "You must enter a description of the action")
							),
							'deadline_date_readable' => array(
								'isSlashDate' => array('rule' => 'isSlashDate', 'message' => "If you enter a date, it must be in the format dd/mm/yy"),
							),
							'deadline_time' => array(
								'isTime' => array('rule' => 'isTime', 'message' => "If you enter a time, it must be in the format hh:mm"),
							)
						);
		
		$this->data[$this->name]['deadline_date'] = $this->setDate($this->data[$this->name]['deadline_date_readable']);
						
		return parent::beforeValidate();	// If we don't return true, validation won't be performed
	}
	
	
	/**
	 * Converts dd/mm/yy to a Y-m-d for entry in the DB
	 *
	 * @return void
	 * @author David Roberts
	 */
	
	function setDate($readableDate)
	{
		$date = explode("/", $readableDate);
		if(count($date) == 3 && is_numeric($date[0]) && is_numeric($date[1]) && is_numeric($date[2]) && checkDate($date[1],$date[0],$date[2]))
		{
			return strftime("%Y-%m-%d",mktime(0,0,0,$date[1],$date[0],$date[2]));
		}
	}

}
?>