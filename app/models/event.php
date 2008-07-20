<?php

class Event extends AppModel 
{
	var $name = 'Event';

	var $sanitize = array(
						'start_time' => array(array('action' => 'stripdefaultvalue', 'default' => 'hh:mm')),
						'end_time' => array(array('action' => 'stripdefaultvalue', 'default' => 'hh:mm'))
					);
	
	function beforeValidate()
	{		
		$this->validate = array(
							'start_date' => array(
								'required' => array('rule' => VALID_NOT_EMPTY, 'message' => "You must enter the date the event starts on in the format dd/mm/yy"),
							),
							'start_time' => array(
								'isTime' => array('rule' => 'isTime', 'message' => "The start time for the event must be in the format hh:mm")
							),
							'end_time' => array(
								'isTime' => array('rule' => 'isTime', 'message' => "The end time for the event must be in the format hh:mm")
							)
						);
		
		$this->data[$this->name]['start_date'] = $this->setDate($this->data[$this->name]['start_date_readable']);
		$this->data[$this->name]['end_date'] = $this->setDate($this->data[$this->name]['end_date_readable']);
		
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