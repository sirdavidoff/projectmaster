<?php

class MeetingsController extends AppController 
{
	// Configuration vars
	var $name = 'Meetings';
	var $uses = array('Meeting');
	var $helpers = array('Html', 'Form', 'Forms', 'Ajax');

	function listAll($contactId = null, $showAllMeetings = true)
	{
		
		//$this->set('userList', $this->Meeting->User->generateNameList());		
		$this->set('contact_id', $contactId);
		$this->set('showAllMeetings', $showAllMeetings);
		
		// The already present meetings and any meeting that is in the process of being added (but whose
		// validation failed) will both use the $this->data['Meeting'] array. For this reason, we put the
		// editing one in $editMeeting
		$this->set('editMeeting', $this->data['Meeting']);
		
		$this->Meeting->recursive = 2;
		$meetings = $this->Meeting->findAll(array('contact_id' => $contactId), null, "date ASC, time ASC");
		$this->data['Meeting'] = Set::extract($meetings, "{n}.Meeting");
	}
	
	
	function add()
	{		
		if (!empty($this->data))
		{
			$this->cleanUpFields();
			$this->Meeting->create();
			
			if($this->Meeting->save($this->data))
			{
				$this->flash("The meeting has been added.");
			} else {
				$this->flashError('The meeting could not be added. Please try again.');
			}
		}
			
		
		$this->setAction('listAll', $this->data['Meeting']['contact_id']);
	}
	
	
	function delete($id = null) 
	{	
		$data = $this->Meeting->find($id, array('contact_id'), null, -1);

		if ($this->Meeting->del($id))
		{
			$this->flash("The meeting has been removed.");
		} else {
			$this->flashError('There was an error deleting the meeting.');
		}
		
		$this->setAction('listAll', $data['Meeting']['contact_id']);
	}
	
	function saveField($modelName, $fieldName, $id, $isForeignKey = false)
	{
		if($fieldName == "date_readable")
		{
			//$this->setAction('saveReadableDate', $id, $this->params['form']['value']);
			//$this->requestMeeting("meetings/saveReadableDate/$id" . $this->params['form']['value']);
			
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
			$data = $otherModel->find($this->params['form']['value'], array('name'), null, -1);
			print $data[$otherModelName]['name'];
		} else {
			echo $this->params['form']['value'];
		}
		
		exit();
	}
	
	
	function saveReadableDate($id, $readableValue)
	{
		$date = $this->Meeting->setDate($readableValue);
		$this->Meeting->id = $id;
		$this->Meeting->saveField('date', $date);
		
		$this->set('date', $date);
		$this->set('slashDate', $readableValue);
	}

	function reschedule()
	{
		// @TODO: We really need some validation here...
		$this->saveReadableDate($this->data['Meeting']['id'], $this->data['Meeting']['date_readable']);
		
		if($this->data['Meeting']['time'] == "hh:mm") $this->data['Meeting']['time'] = "";
		$this->Meeting->id = $this->data['Meeting']['id'];
		$this->Meeting->saveField('time', $this->data['Meeting']['time']);
		
		$msg = "The meeting was rescheduled to the " . $this->data['Meeting']['date_readable'];
		if($this->data['Meeting']['time']) $msg .= " at " . $this->data['Meeting']['time'];
		$this->flash($msg);
		$this->redirect($this->referer(), true);
	}

}
?>