<?php

class ImportController extends AppController 
{
	// Configuration vars
	var $name = 'Import';
	var $uses = array('ContactImport', 'Contact', 'ContactImportField', 'Person', 'Note');
	var $helpers = array('Html', 'Form', 'Forms', 'Ajax', 'Customjs');
	
	function contacts($pid = null)
	{
		if(!isset($pid)) $pid = $this->data['pid'];
		if(!isset($pid)) $this->flashError('Project not specified', $this->Tracking->lastSafePage());
		
		$this->set('pid', $pid);
		
		if($this->data) 
		{
			// If we have data stored in the session, retrieve it
			if(isset($this->data['sessionKey'])) 
			{
				$this->readSession($this->data['sessionKey']);
			// Otherwise, we've come from the first page and we need to read the file and interpret the data
			} else {
				$cols = $this->data['field'];
				$f = $this->data['Contacts']['file'];

				if(!isset($f['name']) || $f['name'] == "") 
				{
					$this->flashError("You must select a file to upload, by clicking on the 'Browse...' button");
					$error = true;
				} elseif(strtolower(substr($f['name'],-3)) != "csv") {
					$this->flashError("The file you upload must be in CSV format");
					$error = true;
				} elseif($f['error'] || $f['size'] == 0) {
					$this->flashError("There was an error uploading your file");
					$error = true;
				} elseif(!isset($cols) || count($cols) < 1) {
					$this->flashError("You did not specify the columns in your file. Please click on the 'Add column' link below");
					$error = true;
				} else {
					$fp = fopen($f['tmp_name'], 'r');
					if(!feof($fp)) {
						$headings = fgetcsv($fp);
						$numCols = count($headings);
						if($numCols < count($cols)) {
							$this->flashError("You specified $numCols columns, but there are only " . count($this->data['field']) . " in your file");
						}

					}
					$this->colCodes = $this->ContactImportField->generateNameList('name');
					$this->problemValues = array();
					$this->fdata = $this->readCSVFile($fp, $cols, $this->colCodes, $this->problemValues);
					fclose($fp);
					
					// Check whether whether there are any problem values
					if(count($this->problemValues) > 0) 
					{
						// Need to save all the data in the session so that we can come back to it
						$this->key = $this->saveSession();

						$this->set('sessionKey', $this->key);
						$this->set('problemValues', $this->problemValues);
						$this->set('allowedValues', $this->getAllowedFieldValues());
						$this->set('colNames', $this->ContactImportField->generateNameList('reference', null, "validate != NULL or validate != ''"));
						$this->pageTitle = "Unknown Values";
						$this->render('contacts_problem_values');
						return;
					}
				}
			}
			
			if(!isset($error) || !$error) 
			{
				// Map the data in preparation for saving if we haven't already done so
				if(!isset($this->mapped) || !$this->mapped) {
					$map = null;
					if(isset($this->data['map'])) $map = $this->data['map'];
					$this->fdata = $this->mapFields($pid, $this->fdata, $map, $this->problemValues);
					$this->mapped = true;
				}
				
				$errors = $this->validates($this->fdata);
				
				if($errors && !isset($this->data['ignoreErrors'])) 
				{
					// Save data in session
					$this->key = $this->saveSession();
					
					$this->set('sessionKey', $this->key);
					$this->set('errors', $errors);
					$this->set('data', $this->fdata);
					$this->pageTitle = "Errors Encountered";
					$this->render('contacts_errors');
					return;
					
				} else {
					// Save the file
					$this->save($this->fdata, $pid);
				}
				
			} 
			
		} else {
			// Set up a couple of columns to the user gets an idea of what they're supposed to do
			$this->data['field'] = array('1', '2');
		}
		
		$c = $this->ContactImportField->generateNameList('reference');
		$this->set('fieldList', $c);
		$this->pageTitle = "Import Contacts";
	}
	
	
	
	
	function readCSVFile($fp, $cols, $colCodes, &$problemValues = null)
	{
		// If we want the problem values, work out what the allowed values are
		if(isset($problemValues)) 
		{
			$allowedValues = $this->getAllowedFieldValues();
			$problemValues = array();
		}
		
		$result = array();
		
		while(!feof($fp)) { // For each line
			
			$data = array();
			$line = fgetcsv($fp);
			
			for($i = 0; $i < count($cols); $i++) // For each column of the line
			{ 
				$colId = $cols[$i];
				if(isset($line[$i])){
					$fieldValue = trim($line[$i]);
				} else {
					$fieldValue = '';
				}
				
				$this->setCSVValue($data, $fieldValue, $colCodes[$colId]);
				
				// If we're collecting problem values, check this one
				if(isset($fieldValue) && $fieldValue != null &&
				   isset($problemValues) && 
				   isset($allowedValues[$colId]) &&
				  !in_array($fieldValue, $allowedValues[$colId]))
				{
					$problemValues[$colId][] = $fieldValue;
				}
			}
			
			// Get rid of any duplicate problem values we might have
			if(isset($problemValues))
			{
				//for($i = 0; $i < count($problemValues); $i++) 
				foreach($problemValues as &$value) 
				{ 
					$value = array_unique($value);
					//$problemValues[$i] = array_unique($problemValues[$i]);
				}
			}
			
			$result[] = $data;
		}
		
		// Get rid of any empty sub-models, as they will fail validation
		$this->stripEmptySubModels($result);
		
		return $result;
	}
	
	
	
	function saveSession()
	{
		
		if(!isset($this->key)) $this->key = rand();
		
		$session = array();
		
		$mapped = null;
		if(isset($this->mapped)) $mapped = $this->mapped;
		
		$session[$this->key] = array('data' => $this->fdata, 
									  'colCodes' => $this->colCodes, 
									  'problemValues' => $this->problemValues,
									  'mapped' => $mapped);
		
		$this->Session->write('contact_import', $session);
		
		return $this->key;
	}
	
	
	
	
	function readSession($key = null)
	{
		if(!isset($key)) $key = $this->key;
		
		$sessionData = $this->Session->read('contact_import');
		if(isset($sessionData[$key])) 
		{
			$this->fdata = $sessionData[$key]['data'];
			$this->colCodes = $sessionData[$key]['colCodes'];
			$this->problemValues = $sessionData[$key]['problemValues'];
			$this->mapped = $sessionData[$key]['mapped'];
		}
		
		$this->key = $key;
		
	}
	
	
	
	
	function setCSVValue(&$container, $value, $col)
	{
		// If the column has no name, ignore it
		if(!isset($col) || $col == '') return;
		
		$levels = explode(".", $col);
		
		if(count($levels) == 2) 
		{
			// Person.name => $container['Person']['name']
			$container[$levels[0]][$levels[1]] = $value;
		} elseif($levels[1] == "n") {
			// Person.n.name => $container['Person'][]['name']
			$container[$levels[0]][][$levels[2]] = $value;
		} else {
			// Person.1.name => $container['Person'][1]['name']
			$container[$levels[0]][$levels[1]][$levels[2]] = $value;
		}
	}
	
	
	
	function unsetCSVValue(&$container, $col)
	{
		// If the column has no name, ignore it
		if(!isset($col) || $col == '') return;
		
		$levels = explode(".", $col);
		
		if(count($levels) == 2) 
		{
			// Person.name => $container['Person']['name']
			unset($container[$levels[0]][$levels[1]]);
		} else {
			// Person.1.name => $container['Person'][1]['name']
			unset($container[$levels[0]][$levels[1]][$levels[2]]);
		}
	}
	
	
	/**
	 * NOTE: We can't read anything of the form Person.n.name this way, because we don't know the index
	 *
	 * @param string $container 
	 * @param string $col 
	 * @return void
	 * @author David Roberts
	 */
	
	function getCSVValue($container, $col)
	{
		// If the column has no name, ignore it
		if(!isset($col) || $col == '') return;
		
		$levels = explode(".", $col);
		
		if(count($levels) == 2) 
		{
			// Person.name => $container['Person']['name']
			if(isset($container[$levels[0]]) && isset($container[$levels[0]][$levels[1]])) 
				return $container[$levels[0]][$levels[1]];
		} else {
			// Person.1.name => $container['Person'][1]['name']
			if(isset($container[$levels[0]]) && isset($container[$levels[0]][$levels[1]]) && isset($container[$levels[0]][$levels[1]][$levels[2]])) 
				return $container[$levels[0]][$levels[1]][$levels[2]];
		}
	}
	
	
	
	
	function getAllowedFieldValues()
	{
		$problemCols = $this->ContactImportField->generateNameList('validate', null, "validate != NULL or validate != ''");
		
		$result = array();
		
		if(count($problemCols) > 0) {
			foreach($problemCols as $colId => $colName) 
			{
				$levels = explode(".", $colName);
				$model = $this->getModel($levels[0]);
					
				$result[$colId] = $model->generateNameList($levels[1]);
			}
		}
		
		return $result;
	}
	
	
	
	/**
	 * When there is a field that references another table (e.g. sector_id), replaces the textual
	 * name in that table with the appropriate id.
	 *
	 * @param string $data 
	 * @param string $map 
	 * @param string $problemFields 
	 * @return void
	 * @author David Roberts
	 */
	
	function mapFields($pid, $data, $problemMap = null, $problemFields = null)
	{
		// Get the column codes for the fields that need to be mapped
		$colCodes = $this->ContactImportField->generateNameList('name', null, "validate != NULL or validate != ''");
		$allowedValues = $this->getAllowedFieldValues();
		
		$rowNum = 1;
		foreach($data as &$row) // Loop through the data
		{
			foreach($colCodes as $colId => $colCode) // Loop through the fields that need to be mapped
			{
				$value = $this->getCSVValue($row, $colCode);
				
				if(isset($value))
				{
					$code = array_search($value, $allowedValues[$colId]);
					if($code !== false) // If the field contains an allowed value map it
					{
						$this->setCSVValue($row, $code, $colCode);
					} elseif(isset($problemMap) && 
							 isset($problemFields) && 
							 isset($problemFields[$colId]) &&
							 array_search($value, $problemFields[$colId]) !== false) { // Otherwise, take the value from the problem map
						$mapCode = array_search($value, $problemFields[$colId]);
						$this->setCSVValue($row, $problemMap[$colId][$mapCode], $colCode);
					} else {
						// If we get here, the value is an empty string, and we need to unset
						// it so that the default value will be inserted in validates()
						$this->unsetCSVValue($row, $colCode);
					}
				}
			}
			
			$row['Contact']['project_id'] = $pid;
			$rowNum++;
		}
		
		return $data;
	}
	
	
	
	function validates($data)
	{
		$errors = array();
		for($i = 0; $i < count($data); $i++) 
		{ 
			$row = $data[$i];
			
			$this->Contact->create($row);
			$this->Contact->fillEmptyFields();
			if(!$this->Contact->validates()) 
			{
				$errors[$i]['Contact'] = $this->Contact->validationErrors;
			}
			
			if(isset($row['Person']) && count($row['Person']) > 0) 
			{
				foreach($row['Person'] as $pos => $person) 
				{
					$this->Person->create($person);
					$this->Person->fillEmptyFields();
					if(!$this->Person->validates()) 
					{
						$errors[$i]['Person'][$pos] = $this->Person->validationErrors;
					}
				}
			}
			
			if(isset($row['Note']) && count($row['Person']) > 0) 
			{
				foreach($row['Note'] as $pos => $note)  
				{
					$this->Note->create($note);
					$this->Note->fillEmptyFields();
					if(!$this->Note->validates()) 
					{
						$errors[$i]['Note'][$pos] = $this->Note->validationErrors;
					}
				}
			}
		}
		
		return $errors;
	}
	
	
	
	function save($data, $pid)
	{
		// Create a new contact import record
		$this->ContactImport->create();
		$this->ContactImport->save();
		
		$rowsAdded = 0;
		for($i = 0; $i < count($data); $i++) 
		{
			$row = $data[$i];
			
			$this->Contact->create($row);
			$this->Contact->set('contact_import_id', $this->ContactImport->id);
			$this->Contact->fillEmptyFields();
			$success = $this->Contact->save();
			
			if($success) 
			{
				if(isset($row['Person']) && count($row['Person']) > 0) 
				{
					foreach($row['Person'] as $pos => $person) 
					{
						$this->Person->create($person);
						$this->Person->set('contact_id', $this->Contact->id);
						$this->Person->fillEmptyFields();
						$success = $success && $this->Person->save();
					}
				}

				if(isset($row['Note']) && count($row['Note']) > 0) 
				{
					foreach($row['Note'] as $pos => $note)  
					{
						$this->Note->create($note);
						$this->Note->set('contact_id', $this->Contact->id);
						$this->Note->fillEmptyFields();
						$success = $success && $this->Note->save(); 
					}
				}
				
				// If we had any problems, delete everything
				if(!$success)
				{
					$this->Contact->del();
				} else {
					$rowsAdded++;
				}
			}
		}
		
		$undoLink = $this->htmlLink('Undo', array('controller' => 'import', 'action' => 'undo'));
		$this->flash("Successfully imported $rowsAdded contacts. $undoLink", array('controller' => 'contacts', 'action' => "listAll/$pid"));
	}
	
	
	/**
	 * Returns true if all the elements of an array are null
	 *
	 * @param string $array 
	 * @return void
	 * @author David Roberts
	 */
	
	function arrayEmpty($array)
	{
		if(count($array) > 0) 
		{
			foreach($array as $value) 
			{
				if($value != NULL && $value != '') return false;
			}
		}
		
		return true;
	}
	
	
	/**
	 * Unsets and people or notes that are empty, as they will fail validation
	 *
	 * @param string $data 
	 * @return void
	 * @author David Roberts
	 */
	
	function stripEmptySubModels(&$data)
	{
		if(count($data) > 0) 
		{
			foreach($data as &$row) 
			{
				if(isset($row['Person'])) 
				{
					foreach($row['Person'] as $key => $person) 
					{
						if($this->arrayEmpty($person)) unset($row['Person'][$key]);
					}
				}
				
				if(isset($row['Note'])) 
				{
					foreach($row['Note'] as $key => $note) 
					{
						if($this->arrayEmpty($note)) unset($row['Note'][$key]);
					}
				}
			}
		}
	}
	
	
	/**
	 * Removes all the contacts added by the user's last import
	 *
	 * @return void
	 * @author David Roberts
	 */
	
	function undo($importId = null)
	{
		if(!isset($importId)) 
		{
			// Find out the id of the user's last import
			$importId = $this->ContactImport->find('created_by = ' . $this->Cauth->user('id'), 'id', 'created DESC', 1);
			if(isset($importId)) $importId = $importId['ContactImport']['id'];
		}
		
		if(!$importId)
		{
			$this->flashError("No import was found to undo", $this->Tracking->lastSafePage());
		} else {
			$this->Contact->recursive = 0;
			$contacts = $this->Contact->findAll('contact_import_id = ' . $importId, array('Contact.id'));
			
			$numContacts = count($contacts);
			
			if($numContacts > 0) 
			{
				foreach($contacts as $contact) 
				{
					$this->Contact->del($contact['Contact']['id']);
				}
			}
			
			$this->flash("$numContacts contacts were removed", $this->Tracking->lastSafePage());
		}
	}
	

}
?>