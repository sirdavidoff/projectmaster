<?php
/**
 * Manages information related to people.
 *
 * @package ppc
 * @subpackage ppc.models
 * @author David Roberts
 */

/**
 * Manages information related to people.
 *
 * @package ppc
 * @subpackage ppc.models
 * @author David Roberts
 */

class Person extends AppModel 
{
	var $name = 'Person';
	var $belongsTo = array('Contact' => array('className' => 'Contact',
										   'dependent' => false,
										   'foreignKey' => 'contact_id'));
										
	// This array defines any actions that we might want to perform on the data before we validate/save
	// it. For an explanation of the keys allowed, see AppModel::sanitize()
	/*var $sanitize = array(
						'first_name' => array('trim', 'escape', 'striphtml', 'stripextraws'),
						'last_name' => array('trim', 'escape', 'striphtml', 'stripextraws'),
						'email' => array('trim')
					);*/
	
	/**
	 * Sets the values of the $validate array, then calls AppModel::beforeValidate().
	 * 
	 * It's cumbersome, but we have to define the $validate array here, as
	 * we can't use functions like _t() (internationalisation) outside a function
	 *
	 * @return void
	 * @author David Roberts
	 */
	
	function beforeValidate()
	{
		
		$this->validate = array(
							'position' => array(
								'required' => array('rule' => VALID_NOT_EMPTY, 'message' => "You must enter the position of the person")
							)
						);
		
		return parent::beforeValidate();
	}
}
?>