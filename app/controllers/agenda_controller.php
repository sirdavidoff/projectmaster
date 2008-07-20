<?php


class AgendaController extends AppController 
{
	// Configuration vars
	var $name = 'Agenda';
	var $uses = array('Contact', 'TeamMember', 'Action', 'Project');
	var $components = array('Agenda');
	var $helpers = array('Html', 'Form', 'Forms', 'Ajax', 'Calendar');

	var $redirectSafe = array('agenda');
	
	
	function view($pid, $userId = 0, $start = null, $end = null)
	{
		$current = !isset($start) && !isset($end);
		
		if(!isset($start)) $start = date('Y-m-d');
		if(!isset($end)) $end = date('Y-m-d', mktime(0, 0, 0, date('m'), date('d')+6, date('Y')));
		
		if(!isset($userId)) $userId = 0;
		
		$events = array();
		$datelessActions = array();
		$overdueActions = array();
		$this->Agenda->getAgenda($pid, $userId, $start, $end, $events, $datelessActions, $overdueActions);
		
		$this->set('current', $current);
		$this->set('start', $start);
		$this->set('end', $end);
		$this->set('eStart', date('Y-m-d', mktime(0, 0, 0, substr($start, 5, 2), substr($start, 8, 2)-7, substr($start, 0, 4))));
		$this->set('eEnd', date('Y-m-d', mktime(0, 0, 0, substr($start, 5, 2), substr($start, 8, 2)-1, substr($start, 0, 4))));
		$this->set('lStart', date('Y-m-d', mktime(0, 0, 0, substr($end, 5, 2), substr($end, 8, 2)+1, substr($end, 0, 4))));
		$this->set('lEnd', date('Y-m-d', mktime(0, 0, 0, substr($end, 5, 2), substr($end, 8, 2)+7, substr($end, 0, 4))));
		
		$this->set('pid', $pid);
		$this->set('userId', $userId);
		$this->set('events', $events);
		$this->set('datelessActions', $datelessActions);
		if($current) $this->set('overdueActions', $overdueActions);
		$this->setExtraData($pid);
		$this->pageTitle = "Agenda";
		
	}
	
	
	
	
	function calendar($pid, $start = null, $end = null)
	{
		// If we are in a closed project with no start and end dates specified,
		// show up until the last calendar entry
		$projStatus = $this->Project->find($pid, 'project_status_id');
		$projStatus = $projStatus['Project']['project_status_id'];
		if($projStatus != 1 && !isset($start)) 
		{
			// Find the date of the last entry
			$end = $this->Agenda->getLastEventDate($pid);
			if(isset($end)) {
				$start = date('Y-m-d', mktime(0, 0, 0, substr($end, 5, 2), substr($end, 8, 2)-30, substr($end, 0, 4)));
			}
		}
		
		if(!isset($start)) $start = date('Y-m-d');
		if(!isset($end)) $end = date('Y-m-d', mktime(0, 0, 0, date('m'), date('d')+30, date('Y')));
		
		// Find the date of the first day (Monday) of the week containing the start date
		$startDayNumber = date('N', mktime(0, 0, 0, substr($start, 5, 2), substr($start, 8, 2), substr($start, 0, 4))) - 1;
		$displayStart = date('Y-m-d', mktime(0, 0, 0, substr($start, 5, 2), substr($start, 8, 2)-$startDayNumber, substr($start, 0, 4)));

		// Find the date of the last day (Sunday) of the week containing the end date
		$endDayNumber = date('N', mktime(0, 0, 0, substr($end, 5, 2), substr($end, 8, 2), substr($end, 0, 4))) - 1;
		$displayEnd = date('Y-m-d', mktime(0, 0, 0, substr($end, 5, 2), substr($end, 8, 2)+6-$endDayNumber, substr($end, 0, 4)));

		// Find out how many days we are displaying
		$numDays = round((strtotime($displayEnd) - strtotime($displayStart)) / 86400) + 1;
		
		$meetings = $this->Agenda->getMeetings($pid, $displayStart, $displayEnd, null);
		$contracts = $this->Agenda->getContracts($pid, $displayStart, $displayEnd, null);
		
		$events = $this->Agenda->mergeEventsByDate($meetings, $contracts);
		
		$this->set('start', $displayStart);
		$this->set('end', $displayEnd);
		$this->set('numDays', $numDays);
		
		$this->set('pid', $pid);
		$this->set('events', $events);
		//$this->setExtraData();
		$this->pageTitle = "Calendar";
	}
	
	
	function setExtraData($pid = null)
	{
		//$this->set('marketList', $this->Contact->Market->generateNameList());
		//$this->set('contacttypeList', $this->Contact->Contacttype->generateNameList());
		//$this->set('sectorList', $this->Contact->Sector->generateNameList());
		//$this->set('statusList', $this->Contact->Status->generateNameList());
		//$this->set('userList', $this->Contact->Action->User->generateNameList());
		$this->set('userList', $this->TeamMember->generateCurrentUserList($pid));
		$this->set('allUserList', $this->TeamMember->generateNameList($pid));
		
		$contacts = $this->Contact->generateNameList('name', 'Contact.name', "Contact.project_id = '$pid'");
		$this->set('contactList', $contacts);
	}

}
?>