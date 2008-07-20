<?php

class EventsController extends AppController 
{
	// Configuration vars
	var $name = 'Events';
	var $uses = array('Event');
	var $helpers = array('Html', 'Form', 'Forms', 'Ajax');

	/*function listAll($contactId = null, $showAllEvents = true)
	{
		
		//$this->set('userList', $this->Event->User->generateNameList());		
		$this->set('contact_id', $contactId);
		$this->set('showAllEvents', $showAllEvents);
		
		// The already present events and any event that is in the process of being added (but whose
		// validation failed) will both use the $this->data['Event'] array. For this reason, we put the
		// editing one in $editEvent
		$this->set('editEvent', $this->data['Event']);
		
		$this->Event->recursive = 2;
		$events = $this->Event->findAll(array('contact_id' => $contactId), null, "date ASC, time ASC");
		$this->data['Event'] = Set::extract($events, "{n}.Event");
	}*/
	
	
	function add()
	{		
		if (!empty($this->data))
		{
			$this->cleanUpFields();
			$this->Event->create();
			
			if($this->Event->save($this->data))
			{
				$this->flash("The event has been added.");
			} else {
				$this->flashError('The event could not be added. Please try again.');
			}
		}
			
		
		$this->setAction('listAll', $this->data['Event']['contact_id']);
	}
	
	
	function delete($id = null) 
	{	
		$data = $this->Event->find($id, array('contact_id'), null, -1);

		if ($this->Event->del($id))
		{
			$this->flash("The event has been removed.");
		} else {
			$this->flashError('There was an error deleting the event.');
		}
		
		$this->setAction('listAll', $data['Event']['contact_id']);
	}
	
	function saveField($modelName, $fieldName, $id, $isForeignKey = false)
	{
		if($fieldName == "date_readable")
		{
			//$this->setAction('saveReadableDate', $id, $this->params['form']['value']);
			//$this->requestEvent("events/saveReadableDate/$id" . $this->params['form']['value']);
			
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
		$date = $this->Event->setDate($readableValue);
		$this->Event->id = $id;
		$this->Event->saveField('date', $date);
		
		$this->set('date', $date);
		$this->set('slashDate', $readableValue);
	}

	/*function reschedule()
	{
		// @TODO: We really need some validation here...
		$this->saveReadableDate($this->data['Event']['id'], $this->data['Event']['date_readable']);
		
		if($this->data['Event']['time'] == "hh:mm") $this->data['Event']['time'] = "";
		$this->Event->id = $this->data['Event']['id'];
		$this->Event->saveField('time', $this->data['Event']['time']);
		
		$msg = "The event was rescheduled to the " . $this->data['Event']['date_readable'];
		if($this->data['Event']['time']) $msg .= " at " . $this->data['Event']['time'];
		$this->flash($msg);
		$this->redirect($this->referer(), true);
	}*/

}
?>