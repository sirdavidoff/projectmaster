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
class FormatHelper extends TextHelper {

	var $helpers = array('Html', 'Javascript', 'Ajax', 'Time');

	/**
	 * Formats a person's name. Puts all words in lowercase with uppercase first letters.
	 *
	 * @param string $first The person's first name
	 * @param string $last  The person's last name
	 * @return void The person's full name (first_name last_name)
	 * @author David Roberts
	 */
	
	function name($first, $last)
	{
		return $this->_ucsmart(low($first)) . " " . $this->_ucsmart(low($last));
	}
	
	function projectName($data)
	{
		$start = $data['Project']['started_on'];
		$startYear = date('y', mktime(0, 0, 0, substr($start, 5, 2), substr($start, 8, 2)-1, substr($start, 0, 4)));
		return $this->_ucsmart(low($data['Media']['name'])) . " " . $this->_ucsmart(low($data['Project']['subject'])) . " " . $startYear;
	}
	
	function _ucsmart($text)
	{
		return preg_replace('/([^\w\']|^)(\w)/e', '"$1".strtoupper("$2")', strtolower($text));
	}
	
	function url($url)
	{
		if(substr($url, 0, 7) != "http://")
		{
			$url = "http://" . $url;
		}
		
		return $url;
	}
	
	// Converts a mysql date into a readable format (e.g. '4th Dec')
	function shortDate($date_string)
	{
		if(!isset($date_string)) return null;
		
		$date = $date_string ? $this->Time->fromString($date_string) : time();
		
		if ($this->Time->isToday($date)) {
			$ret = "Today";
		} elseif ($this->Time->wasYesterday($date)) {
			$ret = "Yest.";
		} elseif ($this->Time->isTomorrow($date)) {
			$ret = "Tom.";
		} else {
			$ret = date("jS M", $date);
		}

		return $this->output($ret);
	}
	
	
	// Converts a mysql date into a readable format (e.g. '4th December')
	function date($date_string, $includeWeekday = false, $includeYear = false, $useNouns = true)
	{
		$date = $date_string ? $this->Time->fromString($date_string) : time();
		
		if ($useNouns && $this->Time->isToday($date)) {
			$ret = "Today";
		} elseif ($useNouns && $this->Time->wasYesterday($date)) {
			$ret = "Yesterday";
		} elseif ($useNouns && $this->Time->isTomorrow($date)) {
			$ret = "Tomorrow";
		} else {
			$ret = date("jS M", $date);
			if($includeWeekday) $ret = date("D", $date) . " " . $ret;
			if($includeYear) $ret .= " " . date("Y", $date);
		}

		return $this->output($ret);
	}
	
	
	// Converts a mysql date into dd/mm/yy 2007-03-10
	function slashDate($date_string)
	{
		if(!isset($date_string)) return 'dd/mm/yy';
		
		$y = substr($date_string, 2, 2);
		$m = substr($date_string, 5, 2);
		$d = substr($date_string, 8, 2);
		
		return "$d/$m/$y";
	}
	
	function dateRange($start, $end)
	{
		$date1 = $start ? $this->Time->fromString($start) : time();
		$date2 = $end ? $this->Time->fromString($end) : time();
		
		$y1 = date("Y", $date1);
		$y2 = date("Y", $date2);
		$m1 = date("M", $date1);
		$m2 = date("M", $date2);
		$d1 = date("jS", $date1);
		$d2 = date("jS", $date2);
		
		$sameYear = $y1 == $y2;
		$sameMonth = $y1 == $y2 && $m1 == $m2;
		$sameDate = $y1 == $y2 && $m1 == $m2 && $d1 == $d2;
		
		if($sameDate) return "$d1 $m1 $y1";
		
		$p1 = $d1;
		if(!$sameMonth) $p1 .= " $m1";
		if(!$sameYear) $p1 .= " $y1";
		
		return "$p1 - $d2 $m2 $y2";
	}
	
	function duration($start, $end, $format = "w")
	{
		$s = mktime(0, 0, 0, substr($start, 5, 2), substr($start, 8, 2), substr($start, 0, 4));
		$e = mktime(0, 0, 0, substr($end, 5, 2), substr($end, 8, 2), substr($end, 0, 4));
		
		$duration = $e - $s;
		
		switch($format)
		{
			case 'd':
				return ceil($duration / (60*60*24));
			case 'w':
			default:
				return round($duration / (60*60*24*7), 1);
		}
	}
	
	function isInFuture($date)
	{
		$d = mktime(0, 0, 0, substr($date, 5, 2), substr($date, 8, 2), substr($date, 0, 4));
		$t = mktime(0, 0, 0, date('m'), date('d'), date('Y'));
		
		return $d > $t;
	}
	
}

?>