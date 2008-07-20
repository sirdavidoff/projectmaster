<?php
/**
 * Controller for user accounts
 * 
 * Deals with the registration and logging in of users, as well as changing/resending passwords, etc
 * 
 * @package ppc
 * @subpackage ppc.controllers
 * @todo Check for JS/cookies on every page
 * @todo Log the creation/editing of records
 */
class ProjectsController extends AppController 
{
	// Configuration vars
	var $name = 'Projects';
	var $uses = array('Project', 'TeamMember', 'User');
	var $helpers = array('Html', 'Form', 'Forms', 'Ajax');

	var $redirectSafe = array('index', 'view', 'overview', 'listAll');

	
	
	function listAll($order = 'start')
	{
		switch ($order) {
			case 'media':
				$orderCode = "media_id ASC, started_on DESC"; break;
			case 'start':
			default:
				$orderCode = "started_on DESC, media_id ASC"; break;
		}
		
		$this->Project->unbindModel( array('hasMany' => array('Contact')) );
		$this->set('projects', $this->Project->findAll(null, null, $orderCode));
		$this->set('userList', $this->User->generateFullNameList($this));
		$this->set('order', $order);
		$this->pageTitle = "All Projects";
	}
	
	
	/**
	 * If the project is still open, redirects to the agenda page. If not, redirects to the overview
	 *
	 * @param string $id 
	 * @return void
	 * @author David Roberts
	 */
	
	function view($id = null)
	{
		$this->Project->recursive = 0;
		$this->data = $this->Project->read(null, $id);
		
		if (!$id || !$this->data) {
			$this->flashError("The details of the project could not be found.", $this->referer());
		}
		
		if($this->formatter()->isInFuture($this->data['Project']['started_on'])) {
			$this->redirect(array('controller' => 'projects', 'action' => 'overview/' . $id));
		} elseif($this->data['Project']['project_status_id'] == 1) {
			$this->redirect(array('controller' => 'agenda', 'action' => 'view/' . $id));
		} else {
			$this->redirect(array('controller' => 'projects', 'action' => 'overview/' . $id));
		}
	}
	
	
	
	function overview($id = null)
	{
		$this->Project->recursive = 0;
		$this->data = $this->Project->read(null, $id);
		
		if (!$id || !$this->data) {
			$this->flashError("The details of the project could not be found.", $this->referer());
		}
		
		// Get all the users associated with this project
		$members = $this->Project->TeamMember->findAllFromProj($id, "status = '1'");
		$this->set('members', $members);
		
		// Number of contacts
		$q = $this->Project->query("SELECT COUNT(`id`) as `x` FROM `contacts` WHERE `project_id` = '$id'");
		$this->set('nC', $q[0][0]['x']);
		
		// Number of contacts with each status
		for($i = 1; $i < 6; $i++) 
		{ 
			$q = $this->Project->query("SELECT COUNT(`id`) as `x` FROM `contacts` WHERE `project_id` = '$id' AND `status_id` = '$i'");
			$numContactStatus[$i] = $q[0][0]['x'];
			$this->set('nCS', $numContactStatus);
		}
		
		// Number of Meetings
		$q = $this->Project->query("SELECT COUNT(`meetings`.`id`) as `x` FROM `meetings` INNER JOIN `contacts` ON `meetings`.`contact_id` = `contacts`.`id` WHERE `project_id` = '$id'");
		$nM = $q[0][0]['x'];
		$this->set('nM', $q[0][0]['x']);
		
		// Number of Pending Meetings
		$q = $this->Project->query("SELECT COUNT(`meetings`.`id`) as `x` FROM `meetings` INNER JOIN `contacts` ON `meetings`.`contact_id` = `contacts`.`id` WHERE `project_id` = '$id' AND `meetings`.`date` > '" . date('Y-m-d') . "'");
		$this->set('nMp', $q[0][0]['x']);
		
		// Date of first meeting
		if($nM > 0) 
		{
			$q = $this->Project->query("SELECT `meetings`.`date` as `x` FROM `meetings` INNER JOIN `contacts` ON `meetings`.`contact_id` = `contacts`.`id` WHERE `project_id` = '$id' ORDER BY `meetings`.`date` ASC LIMIT 1");
			$this->set('dM1', $q[0]['meetings']['x']);
		}
		
		// Number of Contracts
		$q = $this->Project->query("SELECT COUNT(`contracts`.`id`) as `x` FROM `contracts` INNER JOIN `contacts` ON `contracts`.`contact_id` = `contacts`.`id` WHERE `project_id` = '$id'");
		$nCon = $q[0][0]['x'];
		$this->set('nCon', $q[0][0]['x']);
		
		// Total Value of Contracts
		$q = $this->Project->query("SELECT SUM(`contracts`.`cost`) as `x` FROM `contracts` INNER JOIN `contacts` ON `contracts`.`contact_id` = `contacts`.`id` WHERE `project_id` = '$id'");
		$this->set('vCon', $q[0][0]['x']);
		
		// Date of first Contract
		if($nCon > 0) 
		{
			$q = $this->Project->query("SELECT `contracts`.`signed_on` as `x` FROM `contracts` INNER JOIN `contacts` ON `contracts`.`contact_id` = `contacts`.`id` WHERE `project_id` = '$id' ORDER BY `contracts`.`signed_on` ASC LIMIT 1");
			$this->set('dCon1', $q[0]['contracts']['x']);
		}
		
		$this->set('pid', $id);
		
		$this->setTeamMemberData($members);
		/*$currentUsers = array_combine(Set::extract($members, "{n}.User.id"), Set::extract($members, "{n}.User.first_name"));
		$allUsers = $this->Project->TeamMember->User->generateFullNameList($this);
		$otherUsers = $this->otherUsers($allUsers, $currentUsers);
		$this->set('otherUsersList', $otherUsers);*/
		
		$this->setExtraData();
		
		$f = $this->formatter();
		$this->pageTitle = $f->projectName($this->data);
		
	}
	
	
	
	/**
	 * Removes currentUsers from allUsers and returns the result
	 *
	 * @return void
	 * @author David Roberts
	 */
	
	function otherUsers($allUsers, $currentUsers = null)
	{
		if(count($currentUsers) > 0) 
		{
			foreach($currentUsers as $cid => $value) 
			{
				if(isset($allUsers[$cid])) unset($allUsers[$cid]);
			}
		}
		
		return $allUsers;
	}
	
	
	function addTeamMember()
	{		
		if (!empty($this->data))
		{
			$this->cleanUpFields();
			
			$this->data['TeamMember']['started_on'] = date('Y-m-d');
			$this->data['TeamMember']['status'] = 1;
			
			// Take the previous start date if the user already exists
			$old = $this->TeamMember->find("project_id = '".$this->data['TeamMember']['project_id']."' AND user_id = '".$this->data['TeamMember']['user_id']."'");
			if($old)
			{
				$oldId = $old['TeamMember']['id'];
				$this->data['TeamMember']['started_on'] = $old['TeamMember']['started_on'];
			}
			
			$this->TeamMember->create();
			
			if($this->TeamMember->save($this->data))
			{
				if(isset($oldId)) $this->TeamMember->del($oldId);
				$this->TeamMember->updateShortNames($this->data['TeamMember']['project_id'], $this);
				$this->flash("The team member has been added.");
			} else {
				$this->flashError('The team member could not be added. Please try again.');
			}
		}	
		
		$this->setAction('listAllTeamMembers', $this->data['TeamMember']['project_id']);
	}
	
	
	
	function listAllTeamMembers($projectId = null)
	{
		$this->set('pid', $projectId);
		$members = $this->Project->TeamMember->findAllFromProj($projectId, "status = '1'");
		$this->set('members', $members);
		$this->setTeamMemberData($members);
		
		$this->render('../team_members/list_all');
	}
	
	
	function setTeamMemberData($members) 
	{
		if($members) {
			$currentUsers = array_combine(Set::extract($members, "{n}.User.id"), Set::extract($members, "{n}.User.first_name"));
		} else {
			$currentUsers = array();
		}
		
		$allUsers = $this->Project->TeamMember->User->generateFullNameList($this);
		$otherUsers = $this->otherUsers($allUsers, $currentUsers);
		$this->set('otherUsersList', $otherUsers);
		
		$this->set('statusList', $this->Project->Contact->Status->generateNameList());
		$this->set('roleList', $this->Project->TeamMember->Role->generateNameList());
	}
	
	
	function removeTeamMember($id = null) 
	{	
		$data = $this->TeamMember->find($id, array('project_id'), null, -1);

		$this->TeamMember->id = $id;
		$this->TeamMember->saveField('status', 0);
		$this->TeamMember->saveField('finished_on', date('Y-m-d'));
		
		$this->flash("The team member has been removed.");

		/*if ($this->TeamMember->del($id))
		{
			$this->TeamMember->updateShortNames($data['TeamMember']['project_id'], $this);
			$this->flash("The team member has been removed.");
		} else {
			$this->flashError('There was an error removing the team member.');
		}*/
		
		$this->setAction('listAllTeamMembers', $data['TeamMember']['project_id']);
	}
	
	
	
	function add()
	{		
		if (!empty($this->data))
		{
			$this->cleanUpFields();
			$this->Project->create();
			if($this->Project->save($this->data))
			{	
				$this->flash("The project was created successfully. Would you like to " . $this->htmlLink('add another', '/projects/add') . "?", "/projects/overview/" . $this->Project->id);
			} else {
				$this->flashError('The project could not be saved. Please try again.');
			}
		}	
		
		$this->setExtraData();
		
		$this->pageTitle = "Add a Project";
	}
	
	/**
	 * (re)-opens a project
	 *
	 * @param string $id 
	 * @return void
	 * @author David Roberts
	 */
	
	function open($id)
	{	
		$this->Project->recursive = 0;
		$this->data = $this->Project->read(null, $id);
		
		if (!$id || !$this->data) {
			$this->flashError("The details of the project could not be found.", $this->Tracking->lastSafePage());
		}
		
		$this->Project->id = $id;
		$this->Project->saveField('project_status_id', 1);
		
		$this->flash("The project has been opened.", $this->Tracking->lastSafePage());
	}
	
	
	
	function close($id)
	{	
		$this->Project->recursive = 0;
		$this->data = $this->Project->read(null, $id);
		
		if (!$id || !$this->data) {
			$this->flashError("The details of the project could not be found.", $this->Tracking->lastSafePage());
		}
		
		$this->Project->id = $id;
		$this->Project->saveField('project_status_id', 2);
		$this->Project->saveField('finished_on', date('Y-m-d'));
		
		$this->flash("The project has been closed.", $this->Tracking->lastSafePage());
	}


	
	function delete($id = null) 
	{	
		$data = $this->Project->find($id, array('name'), null, -1);

		if ($this->Project->del($id))
		{
			$this->flash("'".$data['Project']['name']."' has been deleted.", 'listAll');
		} else {
			$this->flashError('There was an error deleting the note.', $this->referer());
		}
	}
	
	
	function setExtraData()
	{
		$this->set('mediaList', $this->Project->Media->generateNameList());
	}
	
	
	function saveField($modelName, $fieldName, $id, $isForeignKey = false)
	{
		if(substr($fieldName, -9, 9) == "_readable")
		{
			$origField = substr($fieldName, 0, strlen($fieldName)-9);
			
			// TODO: There has to be an easier way to do this, but setAction doesn't seem to render the view...
			$this->saveReadableDate($id, $modelName, $origField, $this->params['form']['value']);
			$this->render('save_readable_date');
			exit();
		}
		
		if($fieldName == 'user_id' && $this->params['form']['value'] == "0")
		{
			$this->params['form']['value'] = "";
		}

		$model = $this->$modelName;
		
		$model->id = $id;
		$model->saveField($fieldName, $this->params['form']['value']);
		
		// If we're saving a foreign key, we return the name it references
		// rather than the ID itself
		if($isForeignKey && !$this->params['form']['value'])
		{
			print "everyone";
		} elseif ($isForeignKey) {
			$otherModelName = ucwords(substr($fieldName, 0, -3));
			$otherModel = $model->$otherModelName;
			$data = $otherModel->find($this->params['form']['value'], array('name'), null, -1);
			print $data[$otherModelName]['name'];
		} else {
			echo $this->params['form']['value'];
		}
		
		exit();
	}
	
	
	function saveReadableDate($id, $modelName, $fieldName, $readableValue)
	{
		$model = $this->$modelName;
		
		$date = $model->setDate($readableValue);
		$model->id = $id;
		$model->saveField($fieldName, $date);
		
		$this->set('date', $date);
		$this->set('slashDate', $readableValue);
	}


}
?>