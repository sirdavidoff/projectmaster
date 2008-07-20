<?php 
/**
 * Deals with retrieving and putting together the agenda. The controller that calls this must
 * include the appropriate models
 *
 * @package ppc
 * @subpackage ppc.components
 * @author David Roberts
 */

class AgendaComponent extends Object
{

	
	/**
	 * Called before the current action is executed, provided that this component
	 * is included in the $components array of the controller. Tracks the current
	 * page if appropriate
	 *
	 * @param string $controller 
	 * @return void
	 * @author David Roberts
	 */
	
	function startup(&$controller) 
	{
		// Make the controller available to other methods in this component
		$this->controller = $controller;
		
		// Make this component available in views
		//$this->controller->set('tracking', $this);
	}
	
	
	function getAgenda($pid, $userId = 0, $start = null, $end = null, &$events, &$datelessActions, &$overdueActions)
	{
		$actions = $this->getActions($pid, $userId, $start, $end);
		$meetings = $this->getMeetings($pid, $start, $end, $userId);
		$contracts = $this->getContracts($pid, $start, $end, $userId);
		
		$events = $this->mergeEventsByDate($actions, $this->mergeEventsByDate($meetings, $contracts));
		
		// Get dateless actions
		$datelessActions = $this->getDatelessActions($pid, $userId);
		
		// Get overdue actions
		$overdueActions = $this->getOverdueActions($pid, $userId);
		
		return;
	}
	
	
	function getActions($pid, $userId, $start, $end)
	{
		$conditions = "Action.completed = 0 AND Action.deadline_date >= '$start' AND Action.deadline_date <= '$end'";
		if($pid) $conditions .= " AND (Contact.project_id = '$pid')";
		if($userId) $conditions .= " AND (Action.user_id = '$userId')" /*" OR Action.user_id = 0)"*/;
		return $this->controller->Contact->Action->findAll($conditions, null, "deadline_date ASC, deadline_time ASC");
	}
	
	function getOverdueActions($pid, $userId)
	{
		$conditions = "Action.completed = 0 AND Action.deadline_date < '".date('Y-m-d')."'";
		if($pid) $conditions .= " AND (Contact.project_id = '$pid')";
		if($userId) $conditions .= " AND (Action.user_id = '$userId')" /*" OR Action.user_id = 0)"*/;
		return $this->controller->Contact->Action->findAll($conditions, null, "Action.deadline_date ASC");
	}
	
	function getDatelessActions($pid, $userId)
	{
		$conditions = "Action.completed = 0 AND Action.deadline_date IS NULL";
		if($pid) $conditions .= " AND (Contact.project_id = '$pid')";
		if($userId) $conditions .= " AND (Action.user_id = '$userId')" /*" OR Action.user_id = 0)"*/;
		return $this->controller->Contact->Action->findAll($conditions, null, "Action.created ASC");
	}
	
	function getMeetings($pid, $start, $end, $userId)
	{
		if($userId) 
		{
			$pClause = '0';
			$pids = $this->controller->getUserProjects($userId);
			if(count($pids) > 0) 
			{
				$pClause = '(';
				foreach($pids as $value) 
				{
					$pClause .= "Contact.project_id = '$value' OR ";
				}
				$pClause .= '0)';
			}
		} else {
			$pClause = "Contact.project_id = '$pid'";
		}
		
		return $this->controller->Contact->Meeting->findAll("$pClause AND Meeting.date >= '$start' AND Meeting.date <= '$end'", null, "date ASC, time ASC");
	}
	
	function getContracts($pid, $start, $end, $userId)
	{
		if($userId) 
		{
			$pClause = '0';
			$pids = $this->controller->getUserProjects($userId);
			if(count($pids) > 0) 
			{
				$pClause = '(';
				foreach($pids as $value) 
				{
					$pClause .= "Contact.project_id = '$value' OR ";
				}
				$pClause .= '0)';
			}
		} else {
			$pClause = "Contact.project_id = '$pid'";
		}
		
		return $this->controller->Contact->Contract->findAll("$pClause AND Contract.paid_on IS NULL AND Contract.payment_by >= '$start' AND Contract.payment_by <= '$end'", null, "Contract.payment_by ASC");
	}
	
	function mergeEventsByDate($array1, $array2)
	{
		$events = Array();
		
		for($i = 0; $i < count($array1); $i++) 
		{	
			while (count($array2) > 0 && 
				   $this->getEventDate($array2[0]) <= $this->getEventDate($array1[$i]))
			{
				$events[] = array_shift($array2);
			}
			
			$events[] = $array1[$i];
		}
		
		if(count($array2) > 0) $events = array_merge($events, $array2);
		
		return $events;
	}
	
	function getEventDate($event)
	{
		if(isset($event['Action'])) return $event['Action']['deadline_date'];
		if(isset($event['Meeting'])) return $event['Meeting']['date'];
		if(isset($event['Contract'])) return $event['Contract']['payment_by'];
	}
	
	/**
	 * Returns the date of the last event associated with a project
	 *
	 * @param string $pid 
	 * @return void
	 * @author David Roberts
	 */
	
	function getLastEventDate($pid)
	{
		$meetingDate = null;
		$lastMeeting = $this->controller->Contact->Meeting->find("Contact.project_id = $pid", "Meeting.date", "Meeting.date DESC");
		if(count($lastMeeting) > 0) 
		{
			$meetingDate = $lastMeeting['Meeting']['date'];
		}
		
		$contractDate = null;
		// TODO: Uncomment this once the calendar actually shows contract due dates
		/*$lastContract = $this->controller->Contact->Contract->find("Contact.project_id = $pid", "Contract.payment_by", "Contract.payment_by DESC");
		if(count($lastContract) > 0) 
		{
			$contractDate = $lastContract['Contract']['payment_by'];
		}*/
		
		if(!isset($contractDate) || $meetingDate > $contractDate)
			return $meetingDate;
		else
			return $contractDate;
		
	}
}