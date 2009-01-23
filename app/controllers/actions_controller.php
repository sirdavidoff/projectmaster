<?php

class ActionsController extends AppController 
{
	// Configuration vars
	var $name = 'Actions';
	var $uses = array('Action', 'TeamMember');
	var $components = array('ActionComp');
	var $helpers = array('Html', 'Form', 'Forms', 'Ajax');

	function listAll($contactId = null, $showAllActions = false)
	{
		
		// The already present actions and any action that is in the process of being added (but whose
		// validation failed) will both use the $this->data['Action'] array. For this reason, we put the
		// editing one in $editAction
		$this->set('editAction', $this->data['Action']);
		
		$this->Action->recursive = 2;
		$actions = $this->Action->findAll(array('contact_id' => $contactId /*, 'completed' => 0*/), null, "deadline_date ASC, deadline_time ASC");
		$this->data['Action'] = Set::extract($actions, "{n}.Action");
		
		$pid = $actions[0]['Contact']['project_id'];
		
		$this->set('userList', $this->TeamMember->generateCurrentUserList($pid));
		$this->set('allUserList', $this->TeamMember->generateNameList($pid));
		$this->set('contact_id', $contactId);
		$this->set('showAllActions', $showAllActions);
		
		// Put dateless actions at the end of the array
		//$datelessActions = $this->ActionComp->stripDatelessActions($this->data['Action']);
		//if(isset($datelessActions) && count($datelessActions) > 0) $this->data['Action'] = am($this->data['Action'], $datelessActions);
	}
	
	
	function listOverdue($pid)
	{
		$actions = $this->Action->findAll("Contact.project_id = '$pid' AND Action.completed = 0 AND Action.deadline_date < '".date('Y-m-d')."'", null, "deadline_date ASC, deadline_time ASC");
		
		$this->set('pid', $pid);
		$this->set('events', $actions);
		$this->set('userList', $this->TeamMember->generateCurrentUserList($pid));
		$this->set('allUserList', $this->TeamMember->generateNameList($pid));
		$this->pageTitle = "Overdue Actions";
	}
	
	
	function add()
	{		
		if (!empty($this->data))
		{
			$this->cleanUpFields();
			$this->Action->create();
			
			if($this->Action->save($this->data))
			{
				$this->flash("The action has been added.");
			} else {
				$this->flashError('The action could not be added. Please try again.');
			}
		}
		
		if(isset($this->data['Action']['contactSet'])) // If we are adding the action outside a contact page
		{
			$this->render(array('action' => 'view', 'controller' => 'agenda'));
		} else {
			$this->setAction('listAll', $this->data['Action']['contact_id']);
		}
	}
	
	
	function done($id = null, $goBack = false)
	{
		$data = $this->Action->find($id, array('contact_id'), null, -1);
		
		$this->Action->id = $id;
		$this->Action->saveField('completed', 1);
		$this->Action->saveField('completed_at', date('Y-m-d H:i:s', time() - date("Z")));
		if($this->Cauth->user('id'))
		{
			$this->Action->saveField('completed_by', $this->Cauth->user('id'));
		}
		
		$this->flash("The action has been updated.");
		
		if(!$goBack)
		{
			$this->setAction('listAll', $data['Action']['contact_id']);
		} else {
			$this->redirect($this->referer(), true);
		}
	}
	
	
	function delete($id = null) 
	{	
		$data = $this->Action->find($id, array('contact_id'), null, -1);

		if ($this->Action->del($id))
		{
			$this->flash("The action has been removed.");
		} else {
			$this->flashError('There was an error deleting the action.');
		}
		
		$this->setAction('listAll', $data['Action']['contact_id']);
	}
	
	function saveField($modelName, $fieldName, $id, $isForeignKey = false)
	{
		if($fieldName == "deadline_date_readable")
		{
			//$this->setAction('saveReadableDate', $id, $this->params['form']['value']);
			//$this->requestAction("actions/saveReadableDate/$id" . $this->params['form']['value']);
			
			// TODO: There has to be an easier way to do this, but setAction doesn't seem to render the view...
			$this->saveReadableDate($id, $this->params['form']['value']);
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
			if($otherModelName == 'User')
			{
				$returnField = 'first_name';
			} else {
				$returnField = 'name';
			}
			$data = $otherModel->find($this->params['form']['value'], $returnField, null, -1);
			print $data[$otherModelName][$returnField];
		} else {
			echo $this->params['form']['value'];
		}
		
		exit();
	}
	
	
	function saveReadableDate($id, $readableValue)
	{
		$date = $this->Action->setDate($readableValue);
		$this->Action->id = $id;
		$this->Action->saveField('deadline_date', $date);
		
		$this->set('date', $date);
		$this->set('slashDate', $readableValue);
	}

	function reschedule()
	{
		// @TODO: We really need some validation here...
		$this->saveReadableDate($this->data['Action']['id'], $this->data['Action']['deadline_date_readable']);
		
		if($this->data['Action']['deadline_time'] == "hh:mm") $this->data['Action']['deadline_time'] = "";
		$this->Action->id = $this->data['Action']['id'];
		$this->Action->saveField('deadline_time', $this->data['Action']['deadline_time']);
		
		$msg = "The action was rescheduled to the " . $this->data['Action']['deadline_date_readable'];
		if($this->data['Action']['deadline_time']) $msg .= " at " . $this->data['Action']['deadline_time'];
		$this->flash($msg);
		$this->redirect($this->referer(), true);
	}

}
?>