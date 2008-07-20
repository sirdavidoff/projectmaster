<?php

class User extends AppModel 
{
	var $name = 'User';
	
	var $hasMany = array('TeamMember' => array('className' => 'TeamMember', 'foreignKey' => 'user_id'));
	
	// This array defines any actions that we might want to perform on the data before we validate/save
	// it. For an explanation of the keys allowed, see AppModel::sanitize()
	var $sanitize = array(
						'first_name' => array('trim', 'escape', 'striphtml', 'stripextraws'),
						'last_name' => array('trim', 'escape', 'striphtml', 'stripextraws'),
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
							'first_name' => array(
								'required' => array('rule' => VALID_NOT_EMPTY, 'message' => "You must enter the person's first name")
							),
							'last_name' => array(
								'required' => array('rule' => VALID_NOT_EMPTY, 'message' => "You must enter the person's last name(s)")
							),
							'email' => array(
								'unique' => array('rule' => 'isUniqueField', 'field' => 'email', 'message' => "Someone is already using this email address"),
								'valid' => array('rule' => VALID_EMAIL, 'message' => "You must enter a valid email address")
							),
							'passwd' => array(
								'notusername' => array('rule' => 'notEqualToField', 'field' => 'username', 'message' => 'Your password cannot be the same as your username'),
								'spaces' => array('rule' => '/^[^ ]*$/', 'message' => "Your password cannot contain spaces"),
								'length' => array('rule' => array('between', 7, 50), 'message' => "Your password must be between 7 and 50 characters long"),
								'required' => array('rule' => VALID_NOT_EMPTY, 'message' => "You must enter a password")
							),
							'passwd2' => array('rule' => 'equalToField', 'field' => 'passwd', 'message' => 'The passwords do not match')
						);

		return parent::beforeValidate();
	}
	
	/**
	 * Creates a random alphanumeric password of the given length
	 *
	 * @param string $length The number of characters that should be in the password
	 * @return void A randomly generated password
	 * @author David Roberts
	 */
	
	function createRandomPassword($length = 7) 
	{
		// The letters 'o' and 'l' and the numbers 0 and 1 have been removed, as they can be mistaken for each other.
		$chars = "abcdefghijkmnpqrstuvwxyz23456789";
		srand((double)microtime()*1000000);
		
		$i = 0;
		$pass = '' ;

		while ($i <= $length) 
		{
			$num = rand() % 33;
			$tmp = substr($chars, $num, 1);
			$pass = $pass . $tmp;
			$i++;
		}

		return $pass;
	}
	
	/**
	 * Generates a username based on the attributes of the person. Tries to use the person's
	 * email address, but if it is already in use as a username, generates the username based on
	 * the person's first and last names
	 *
	 * @param string $person 
	 * @return void
	 * @author David Roberts
	 */
	
	function createUsername($user)
	{
		$isUniqueParams = array('field' => 'username');
		if(isset($user['User'])) $user = $user['User'];
		
		$username = '';
		
		if(strlen($username) < 4 || !$this->isUniqueField($username, $isUniqueParams))
		{
			// The email address is no good, so make something out of the first and last names
			$username = strtolower(preg_replace("/[^\w]/", "", $user['first_name'][0] . $user['last_name']));
			if(strlen($username) < 4) strtolower($username = preg_replace("/[^\w]/", "", $user['first_name'] . $user['last_name']));
		
			// If the username is too short we need to make it longer
			if(strlen($username) < 4) $username .= "user";
			
			// Check the username is unique and if not add a number on the end
			if(!$this->isUniqueField($username, $isUniqueParams))
			{
				$i = 2;
				// This while loop isn't very efficient, but this code should hardly ever be executed
				while(!$this->isUniqueField($username . $i, $isUniqueParams))
				{
					$i++;
				}
				$username .= $i;
			}
		}
		
		return $username;
	}
	
	function generateNameList($col = 'first_name', $orderBy = 'id ASC')
	{
		$list = parent::generateNameList($col, $orderBy);
		
		if($col == 'name' || $col == 'first_name')
		{
			$list[0] = 'everyone';
		}
		
		return $list;
	}
	
	function generateFullNameList($controller)
	{
		$data = $this->findAll(null, array('id', 'first_name', 'last_name'), "last_name, first_name");
		
		for($i = 0; $i < count($data); $i++) 
		{ 
			$index = $data[$i]['User']['id'];
			$list[$index] = $controller->formatter()->name($data[$i]['User']['first_name'], $data[$i]['User']['last_name']);
		}
		
		return $list;
	}

}
?>