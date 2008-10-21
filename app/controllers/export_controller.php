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
		$formatStatus[1][false] =& $xls->addFormat();
		$formatStatus[1][false]->setFgColor(22);
		$formatStatus[1][true] =& $xls->addFormat();
		$formatStatus[1][true]->setFgColor(22);
		$formatStatus[1][true]->setTop(1);
		$formatStatus[1][true]->setTopColor(0);
		
		$formatStatus[2][false] =& $xls->addFormat();
		$formatStatus[2][false]->setFgColor(45);
		$formatStatus[2][true] =& $xls->addFormat();
		$formatStatus[2][true]->setFgColor(45);
		$formatStatus[2][true]->setTop(1);
		$formatStatus[2][true]->setTopColor(0);
		
		$formatStatus[3][false] =& $xls->addFormat();
		$formatStatus[3][false]->setFgColor(43);
		$formatStatus[3][true] =& $xls->addFormat();
		$formatStatus[3][true]->setFgColor(43);
		$formatStatus[3][true]->setTop(1);
		$formatStatus[3][true]->setTopColor(0);
		
		$formatStatus[4][false] =& $xls->addFormat();
		$formatStatus[4][false]->setFgColor(44);
		$formatStatus[4][true] =& $xls->addFormat();
		$formatStatus[4][true]->setFgColor(44);
		$formatStatus[4][true]->setTop(1);
		$formatStatus[4][true]->setTopColor(0);
		
		$formatStatus[5][false] =& $xls->addFormat();
		$formatStatus[5][false]->setFgColor(50);
		$formatStatus[5][true] =& $xls->addFormat();
		$formatStatus[5][true]->setFgColor(50);
		$formatStatus[5][true]->setTop(1);
		$formatStatus[5][true]->setTopColor(0);
		
		$normalFormat[false] =& $xls->addFormat();
		$normalFormat[true] =& $xls->addFormat();
		$normalFormat[true]->setTop(1);      
		$normalFormat[true]->setTopColor(0); 

		// Add a worksheet to the file, returning an object to add data to
		$sheet =& $xls->addWorksheet('Summary');
		
		// Set the column widths
		$sheet->setColumn(0, 0, 8);
		$sheet->setColumn(1, 1, 15);
		$sheet->setColumn(2, 2, 5);
		$sheet->setColumn(3, 3, 30);
		$sheet->setColumn(4, 4, 20);
		$sheet->setColumn(5, 5, 20);
		$sheet->setColumn(6, 6, 30);
		$sheet->setColumn(7, 7, 40);
		
		// Write the headers
		$sheet->write(0, 0, 'Status', $formatHeading);
		$sheet->write(0, 1, 'Sector', $formatHeading);
		$sheet->write(0, 2, 'Market', $formatHeading);
		$sheet->write(0, 3, 'Company', $formatHeading);
		$sheet->write(0, 4, 'Interviewee', $formatHeading);
		$sheet->write(0, 5, 'Website', $formatHeading);
		$sheet->write(0, 6, 'Sector', $formatHeading);
		$sheet->write(0, 7, 'Alternative Aps', $formatHeading);
		$sheet->write(0, 8, 'Notes', $formatHeading);

		$contacts = $this->Contact->findAll("Contact.project_id = '$pid'", null, "sector_id ASC, market_id ASC, contacttype_id ASC, status_id DESC");
		
		$prevSectorId = '';
		$firstInSector = false;
		$j = 0;
		
		for($i = 0; $i < count($contacts); $i++) 
		{ 
			$c = $contacts[$i];
			
			/*$advertised = '';
			foreach($c['Note'] as $note) 
			{
				if(strToUpper(trim(substr($note['text'], 0, 6))) == "ADVERT") 
				{
					$advertised = $note['text'];
					$j++;
				}
			}*/
			
			//if(strlen($advertised) > 0) {
			
			$firstInSector = ($prevSectorId != $c['Sector']['id']);
			$prevSectorId = $c['Sector']['id'];
			
			// Write Sector
			$sheet->write($i+1, 1, $c['Sector']['name'], $normalFormat[$firstInSector]);
			
			// Write Market
			$sheet->write($i+1, 2, $c['Market']['name'], $normalFormat[$firstInSector]);
			
			// Write Contact Name
			$sheet->write($i+1, 3, $c['Contact']['name'], $normalFormat[$firstInSector]);
			
			// Write Interviewee and Position
			if(count($c['Person']) > 0)
			{
				$interviewee = $c['Person'][0]['name'];
				if(isset($c['Person'][0]['position'])) $interviewee .= " (" . $c['Person'][0]['position'] . ")";
				$sheet->write($i+1, 4, $interviewee, $normalFormat[$firstInSector]);
			}
			
			// Write Website
			$sheet->write($i+1, 5, $c['Contact']['website'], $normalFormat[$firstInSector]);
			
			// Write Alternative Aps
			$open = "";
			$numOpen = count($c['Openee']);
			for($j = 0; $j < $numOpen; $j++) 
			{ 
				$open .= $c['Openee'][$j]['name'];
				if($j != $numOpen-1) $open .= ", ";
			}
			$sheet->write($i+1, 6, $open, $normalFormat[$firstInSector]);
			
			// Write Status
			$status = $c['Contact']['status_id'];
			$sheet->write($i+1, 0, $this->getSlashStatus($c), $formatStatus[$status][$firstInSector]);
			
			//} // Remove me 
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
					
			case 3: return $this->getOpenStatus($c);
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
	
	function getOpenStatus($c)
	{
		if(isset($c['Meeting']) && count($c['Meeting']) > 0) return 'Meeting';
		return 'Open';
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