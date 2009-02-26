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
class ContactsController extends AppController 
{
	// Configuration vars
	var $name = 'Contacts';
	var $uses = array('Contact', 'TeamMember', 'Person', 'Contract', 'Meeting', 'Action', 'Note', 'ContactStatusChange');
	var $helpers = array('Html', 'Form', 'Forms', 'Ajax', 'Rendering');

	var $redirectSafe = array('index', 'view', 'listAll');

	
	function add($pid = null)
	{		
		if (!empty($this->data))
		{
			$pid = $this->data['Contact']['project_id'];
			
			if(!$pid) 
			{
				$this->flashError('The contact could not be saved. No project ID was provided.');
			} else {
				$this->cleanUpFields();
				$this->Contact->create();
				if($this->Contact->save($this->data))
				{
					// Add an entry to the contact_status_change table just so we know what status the
					// contact started out with
					$this->ContactStatusChange->create();
					$data = Array('contact_id' => $this->Contact->id, 'status_id' => $this->data['Contact']['status_id'], 'changed_at' => date('Y-m-d H:i:s'));
					$this->ContactStatusChange->save($data);
				
					$this->flash("The contact was created successfully. Would you like to " . $this->htmlLink('add another', "/contacts/add/$pid") . "?", "/contacts/view/" . $this->Contact->id);
				} else {
					$this->flashError('The contact could not be saved. Please try again.');
				}
			}
		} else {
			// If we are adding a new contact, set certain things to the most likely values
			$this->data['Contact']['contacttype_id'] = 2; // Contratante
			$this->data['Contact']['market_id'] = 1; // Market A
			$this->data['Contact']['status_id'] = 3; // Open
			$res = $this->Contact->Assignee->find(array('project_id' => $pid, 'user_id' => $this->Cauth->user('id')), array('id')); 
			if(isset($res['TeamMember']['id']))
				$this->data['Contact']['assigned_to'] = $res['TeamMember']['id']; // Assign the new contact to the current user
		}	
		
		if(!$pid){
			$this->flashError('Could not add a contact as no project ID was provided.', $this->Tracking->lastSafePage());
		}
		
		$this->set('pid', $pid);
		$this->setExtraData($pid);
		
		$this->pageTitle = "Add a Contact";
	}
	
	
	
	function setExtraData($pid)
	{
		$this->set('marketList', $this->Contact->Market->generateNameList());
		$this->set('contacttypeList', $this->Contact->Contacttype->generateNameList());
		$this->set('sectorList', $this->Contact->Sector->generateNameList());
		$statusList = $this->Contact->Status->generateNameList();
		$this->set('statusList', $statusList);
		//$this->set('userList', $this->Contact->Action->User->generateNameList());
		$this->set('userList', $this->TeamMember->generateCurrentUserList($pid));
		$assignedUserList = $this->TeamMember->generateAssignedUserList($pid);
		$this->set('assignedUserList', $assignedUserList);
		$teamUserList = $this->TeamMember->generateTeamUserList($pid);
		$this->set('teamUserList', $teamUserList);
		$this->set('allUserList', $this->TeamMember->generateNameList($pid));
		$contacts = $this->Contact->generateNameList('name', 'Contact.name', "Contact.project_id = '$pid'");
		$this->set('contactList', $contacts);
		$this->set('openerContactList', $this->otherContactList($contacts, 'Opener'));
		$this->set('openeeContactList', $this->otherContactList($contacts, 'Openee'));
		$this->set('multiActions', $this->getMultiActions($assignedUserList, $statusList));
	}
	
	/**
	 * Returns a list of contacts with the contacts associated with the current one removed
	 *
	 * @return void
	 * @author David Roberts
	 */
	
	function otherContactList($contacts, $type)
	{
		if(isset($this->data['Contact']) && isset($this->data['Contact']['id'])) 
		{
			if(isset($contacts[$this->data['Contact']['id']])) unset($contacts[$this->data['Contact']['id']]);
		}
		
		if(isset($this->data[$type])) 
		{
			foreach($this->data[$type] as $contact) 
			{
				unset($contacts[$contact['id']]);
			}
		}
		
		return $contacts;
	}
	
	
	function view($id = null, $print = false)
	{
		$this->Contact->recursive = 1;
		$this->data = $this->Contact->read(null, $id);
		
		if (!$id || !$this->data) {
			$this->flashError("The details of the contact could not be found.", $this->referer());
		}
		
		$pid = $this->data['Contact']['project_id'];
		$this->set('pid', $this->data['Contact']['project_id']);
		
		// Set default values for adding a contract
		// this why we should use requestAction rather than renderElement...
		$this->set('editContract', $this->Contact->Contract->defaultValues());

		// Show all meetings by default
		$this->set('showAllMeetings', true);
		
		if($print) $this->layout = 'print';
		
		$this->set('print', $print);
		$this->setExtraData($pid);
		$this->pageTitle = $this->data['Contact']['name'];
		
	}


	
	function delete($id = null, $batch = false) 
	{	
		$data = $this->Contact->find($id, array('name', 'project_id'), null, -1);
		$pid = $data['Contact']['project_id'];

		if ($this->Contact->del($id))
		{
			if(!$batch) $this->flash("'".$data['Contact']['name']."' has been deleted.", "listAll/$pid");
		} else {
			$this->flashError('There was an error deleting the note.', $this->referer());
		}
	}
	
	
	
	function listAll($pid, $order = 'sector', $assignee = 'any')
	{
		switch ($order) {
			case 'status':
				$orderCode = "status_id DESC, contacttype_id ASC, market_id ASC, sector_id ASC"; break;
			case 'type':
				$orderCode = "contacttype_id ASC, market_id ASC, status_id DESC, sector_id ASC"; break;
			case 'sector':
			default:
				$orderCode = "Sector.ordering, Sector.name, status_id DESC, contacttype_id ASC, market_id ASC"; break;
		}
		
		// Get the IDs of all the team members of this project
		$members = $this->Contact->Assignee->generateTeamUserList($pid);
		
		switch ($assignee) {
			case 'any':
				$whereClause = "1"; 
				$titleInsert = "";
				break;
			case 'noone':
				$whereClause = "";
				if(isset($members)) 
				{
					foreach($members as $key => $value) 
					{
						$whereClause .= "Contact.assigned_to != '$key' AND ";
					}
				}
				$whereClause .= "1";
				$titleInsert = "Unassigned";
				break;
			default:
				$whereClause = "Contact.assigned_to = '$assignee'";
				$titleInsert = $members[$assignee] . "'s";
		}
		
		//$this->Contact->restrict(Array('Market', 'Contacttype', 'Sector', 'Status', 'Meeting.id'));
		$this->set('contacts', $this->Contact->findAll("Contact.project_id = '$pid' AND ($whereClause)", null, $orderCode));
		$this->setExtraData($pid);
		$this->set('pid', $pid);
		$this->set('order', $order);
		$this->set('assignee', $assignee);
		
		$this->pageTitle = "All $titleInsert Contacts";
	}
	
	
	
	function phoneNumbers($pid, $type = 'all')
	{
		switch ($type) {
			case 'active':
				$whereCode = "Contact.project_id = '$pid' AND status_id > 2";
				break;
			case 'all':
			default:
				$whereCode = "Contact.project_id = '$pid'";
				break;
		}
		
		//$this->Contact->restrict(Array('Market', 'Contacttype', 'Sector', 'Status', 'Meeting.id'));
		$this->set('contacts', $this->Contact->findAll($whereCode, null, "Contact.name"));
		$this->set('pid', $pid);
		$this->set('type', $type);
		$this->layout = 'print';
		$this->pageTitle = "Contact Phone Numbers";
	}
	
	
	
	function getMultiActions($teamUserList, $statusList)
	{
		$actions = array();
		
		if(count($teamUserList) > 0) 
		{
			foreach($teamUserList as $id => $name) 
			{
				$actions["1_" . $id] = "Assign to $name";
			}
		}
		
		if(count($statusList) > 0) 
		{
			foreach($statusList as $id => $name) 
			{
				$actions["2_" . $id] = "Status to $name";
			}
		}
		
		$actions['3'] = "Delete";
		
		return $actions;
	}
	
	
	/**
	 * Performs an action on multiple contacts
	 *
	 * @return void
	 * @author David Roberts
	 */
	function multi()
	{
		$action = $this->data['Contact']['multiAction'];
		if(!$action) $this->flashError('You must select an action to perform from the drop-down box.', $this->referer());
		 
		if(!isset($this->data['Contact']['ids']) || !count($this->data['Contact']['ids'] <= 0)) $this->flashError('You must select at least one contact using the checkboxes on the right.', $this->referer()); 
		$ids = $this->data['Contact']['ids'];
		
		$action = split('_', $action);
		
		switch ($action[0]) {
			case '1':
				foreach($ids as $id) 
				{
					$this->params['form']['value'] = $action[1];
					$this->saveField("Contact", "assigned_to", $id, false, true);
				}
				$this->flash(count($ids) . " contacts have been assigned.");
				break;
			
			case '2':
				foreach($ids as $id) 
				{
					$this->params['form']['value'] = $action[1];
					$this->saveField("Contact", "status_id", $id, false, true);
				}
				$this->flash(count($ids) . " statuses have been changed.");
				break;
				
			case '3':
				foreach($ids as $id) 
				{
					$this->delete($id, true);
				}
				$this->flash(count($ids) . " contacts have been deleted.");
				break;
		}

		$this->redirect($this->referer(), null, true);
		
	}


	
	
	function search($pid, $query = null)
	{
		if(!isset($query) && isset($this->params['url']['query'])) $query = $this->params['url']['query'];
		
		if(!isset($query) || $query == "")
		{
			$this->redirect(array('controller' => 'users', 'action' => 'front'), null, true);
		}
		
		//Check to see whether there are any relevant people associated with a contact
		$where = "Contact.project_id = '$pid' AND (Person.name LIKE '%".$query."%' OR Person.address LIKE '%".$query."%' OR Person.tel LIKE '%".$query."%' OR Person.mobile LIKE '%".$query."%')";
		$people = $this->Contact->Person->findAll($where, array('contact_id'), "contacttype_id ASC, market_id ASC, status_id DESC, sector_id ASC");
		$peopleIds = Set::extract($people, '{n}.Person.contact_id');
		
		// Construct the SQL to add the contacts found above to the query
		$peopleWhere = "0";
		if(count($peopleIds) > 0) 
		{
			$peopleWhere = "(";
			foreach($peopleIds as $peopleId) 
			{
				$peopleWhere .= "Contact.id = $peopleId OR ";
			}
			$peopleWhere .= "0)";
		}
		
		$this->Contact->recursive = 1;
		$where = "Contact.project_id = '$pid' AND (Contact.name LIKE '%".$query."%' OR Contact.address LIKE '%".$query."%' OR Contact.tel LIKE '%".$query."%' OR $peopleWhere)";
		$contacts = $this->Contact->findAll($where, null, "contacttype_id ASC, market_id ASC, status_id DESC, sector_id ASC");
		
		if(count($contacts) == 1)
		{
			$this->redirect(array('action' => 'view/' . $contacts[0]['Contact']['id']), null, true);
		}
		
		$this->set('pid', $pid);
		$this->set('query', $query);
		$this->set('contacts', $contacts);
		$this->pageTitle = "Search for '$query'";
	}
	
	function removeOpener($subjectId, $openerId)
	{
		$this->Contact->habtmDelete('Opener', $subjectId, $openerId);
	}
	
	function addOpener($subjectId, $openerId)
	{
		$this->Contact->habtmAdd('Opener', $subjectId, $openerId);
	}
	
	function removeOpenee($subjectId, $openerId)
	{
		$this->Contact->habtmDelete('Openee', $subjectId, $openerId);
	}
	
	function addOpenee($subjectId, $openerId)
	{
		$this->Contact->habtmAdd('Openee', $subjectId, $openerId);
	}
	
	function saveField($modelName, $fieldName, $id, $isForeignKey = false, $batch = false)
	{
		
		if($modelName == 'Contact' && $fieldName == 'status_id')
		{
			$this->ContactStatusChange->create();
			$data = Array('contact_id' => $id, 'status_id' => $this->params['form']['value'], 'changed_at' => date('Y-m-d H:i:s', time() - date("Z")));
			$this->ContactStatusChange->save($data);
		}
		
		parent::saveField($modelName, $fieldName, $id, $isForeignKey, $batch);
		
	}

}
?>