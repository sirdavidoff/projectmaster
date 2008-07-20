<?php

class UsersController extends AppController 
{
	// Configuration vars
	var $name = 'Users';
	var $uses = array('User', 'Project', 'Contact', 'Meeting', 'Action');
	var $components = array('Agenda');
	var $helpers = array('Html', 'Form', 'Forms', 'Ajax');
	
	//var $layout = 'top';
	
	// NB: validateField must always be in the $allow array if you want the forms helper's
	// AJAX validation to work properly
	// var $allow = array('login', 'register', 'sendLoginDetails', 'validateField');
	var $allowIfLoggedIn = array('index', 'changePassword', 'add');
	var $redirectSafe = array('index', 'settings', 'listAll', 'view');
	
	/**
	 * Returns whether the given user is allowed to perform the given action on the
	 * given object(s). The action should correspond to a function (action) in the controller
	 *
	 * @param string $action The action in the controller that we want to test against
	 * @param array $objects An array containing the parameters for the action
	 * @param string $user The user performing the action (if null the logged-in user is used)
	 * @return boolean Whether the user is allowed to perform the action on the object
	 * @author David Roberts
	 */
	
	/*function userCan($action, $objects = null, $user = null)
	{
		if(!isset($user)) $user = $this->Cauth->user();
		
		// Administrators can do anything
		if($user && $user['level'] == ADMIN) return true;
		
		switch($action)
		{
			case 'delete':
				return $objects[0] == $user['id']; // The user can delete themself
				break;
				
			case 'index':
				return true;
				break;
		}
		
		trigger_error(_t("Permissions not defined for action '$action' in the $this->name controller (see the userCan() function)", true), E_USER_WARNING);
		return false;
	}*/


	/**
	 * Lets a logged-in user add another person to the system
	 *
	 * @return void
	 * @author David Roberts
	 */
	
	function add()
	{		
		/*if(!$this->Config->get('allow_add_person'))
		{
			$this->flashError(_t("You are not allowed to add other people at this time.", true), $this->Tracking->lastSafePage());
		}*/
		
		$this->_createNewPerson(false);
		
		$this->pageTitle = "Add a Person";
	}
	
	/**
	 * Used by add(). Handles the dynamics of entering a person's details in
	 * the system
	 *
	 * @param string $selfCreated 
	 * @return void
	 * @author David Roberts
	 */
	
	function _createNewPerson($selfCreated)
	{		
		if (!empty($this->data))
		{
			$this->cleanUpFields();

			if(!$selfCreated)
			{
				$this->data['User']['username'] = $this->User->createUsername($this->data);
				$this->data['User']['passwd'] = $this->User->createRandomPassword();
				$this->data['User']['password'] = $this->data['User']['passwd'];
			}
			$this->User->create($this->data);
			$error = !$this->User->validates();

			if(!$error) 
			{
				// Hash the password and then save
				$this->data['User']['passwd'] = $this->Cauth->password($this->data['User']['passwd']);
				$error = !$this->User->save($this->data, false);
				
				if(!$error)
				{	
						$this->data['User']['id'] = $this->User->id;
						$this->logInfo("New person added (".$this->User->id.") by " . $this->Cauth->user('id'));
						/*$name = $this->htmlLink($this->formatter()->name($this->data['User']['first_name'], $this->data['User']['last_name']),
												array('controller' => 'users', 'action' => 'view/' . $this->User->id));
						$this->flash(_t("An user has been created for $name.", true), $this->Tracking->lastSafePage());*/
						$this->pageTitle = "User Added";
						$this->render('addDone');
				}
			}
				
			
			if($error)
			{
				unset($this->data['User']['passwd']);
				unset($this->data['User']['passwd2']);
				
				if($selfCreated) 
				{
					$this->flashError('Your information could not be saved. Please try again.');
				} else {
					$this->flashError('The information could not be saved. Please try again.');
				}
			}
		}
		
	}	
	
	function addDone()
	{
		if(!isset($this->data['User']['password']))
		{
			$this->flashError("Nobody was added", '/users/index');
		}
		
		$this->pageTitle = "User Added";
	}
	
	
	function view($id = null)
	{
		if(!$id) $id = $this->Cauth->user('id');
		
		$this->data = $this->User->read(null, $id);
		
		if (!$id || !$this->data) {
			$this->flashError("The details of the user could not be found.", $this->Tracking->lastSafePage());
		}
		
		// Get the agenda info for the next week
		$start = date('Y-m-d');
		$end = date('Y-m-d', mktime(0, 0, 0, date('m'), date('d')+6, date('Y')));
		
		$events = array();
		$datelessActions = array();
		$overdueActions = array();
		$this->Agenda->getAgenda(null, $id, $start, $end, $events, $datelessActions, $overdueActions);
		
		$this->set('current', true);
		$this->set('start', $start);
		$this->set('end', $end);

		$this->set('events', $events);
		$this->set('datelessActions', $datelessActions);
		$this->set('overdueActions', $overdueActions);
		
		// Get the project info for this user
		$this->Project->unbindModel( array('hasMany' => array('Contact')) );
		$pids = $this->getUserProjects($id, null, false);
		
		$otherProj = $this->Project->generateProjectNameList("Project.project_status_id ASC, Project.started_on DESC");
		
		if(count($pids) > 0) 
		{
			$where = "";
			foreach($pids as $p) 
			{
				$where .= "Project.id = '$p' OR ";
				unset($otherProj[$p]);
			}
			$where .= "0";
			$this->Project->recursive = 2;
			$this->Project->unbindModel( array('hasMany' => array('Contact')) );
			$this->Project->TeamMember->unbindModel( array('belongsTo' => array('User')) );
			$this->set('projects', $this->Project->findAll($where, null, "Project.project_status_id ASC, Project.started_on DESC"));
		} else {
			$this->set('projects', array());
		}
		
		// Set up other projects
		$this->set('otherProjList', $otherProj);
		$this->set('roleList', $this->Project->TeamMember->Role->generateNameList());
		
		$this->set('multiProj', true);
		$this->pageTitle = $this->formatter()->name($this->data['User']['first_name'], $this->data['User']['last_name']);
	}
	
	
	/**
	 * This is where we are redirected to when we go to the site root (/). Decides where
	 * to redirect the user
	 *
	 * @return void
	 * @author David Roberts
	 */
	
	function front()
	{
		$relProj = $this->getUserProjects();
		
		// If the user has any projects, redirect to the most important one
		if(count($relProj) > 0) 
		{
			$this->redirect(array('controller' => 'projects', "action" => 'view/' . $relProj[0]));
		} else {
			// If the user has no projects, redirect to their settings page
			$this->redirect(array('controller' => 'users', "action" => 'view'));
		}
	}


	
	/**
	 * Lets the user change their password
	 *
	 * @return void
	 * @author David Roberts
	 */
	
	function changePassword()
	{	
		if (!empty($this->data) && $this->data['User']['old_passwd'])
		{
			// Get the current password from the DB
			$user = $this->User->find(array($this->User->escapeField() => $this->Cauth->user('id')));
			
			// Check that the current password is correct
			if($this->Cauth->password($this->data['User']['old_passwd']) != $user['User']['passwd'])
			{
				$this->User->validationErrors['old_passwd'] = "The password specified was incorrect.";
			} else {
				// Check that the new password is valid
				$user['User']['passwd'] = $this->data['User']['passwd'];
				$user['User']['passwd2'] = $this->data['User']['passwd2'];

				$this->User->data = $user;
				if($this->User->validates())
				{
					// Save the new passwd
					$this->User->id = $this->Cauth->user('id');
					$this->User->saveField('passwd', $this->Cauth->password($this->data['User']['passwd']));
					
					$this->logInfo("Password changed (".$this->User->id.")");
					$this->flash("Your password has been changed.", $this->Tracking->lastSafePage());
					
				}
				
				$this->flashError("There was a problem changing your password.");
			}
			
			unset($this->data['User']);
		}
		
		$this->pageTitle = "Change Your Password";
	}
	
	
	
	/**
	 * Set's the password for the given user to a random alphanumeric string
	 *
	 * @param string $id 
	 * @return void
	 * @author David Roberts
	 */
	
	function resetPassword($id)
	{	
		$this->User->recursive = 0;
		$this->data = $this->User->read(null, $id);
		
		if (!$id || !$this->data) {
			$this->flashError("The details of the user could not be found.", $this->Tracking->lastSafePage());
		}
		
		$newPass = $this->User->createRandomPassword();
		
		$this->User->id = $id;
		$this->User->saveField('passwd', $this->Cauth->password($newPass));
		
		$name = $this->formatter()->name($this->data['User']['first_name'], $this->data['User']['last_name']);
		$this->flash("The password for $name has been changed to '<strong>$newPass</strong>'.", $this->Tracking->lastSafePage());
	}
	
	
	
	
	/**
	 * Logs the user in. This functionality is entirely handled by the Cauth component
	 *
	 * @return void
	 * @author David Roberts
	 */
	
	function login()
	{		
		// The Cauth component deals with the login automatically
		$this->layout = 'login';
		$this->pageTitle = "Login";
	}
	
	/**
	 * Logs the user out
	 *
	 * @return void
	 * @author David Roberts
	 */
	
	function logout()
	{		
		if($this->Cauth->loggedIn())
		{
			$this->Cauth->logout();
			$this->flash("You have been logged out.");
		} else {
			$this->flashError("You are not logged in.");
		}
		
		$this->redirect(array('action' => 'login'), null, true);
	}
	
	
	function deactivate($id)
	{	
		$this->User->recursive = 0;
		$this->data = $this->User->read(null, $id);
		
		if (!$id || !$this->data) {
			$this->flashError("The details of the user could not be found.", $this->Tracking->lastSafePage());
		}
		
		$this->User->id = $id;
		$this->User->saveField('is_active', 0);
		
		$name = $this->formatter()->name($this->data['User']['first_name'], $this->data['User']['last_name']);
		$this->flash("$name has been deactivated and can no longer log in.", $this->Tracking->lastSafePage());
	}
	
	
	function activate($id)
	{	
		$this->User->recursive = 0;
		$this->data = $this->User->read(null, $id);
		
		if (!$id || !$this->data) {
			$this->flashError("The details of the user could not be found.", $this->Tracking->lastSafePage());
		}
		
		$this->User->id = $id;
		$this->User->saveField('is_active', 1);
		
		$name = $this->formatter()->name($this->data['User']['first_name'], $this->data['User']['last_name']);
		$this->flash("$name has been activated and can now log in.", $this->Tracking->lastSafePage());
	}
	
	/**
	 * Removes a user user from the system. If the user to be deleted is logged in,
	 * logs them out first
	 *
	 * @param string $id 
	 * @return void
	 * @author David Roberts
	 */
	
	function delete($id = null) 
	{	
		$person = $this->Person->find($id, array('first_name', 'last_name'), null, -1);
		
		// TODO: We should check for this stuff in the userCan function, not here
		/*if(!$id || !$person) 
		{
			$this->flashError(_t('Invalid id for user.', true), $this->Tracking->lastSafePage());
		}
		
		// Check that we're allowed to delete this person
		if(!$this->User->allowedTo('delete', $id))
		{
			$this->flashError(_t('You are not allowed to delete this person.', true), $this->Tracking->lastSafePage());
		}*/
		
		// If the logged-in person is deleting themselves, log them out first
		if ($this->Cauth->user('id') == $id) 
		{
			$this->Cauth->logout();
			$loggedOut = true;
		}
		
		// Everything's OK, so do the actual deleting
		if ($this->Person->del($id)) // If we delete the Person the User will be deleted, but not vice-versa
		{
			$this->logInfo("User deleted (".$id.")");
			
			if (isset($loggedOut)) 
			{
				$this->flash("Your user has been deleted and you have been logged out.");
				$this->redirect(array('action'=>'index'), null, true);
			} else {
				$name = $this->formatter()->name($person['Person']['first_name'], $person['Person']['last_name']);
				$this->flash("The details of $name have been deleted.", $this->Tracking->lastSafePage());
			}
		} else {
			$this->flashError('There was an error deleting the user.', $this->Tracking->lastSafePage());
			$this->log("Error deleting user with ID ($id)");
		}
	}
	
	function listAll()
	{
		$users = $this->User->findAll(null, null, "User.is_active DESC, User.last_name, User.first_name");
		$this->set('users', $users);
		$this->set('projectList', $this->Project->generateProjectNameList());
		$this->set('projectStatuses', $this->Project->generateNameList('project_status_id'));
		
		$this->pageTitle = "All Users";
	}

}
?>