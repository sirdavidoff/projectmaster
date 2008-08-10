<?php
/**
 * Manages information related to user accounts.
 *
 * @package ppc
 * @subpackage ppc.models
 * @author David Roberts
 */

/**
 * Manages information related to user accounts.
 *
 * @package ppc
 * @subpackage ppc.models
 * @author David Roberts
 */

class Contact extends AppModel {

	var $name = 'Contact';
											
	var $belongsTo = array('Market' => array('className' => 'Market', 'foreignKey' => 'market_id'),
						'Contacttype' => array('className' => 'Contacttype', 'foreignKey' => 'contacttype_id'),
						'Sector' => array('className' => 'Sector', 'foreignKey' => 'sector_id'),
						'Status' => array('className' => 'Status', 'foreignKey' => 'status_id'),
						'Creator' => array('className' => 'User', 'foreignKey' => 'created_by'),
						'Updater' => array('className' => 'User', 'foreignKey' => 'updated_by'));
						
	var $hasMany = array('Contract' => array('className' => 'Contract', 
										 'order' => 'signed_on DESC',
										 'dependent' => true,
										 'foreignKey' => 'contact_id'),
						 'Meeting' => array('className' => 'Meeting', 
										 'order' => 'date ASC, time ASC',
										 'dependent' => true,
										 'foreignKey' => 'contact_id'),
						 'Action' => array('className' => 'Action', 
										 'order' => 'deadline_date ASC, deadline_time ASC',
										 /*'conditions' => 'Action.completed = 0',*/
										 'dependent' => true,
										 'foreignKey' => 'contact_id'),
						 'Note' => array('className' => 'Note', 
										 'order' => 'Note.ordering DESC',
										 'dependent' => true,
										 'foreignKey' => 'contact_id'),
						 'Person' => array('className' => 'Person', 
										 'order' => 'Person.ordering ASC',
										 'dependent' => true,
										 'foreignKey' => 'contact_id'),
						 'ContactStatusChange' => array('className' => 'ContactStatusChange',    
										 'order' => 'ContactStatusChange.changed_at DESC',
										 'dependent' => true,
										 'foreignKey' => 'contact_id'));
										
	var $hasAndBelongsToMany = array('Opener'=> array('classname'=>'Contact', 
													  'joinTable'=>'contact_opens_contact',
													  'foreignKey'=>'openee_id',
													  'order' => 'Opener.name',
													  'associationForeignKey'=>'opener_id'),
									 'Openee'=> array('classname'=>'Contact', 
													  'joinTable'=>'contact_opens_contact',
													  'foreignKey'=>'opener_id',
													  'order' => 'Openee.name',
													  'associationForeignKey'=>'openee_id'));

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
							'contacttype_id' => array(
								'required' => array('rule' => VALID_NOT_EMPTY, 'message' => "You must select the type of contact")
							),
							'name' => array(
								'unique' => array('rule' => 'isUniqueFieldInProject', 'field' => 'name', 'message' => "There is already a contact with this name"),
								'required' => array('rule' => VALID_NOT_EMPTY, 'message' => "You must enter the name of the contact")
							),
							'sector_id' => array(
								'required' => array('rule' => VALID_NOT_EMPTY, 'message' => "You must select the sector of this contact")
							),
							'market_id' => array(
								'required' => array('rule' => VALID_NOT_EMPTY, 'message' => "You must select the market of this contact")
							),
							'status_id' => array(
								'required' => array('rule' => VALID_NOT_EMPTY, 'message' => "You must select the status of this contact")
							)
						);
		
		return parent::beforeValidate();	// If we don't return true, validation won't be performed
	}
	
	
	
	function isUniqueFieldInProject($value, &$params = array())
	{
		if(!$params['field']) trigger_error("No field specified for AppModel::isUniqueField() call", E_USER_WARNING);
		$field = $params['field'];
		if(is_array($value)) $value = array_pop($value);
		
		if(isset($this->data[$this->name]))
			$data = $this->data[$this->name];
		else	
			$data = $this->data;

		// If the record is already in the DB and the value is the same as the DB one, don't complain
		if(isset($data['id']))
		{
			$result = $this->find($data['id'], array($field));
			if($result[$this->name][$field] == $value) {
				return true;
			}
		}
		
		$results = $this->findAll(array($params['field'] => $value, 'project_id' => $data['project_id']), array('id'), null, null, null, -1);
		
		if(!$results) return true;
		
		$ids = Set::extract($results, '{n}.'.$this->name.'.id');
		return $ids && count($ids) == 1 && $ids[0] == $this->id;
	}

}
?>