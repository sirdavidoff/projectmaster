<?php
uses('view/helpers/text');
/**
 * Helper methods for the formatting of various data.
 *
 * @package ppc
 * @subpackage ppc.helpers
 * @author David Roberts
 */

/**
 * Helper methods for the formatting of various data.
 *
 * @package ppc
 * @subpackage ppc.helpers
 * @author David Roberts
 */

uses('view/helpers/time');
class CalendarHelper extends TextHelper {

	var $helpers = array('Html', 'Javascript', 'Ajax', 'Time');
	
	function calendarCellDate($date, $startDate = null)
	{
		$d = substr($date, 8, 2);
		$m = substr($date, 5, 2);
		$y = substr($date, 0, 4);
		
		$format = 'j';
		
		// Add on the month if it's the first day of the month or first day in the calendar
		if($d == "01" || (isset($startDate) && $startDate == $date)) $format .= " M";
		
		// Add on the year if it's the first day of the year or first day in the calendar
		if(($d == "01" && $m == "01") || (isset($startDate) && $startDate == $date)) $format .= " Y";
		
		return date($format, mktime(0, 0, 0, $m, $d, $y));
	}
	
	function daysLeftInMonth($date)
	{
		$d = substr($date, 8, 2);
		$m = substr($date, 5, 2);
		$y = substr($date, 0, 4);
		
		return date('t', mktime(0, 0, 0, $m, $d, $y)) - date('j', mktime(0, 0, 0, $m, $d, $y));
	}
	
	function dayOfMonth($date)
	{
		$d = substr($date, 8, 2);
		$m = substr($date, 5, 2);
		$y = substr($date, 0, 4);
		
		return date('j', mktime(0, 0, 0, $m, $d, $y));
	}
	
	function getEventDate($event)
	{
		if(isset($event['Action'])) return $event['Action']['deadline_date'];
		if(isset($event['Meeting'])) return $event['Meeting']['date'];
		if(isset($event['Contract'])) return $event['Contract']['payment_by'];
	}
	
}

?>