<?php

class TeamMember extends AppModel 
{
	var $name = 'TeamMember';
	
	// This var tells the system what it should look for when making lists of all the records, etc
	// used in AppModel::saveField()
	var $mainField = 'unique_name';
	
	var $belongsTo = array('User' => array('className' => 'User', 'foreignKey' => 'user_id'),
						   'Role' => array('className' => 'Role', 'foreignKey' => 'role_id'));
	
	// This array defines any actions that we might want to perform on the data before we validate/save
	// it. For an explanation of the keys allowed, see AppModel::sanitize()
	var $sanitize = array(
						'phone' => array('trim', 'escape', 'striphtml', 'stripextraws'),
						'email' => array('trim')
					);
					
				/**
				 * Sets the values of the $validate array, then calls AppModel::beforeValidate().
				 * 
				 * It's cumbersome, but we have to define the $validate array here, as
				 * we can't use functions like ) (internationalisation) outside a function
				 *
				 * @return void
				 * @author David Roberts
				 */

	function beforeValidate()
	{

		$this->validate = array(
							'project_id' => array(
								'required' => array('rule' => VALID_NOT_EMPTY, 'message' => "The project to add the user to was not specified")
							),
							'user_id' => array(
								'required' => array('rule' => VALID_NOT_EMPTY, 'message' => "You must choose the person that you want to add to the project")
							),
							'role_id' => array(
								'required' => array('rule' => VALID_NOT_EMPTY, 'message' => "You must choose the role that the person plays in the project")
							)
						);

		return parent::beforeValidate();
	}
	
	
	/**
	 * Returns all the team members associated with a project
	 *
	 * @param string $projectId 
	 * @param string $conditions 
	 * @return void
	 * @author David Roberts
	 */
	
	function findAllFromProj($projectId, $conditions = null)
	{
		if(!isset($conditions)) $conditions = 1;
		$conditions = "(" . $conditions . ") AND project_id = '$projectId'";
		
		return $this->findAll($conditions, null, "status DESC, role_id ASC");
	}
	
	/* Same as generateTeamUserList but adds 'no-one' on the end */
	function generateAssignedUserList($pid, $col = 'unique_name', $orderBy = 'status DESC, role_id ASC', $conditions = null)
	{
		$users = $this->generateTeamUserList($pid, $col, $orderBy, "project_id = '$pid'");
		$users[0] = "no-one";
		
		return $users;
	}
	
	/* Returns a list of all the team members with their team member ids as the keys */
	function generateTeamUserList($pid, $col = 'unique_name', $orderBy = 'status DESC, role_id ASC', $conditions = null)
	{
		return parent::generateNameList($col, $orderBy, "project_id = '$pid'");
	}
	
	function generateNameList($pid, $col = 'unique_name', $orderBy = 'status DESC, role_id ASC', $conditions = null)
	{
		if(!isset($conditions)) $conditions = "1";
		$conditions = "($conditions) AND project_id = '$pid'";
		$data = $this->query("SELECT `User`.`id`, `TeamMember`.`$col` FROM `users` as `User` INNER JOIN `team_members` AS `TeamMember` ON `TeamMember`.`user_id` = `User`.`id` WHERE $conditions ORDER BY $orderBy");
		
		$list = array();
		if($data) $list = array_combine(Set::extract($data, "{n}.User.id"), Set::extract($data, "{n}.TeamMember.".$col));
		if($col == 'unique_name') $list[0] = 'everyone';
		
		return $list;
	}
	
	function generateCurrentUserList($pid, $col = 'unique_name', $orderBy = 'status DESC, role_id ASC', $conditions = null)
	{
		if(!isset($conditions)) $conditions = "1";
		$conditions = "($conditions) AND status = '1' AND User.is_active = 1";
		
		return $this->generateNameList($pid, $col, $orderBy, $conditions);
	}
	
	
	
	// Works out the shortest unique name to use for each person in a project
	function updateShortNames($pid, $controller)
	{
		$data = $this->query("SELECT `TeamMember`.`id`, `User`.`first_name`, `User`.`last_name` FROM `users` as `User` INNER JOIN `team_members` AS `TeamMember` ON `TeamMember`.`user_id` = `User`.`id` WHERE `TeamMember`.`project_id` = '$pid'");
		
		// Make the arrays
		for($i = 0; $i < count($data); $i++) 
		{ 
			$index = $data[$i]['TeamMember']['id'];
			$users[$index] = $controller->formatter()->name($data[$i]['User']['first_name'], $data[$i]['User']['last_name']);
			$first[$index] = $data[$i]['User']['first_name'];
		}
		
		asort($users);
		
		if(count($users) > 1)
		{
			$prev = "";
			$cur = current($users);
			$curKey = key($users);
			
			while($cur)
			{
				$next = next($users);
				
				// Do your thing
				$d0 = $this->matchStart($prev, $cur);
				$d1 = $this->matchStart($cur, $next);
				$s = substr($cur, 0, max($d0, $d1)+1);
				
				if(strlen($first[$curKey]) > strlen($s)) 
				{
					$short[$curKey] = $first[$curKey];
				} else {
					$short[$curKey] = $s;
				}
				
				$prev = $cur;
				$cur = $next;
				$curKey = key($users);
			}
		} else {
			$short = $first;
		}
		
		foreach($short as $id => $value) 
		{
			$this->id = $id;
			$this->saveField('unique_name', $value);
		}
		
	}
	
	// Returns the number of chars from the beginning of the strings when they start to diverge
	function matchStart($a, $b)
	{
		for($i = 0; $i < strlen($a); $i++) 
		{ 
			if($i >= strlen($b)) return $i;
			if($a[$i] != $b[$i]) return $i;
		}
		
		return strlen($a);
	}
	
	

}
?>