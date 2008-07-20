<?php

class SummariesController extends AppController 
{
	// Configuration vars
	var $name = 'Summaries';
	var $uses = array('Contact', 'Contract', 'Meeting', 'Action', 'Note', 'ContactStatusChange');
	var $helpers = array('Html', 'Form', 'Forms', 'Ajax');

	function activity($pid, $day = null)
	{
		if(!isset($day) || $day == 'today') $day = date('Y-m-d');
		$dayStart = $day . " 00:00:00";
		$dayEnd = $day . " 23:59:59";
		
		// Contracts signed or paid
		$contractsToday = $this->Contract->findAll("Contact.project_id = '$pid' AND (Contract.paid_on = '$day' OR Contract.signed_on = '$day')", 'Contract.contact_id');		
		$contractsTodayIds = Set::extract($contractsToday, '{n}.Contract.contact_id');
		
		// Meetings attended or added
		$meetingsToday = $this->Meeting->findAll("Contact.project_id = '$pid' AND (Meeting.date = '$day' OR (Meeting.created >= '$dayStart' AND Meeting.created <= '$dayEnd'))", 'Meeting.contact_id');		
		$meetingsTodayIds = Set::extract($meetingsToday, '{n}.Meeting.contact_id');

		// Actions completed
		$actionsDoneToday = $this->Action->findAll("Contact.project_id = '$pid' AND Action.completed = 1 AND Action.completed_at >= '$dayStart' AND Action.completed_at <= '$dayEnd'", 'Action.contact_id');
		$this->set('actionsDoneToday', $actionsDoneToday);
		$actionsDoneTodayIds = Set::extract($actionsDoneToday, '{n}.Action.contact_id');
		
		// Notes updated
		$notesAddedToday = $this->Note->findAll("Contact.project_id = '$pid' AND (Note.updated >= '$dayStart' AND Note.updated <= '$dayEnd')", 'Note.contact_id');
		$this->set('notesAddedToday', $notesAddedToday);
		$notesAddedTodayIds = Set::extract($notesAddedToday, '{n}.Note.contact_id');
		
		// Status changed to positivo or negativo
		$statusesChangedTodayIds = $this->getContactStatusChangedIds($pid, $day);
		
		// Get a list of contact ids
		$contactIds = array_unique(array_merge($contractsTodayIds, $meetingsTodayIds, $actionsDoneTodayIds, $notesAddedTodayIds, $statusesChangedTodayIds));
		
		// Retrieve the contacts from the ids, ordered by their status on $day
		$contacts = $this->Contact->findAll(Array('Contact.id' => $contactIds), null, 'Contact.status_id DESC, Contact.market_id, Contact.contacttype_id, Contact.sector_id');
		$this->addHistoricalStatuses($day, $contacts);
		$this->sortContactsByHistoricalStatus($contacts);
		$this->set('contacts', $contacts);
		
		$this->set('statuses', $this->Contact->Status->generateNameList());
		$this->set('pid', $pid);
		$this->set('day', $day);
		$this->pageTitle = "Activity Summary";
	}
	
	/**
	 * Returns the IDs of the contacts that were set to either negativo or positivo on $day and were still at
	 * that status at the end of the day
	 *
	 * @param string $day 
	 * @return void
	 * @author David Roberts
	 */
	
	function getContactStatusChangedIds($pid, $day = null)
	{
		if(!isset($day) || $day == 'today') $day = date('Y-m-d');
		$dayStart = $day . " 00:00:00";
		$dayEnd = $day . " 23:59:59";
		
		$conditions = "(Contact.project_id = '$pid' AND ContactStatusChange.changed_at >= '$dayStart' AND ContactStatusChange.changed_at <= '$dayEnd')";
		$statusChanges = $this->ContactStatusChange->findAll($conditions, null, "ContactStatusChange.contact_id, ContactStatusChange.changed_at DESC");
		
		$revelantContacts = Array();
		if(isset($statusChanges) && count($statusChanges) > 0)
		{
			$currentId = '';
			foreach($statusChanges as $change) 
			{
				if($change['ContactStatusChange']['contact_id'] != $currentId)
				{
					$currentId = $change['ContactStatusChange']['contact_id'];
					if($change['ContactStatusChange']['status_id'] == 1 || $change['ContactStatusChange']['status_id'] == 5)
					{
						$revelantContacts[] = $change['ContactStatusChange']['contact_id'];
					}
				}
			}
		}
		
		return $revelantContacts;
	}
	
	function addHistoricalStatuses($day, &$contacts)
	{
		if(isset($contacts) && count($contacts) > 0)
		{
			foreach($contacts as &$contact) 
			{
				$contact['Contact']['historical_status'] = $this->getStatusOn($day, $contact);
			}
		}
	}
	
	function sortContactsByHistoricalStatus(&$contacts)
	{
		usort($contacts, Array('SummariesController', 'cmpHistoricalStatus'));
	}
	
	function cmpHistoricalStatus($a, $b)
	{
	    if ($a['Contact']['historical_status'] == $b['Contact']['historical_status']) {
	        if ($a['Contact']['contacttype_id'] == $b['Contact']['contacttype_id']) {
		        if ($a['Contact']['market_id'] == $b['Contact']['market_id']) {
			        if ($a['Contact']['sector_id'] == $b['Contact']['sector_id']) {
				        return 0;
				    }
				    return ($a['Contact']['sector_id'] < $b['Contact']['sector_id']) ? -1 : 1;
			    }
			    return ($a['Contact']['market_id'] < $b['Contact']['market_id']) ? -1 : 1;
		    }
		    return ($a['Contact']['contacttype_id'] < $b['Contact']['contacttype_id']) ? -1 : 1;
	    }
	    return ($a['Contact']['historical_status'] > $b['Contact']['historical_status']) ? -1 : 1;
	}
	
	function cmpMarket($a, $b)
	{
	    
	}
	
	function getStatusOn($day, $contact)
	{
		if(isset($contact['ContactStatusChange']) && count($contact['ContactStatusChange']) > 0)
		{
			foreach($contact['ContactStatusChange'] as $change) 
			{
				if($change['changed_at'] <= $day . " 23:59:59")
				{
					return $change['status_id'];
				}
			}
		}
		
		// No status changes before $day, so use current status
		// (this will never happen from now on as contacts are 
		//  given a status change when they are created)
		return $contact['Contact']['status_id'];
	}
}
?>