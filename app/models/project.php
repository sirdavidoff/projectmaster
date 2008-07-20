<?php

class Project extends AppModel {

	var $name = 'Project';
											
	var $belongsTo = array('Media' => array('className' => 'Media', 'foreignKey' => 'media_id'),
						'ProjectStatus' => array('className' => 'ProjectStatus', 'foreignKey' => 'project_status_id'));
						
	var $hasMany = array('Contact' => array('className' => 'Contact', 
										 'dependent' => true,
										 'foreignKey' => 'project_id'),
						 'TeamMember' => array('className' => 'TeamMember', 
										 'dependent' => true,
										 'foreignKey' => 'project_id'));
										
	/*var $hasAndBelongsToMany = array('User'=> array('classname'=>'User', 
													  'joinTable'=>'contact_opens_contact',
													  'foreignKey'=>'openee_id',
													  'order' => 'Opener.name',
													  'associationForeignKey'=>'opener_id'));*/

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
							'media_id' => array(
								'required' => array('rule' => VALID_NOT_EMPTY, 'message' => _t("You must select the media of the project", true))
							),
							'subject' => array(
								'required' => array('rule' => VALID_NOT_EMPTY, 'message' => _t("You must enter the subject of the project", true))
							),
							'started_on_readable' => array(
								'required' => array('rule' => VALID_NOT_EMPTY, 'message' => _t("You must enter the date the project starts on in the format dd/mm/yy", true)),
								'isSlashDate' => array('rule' => 'isSlashDate', 'message' => "The date must be in the format dd/mm/yy")
							)
						);
						
		$this->data[$this->name]['started_on'] = $this->setDate($this->data[$this->name]['started_on_readable']);
		
		return parent::beforeValidate();	// If we don't return true, validation won't be performed
	}


	function generateProjectNameList($orderBy = 'id ASC', $conditions = null)
	{
		$this->recursive = 0;
		$data = $this->findAll($conditions, array('Project.id', 'Project.subject', 'Project.started_on', 'Media.name'), $orderBy, 0);
		if($data) {
			foreach($data as $p) 
			{
				$result[$p['Project']['id']] = $this->controller->formatter()->projectName($p);
			}
			return $result;
		} else {
			return array();
		}
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