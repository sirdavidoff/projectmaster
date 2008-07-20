<?php

class PeopleController extends AppController 
{
	// Configuration vars
	var $name = 'People';
	var $uses = array('Person');
	var $helpers = array('Html', 'Form', 'Forms', 'Ajax');

	function listFromContact($contactId = null)
	{
		$this->set('contact_id', $contactId);
		$people = $this->Person->findAll("contact_id = " . $contactId, null, "ordering ASC");
		$this->data['Person'] = Set::extract($people, "{n}.Person");
	}
	
	
	function add()
	{		
		if (!empty($this->data))
		{
			$this->cleanUpFields();
			$this->Person->create();
			
			// Work out what the highest review ordering number for this contact is, and give
			// this review a higher one
			$highestOrdering = $this->Person->field('ordering', "contact_id = '".$this->data['Person']['contact_id']."'", 'ordering DESC');
			if(!$highestOrdering) $highestOrdering = 0;
			$this->data['Person']['ordering'] = $highestOrdering + 1;
			
			if($this->Person->save($this->data))
			{
				$this->flash("The person has been added.");
			} else {
				$this->flashError('The person could not be added. Please try again.');
			}
		}	
		
		$this->setAction('listFromContact', $this->data['Person']['contact_id']);
	}
	
	
	function reorder()
	{
		// The ids of the reviews are passed in an array in the new order
		$ids = $this->params['form']['people'];
		
		// Save the new orderings to the database (1st review gets highest number)
		$i = 0;
		foreach($ids as $id)
		{
			$this->Person->id = $id;
			$this->Person->saveField('ordering', $i++);
		}
		
		// Re-render the list of reviews to show that we have succeeded
		$contact_id = $this->Person->field('contact_id', "Person.id = '".$ids[0]."'");
		
		$this->setAction('listFromContact', $contact_id);
	}
	
	
	function delete($id = null) 
	{	
		$data = $this->Person->find($id, array('contact_id'), null, -1);

		if ($this->Person->del($id))
		{
			$this->flash("The person has been removed.");
		} else {
			$this->flashError('There was an error deleting the person.');
		}
		
		$this->setAction('listFromContact', $data['Person']['contact_id']);
	}

}
?>