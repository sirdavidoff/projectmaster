<?php

class Contract extends AppModel 
{
	var $name = 'Contract';
	
	var $belongsTo = array('Contact' => array('className' => 'Contact', 'foreignKey' => 'contact_id'));
	
	var $sanitize = array(
						'time' => array(array('action' => 'stripdefaultvalue', 'default' => 'hh:mm'))
					);
	
	function beforeValidate()
	{		
		$this->validate = array(
							'space' => array(
								'required' => array('rule' => VALID_NOT_EMPTY, 'message' => "You must enter the space that the contract is for"),
							),
							'cost' => array(
								'number' => array('rule' => VALID_NUMBER, 'message' => "The cost must be a number"),
								'required' => array('rule' => VALID_NOT_EMPTY, 'message' => "You must enter the cost of the contract in euros (€)"),
							),
							'signed_on' => array(
								'required' => array('rule' => VALID_NOT_EMPTY, 'message' => "You must enter the date the contract was signed in the format dd/mm/yy"),
							),
							'payment_by_readable' => array(
								'required' => array('rule' => 'isReadableDateOrNull', 'message' => "You must enter the date the contract must be paid in the format dd/mm/yy or leave it blank"),
							)
						);
		
		$this->data[$this->name]['signed_on'] = $this->setDate($this->data[$this->name]['signed_on_readable']);
		$this->data[$this->name]['payment_by'] = $this->setDate($this->data[$this->name]['payment_by_readable']);
						
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
	
	function isReadableDateOrNull($value, &$params = array())
	{
		if(is_array($value)) $value = array_pop($value);
		return $value == null || $this->setDate($value) != null;
	}
	
	function defaultValues()
	{
		$contract['signed_on_readable'] = date('d/m/y');
		$contract['payment_by_readable'] = date('d/m/y', mktime(0, 0, 0, date('m'), date('d')+30, date('Y')));
		
		return $contract;
	}

}
?>