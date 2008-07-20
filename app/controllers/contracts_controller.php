<?php

class ContractsController extends AppController 
{
	// Configuration vars
	var $name = 'Contracts';
	var $uses = array('Contract');
	var $helpers = array('Html', 'Form', 'Forms', 'Ajax');

	function listAll($contactId = null)
	{
		
		//$this->set('userList', $this->Contract->User->generateNameList());		
		$this->set('contact_id', $contactId);
		
		// The already present contracts and any contract that is in the process of being added (but whose
		// validation failed) will both use the $this->data['Contract'] array. For this reason, we put the
		// editing one in $editContract
		$this->set('editContract', $this->data['Contract']);
		
		$this->Contract->recursive = 2;
		$contracts = $this->Contract->findAll(array('contact_id' => $contactId), null, "signed_on ASC");
		$this->data['Contract'] = Set::extract($contracts, "{n}.Contract");
	}
	
	
	function add()
	{		
		if (!empty($this->data))
		{
			$this->cleanUpFields();
			$this->Contract->create();
			
			if($this->Contract->save($this->data))
			{
				$this->flash("The contract has been added.");
			} else {
				$this->flashError('The contract could not be added. Please try again.');
			}
		}
		
		$this->setAction('listAll', $this->data['Contract']['contact_id']);
	}
	
	
	function delete($id = null) 
	{	
		$data = $this->Contract->find($id, array('contact_id'), null, -1);

		if ($this->Contract->del($id))
		{
			$this->flash("The contract has been removed.");
		} else {
			$this->flashError('There was an error deleting the contract.');
		}
		
		$this->setAction('listAll', $data['Contract']['contact_id']);
	}
	
	function saveField($modelName, $fieldName, $id, $isForeignKey = false)
	{
		if($fieldName == "payment_by_readable")
		{
			$this->saveReadableDate($id, $this->params['form']['value'], 'payment_by');
			$this->render('save_readable_date');
			exit();
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
	
	
	function saveReadableDate($id, $readableValue, $fieldName)
	{
		$date = $this->Contract->setDate($readableValue);
		$this->Contract->id = $id;
		$this->Contract->saveField($fieldName, $date);
		
		$this->set('date', $date);
		$this->set('slashDate', $readableValue);
	}

	function pay($id)
	{
		$this->Contract->id = $id;
		$this->Contract->saveField('paid_on', date('Y-m-d'));
		$this->flash('The contract was marked as paid');
		$this->redirect($this->referer(), true);
	}
	
	function reschedule()
	{
		// @TODO: We really need some validation here...
		$this->saveReadableDate($this->data['Contract']['id'], $this->data['Contract']['payment_by_readable'], 'payment_by');
		
		$msg = "The contract payment deadline was rescheduled to the " . $this->data['Contract']['payment_by_readable'];
		$this->flash($msg);
		$this->redirect($this->referer(), true);
	}

}
?>