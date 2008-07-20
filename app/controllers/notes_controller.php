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
class NotesController extends AppController 
{
	// Configuration vars
	var $name = 'Notes';
	var $uses = array('Note', 'TeamMember');
	var $helpers = array('Html', 'Form', 'Forms', 'Ajax');

	function listAll($contactId = null)
	{
		// Work out the projectID
		$c = $this->Note->Contact->find($contactId, 'project_id');
		$pid = $c['Contact']['project_id'];
		
		$this->set('contact_id', $contactId);
		$notes = $this->Note->findAll("contact_id = " . $contactId, null, "ordering DESC");
		$this->data['Note'] = Set::extract($notes, "{n}.Note");
		$this->set('allUserList', $this->TeamMember->generateNameList($pid));
	}
	
	
	function add()
	{		
		if (!empty($this->data))
		{
			$this->cleanUpFields();
			$this->Note->create();
			
			// Work out what the highest review ordering number for this merchant is, and give
			// this review a higher one
			$highestOrdering = $this->Note->field('ordering', "contact_id = '".$this->data['Note']['contact_id']."'", 'ordering DESC');
			if(!$highestOrdering) $highestOrdering = 0;
			$this->data['Note']['ordering'] = $highestOrdering + 1;
			
			if($this->Note->save($this->data))
			{
				$this->flash("The note has been added.");
			} else {
				$this->flashError('The note could not be added. Please try again.');
			}
		}	
		
		$this->setAction('listAll', $this->data['Note']['contact_id']);
	}
	
	
	function reorder()
	{
		// The ids of the reviews are passed in an array in the new order
		$ids = $this->params['form']['notes'];
		
		// Save the new orderings to the database (1st review gets highest number)
		$i = count($ids);
		foreach($ids as $id)
		{
			$this->Note->id = $id;
			$this->Note->saveField('ordering', $i--);
		}
		
		// Re-render the list of reviews to show that we have succeeded
		$contact_id = $this->Note->field('contact_id', "Note.id = '".$ids[0]."'");
		
		$this->setAction('listAll', $contact_id);
	}
	
	
	function delete($id = null) 
	{	
		$data = $this->Note->find($id, array('contact_id'), null, -1);

		if ($this->Note->del($id))
		{
			$this->flash("The note has been removed.");
		} else {
			$this->flashError('There was an error deleting the note.');
		}
		
		$this->setAction('listAll', $data['Note']['contact_id']);
	}

}
?>