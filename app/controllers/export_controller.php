<?php

class ExportController extends AppController 
{
	// Configuration vars
	var $name = 'Export';
	var $uses = array('Contact');
	var $helpers = array('Html', 'Form', 'Forms', 'Ajax');
	
	function summary($pid)
	{		
		// Include PEAR::Spreadsheet_Excel_Writer
		require_once "Spreadsheet/Excel/Writer.php";

		// Create an instance
		$xls =& new Spreadsheet_Excel_Writer();

		// Send HTTP headers to tell the browser what's coming
		$xls->send("summary" . date('Y-m-d') . ".xls");
		
		// Set the style for the column headings
		$formatHeading =& $xls->addFormat();
		$formatHeading->setBold();
		
		// Set the style for the status cells
		$formatStatus[1] =& $xls->addFormat();
		$formatStatus[1]->setFgColor(22);
		$formatStatus[2] =& $xls->addFormat();
		$formatStatus[2]->setFgColor(45);
		$formatStatus[3] =& $xls->addFormat();
		$formatStatus[3]->setFgColor(43);
		$formatStatus[4] =& $xls->addFormat();
		$formatStatus[4]->setFgColor(44);
		$formatStatus[5] =& $xls->addFormat();
		$formatStatus[5]->setFgColor(50);

		// Add a worksheet to the file, returning an object to add data to
		$sheet =& $xls->addWorksheet('Summary');
		
		// Set the column widths
		$sheet->setColumn(0, 0, 15);
		$sheet->setColumn(1, 1, 15);
		$sheet->setColumn(2, 2, 30);
		$sheet->setColumn(3, 3, 30);
		$sheet->setColumn(4, 4, 20);
		$sheet->setColumn(5, 5, 20);
		$sheet->setColumn(6, 6, 30);
		$sheet->setColumn(7, 7, 40);
		
		// Write the headers
		$sheet->write(0, 0, 'Status', $formatHeading);
		$sheet->write(0, 1, 'Objective (euros)', $formatHeading);
		$sheet->write(0, 2, 'Company', $formatHeading);
		$sheet->write(0, 3, 'Interviewee', $formatHeading);
		$sheet->write(0, 4, 'Website', $formatHeading);
		$sheet->write(0, 5, 'Sector', $formatHeading);
		$sheet->write(0, 6, 'Alternative Aps', $formatHeading);
		$sheet->write(0, 7, 'Notes', $formatHeading);

		$contacts = $this->Contact->findAll("Contact.project_id = '$pid'", null, "sector_id ASC, market_id ASC, contacttype_id ASC, status_id DESC");
		
		for($i = 0; $i < count($contacts); $i++) 
		{ 
			$c = $contacts[$i];
			
			// Write Sector
			$sheet->write($i+1, 5, $c['Sector']['name']);
			
			// Write Contact Name
			$sheet->write($i+1, 2, $c['Contact']['name']);
			
			// Write Interviewee and Position
			if(count($c['Person']) > 0)
			{
				$interviewee = $c['Person'][0]['name'];
				if(isset($c['Person'][0]['position'])) $interviewee .= " (" . $c['Person'][0]['position'] . ")";
				$sheet->write($i+1, 3, $interviewee);
			}
			
			// Write Website
			$sheet->write($i+1, 4, $c['Contact']['website']);
			
			// Write Alternative Aps
			$open = "";
			$numOpen = count($c['Openee']);
			for($j = 0; $j < $numOpen; $j++) 
			{ 
				$open .= $c['Openee'][$j]['name'];
				if($j != $numOpen-1) $open .= ", ";
			}
			$sheet->write($i+1, 6, $open);
			
			// Write Status
			$status = $c['Contact']['status_id'];
			$sheet->write($i+1, 0, $this->getSlashStatus($c), $formatStatus[$status]);
		}

		// Finish the spreadsheet, dumping it to the browser
		$xls->close();
		
		exit();
	}
	
	/**
	 * Returns the status of a contact in slash format, e.g PTO/OTS/SPACE
	 *
	 * @param string $c 
	 * @return void
	 * @author David Roberts
	 */
	
	function getSlashStatus($c)
	{
		switch($c['Contact']['status_id'])
		{
			case 1: return $this->getNegStatus($c);
					break;
			
			case 2: return 'To Open';
					break;
					
			case 3: return 'Open';
					break;
					
			case 4: return $this->getFollowUpStatus($c);
					break;
					
			case 5: return $this->getPosStatus($c);
					break;
		}
	}
	
	function getNegStatus($c)
	{
		if($c['Contact']['contacttype_id'] == 1) return 'Aps / Neg';
		if(count($c['Meeting']) == 0) return 'Neg Ent';
		if(!$this->onceHadStatusCode($c, 4)) return 'Pto / Neg';
		return 'Pto / Rto / Neg';
	}
	
	function getFollowUpStatus($c)
	{
		if($c['Contact']['contacttype_id'] == 1) return 'Aps / Rto';
		return 'Pto / Rto';
	}
	
	function getPosStatus($c)
	{
		if($c['Contact']['contacttype_id'] == 1) return 'Aps / Pos';
		
		if(!$this->onceHadStatusCode($c, 4)) 
			$start = 'Pto / OTS';
		else
			$start = 'Pto / Rto';
		
		if(isset($c['Contract']) && count($c['Contract']) > 0)
			$start .= " / " . $c['Contract'][0]['space'];
			
		return $start;
	}
	
	function onceHadStatusCode($c, $code)
	{
		for($i = 0; $i < count($c['ContactStatusChange']); $i++) 
		{ 
			if($c['ContactStatusChange'][$i]['status_id'] == $code) return true;
		}
		
		return false;
	}

}
?>