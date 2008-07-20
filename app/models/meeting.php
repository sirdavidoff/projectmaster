<?php

class Meeting extends AppModel 
{
	var $name = 'Meeting';
	
	var $belongsTo = array('Contact' => array('className' => 'Contact', 'foreignKey' => 'contact_id'));
	
	var $sanitize = array(
						'time' => array(array('action' => 'stripdefaultvalue', 'default' => 'hh:mm'))
					);
	
	function beforeValidate()
	{		
		$this->validate = array(
							'date' => array(
								'required' => array('rule' => VALID_NOT_EMPTY, 'message' => "You must enter the date of the meeting in the format dd/mm/yy"),
							),
							'time' => array(
								'isTime' => array('rule' => 'isTime', 'message' => "The time for the meeting must be in the format hh:mm"),
								'required' => array('rule' => VALID_NOT_EMPTY, 'message' => "You must enter a time for the meeting in the format hh:mm")
							)
						);
		
		$this->data[$this->name]['date'] = $this->setDate($this->data[$this->name]['date_readable']);
						
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