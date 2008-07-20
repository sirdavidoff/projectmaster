<?php
/* SVN FILE: $Id: app_model.php 4410 2007-02-02 13:31:21Z phpnut $ */
/**
 * This file is application-wide model file. You can put all
 * application-wide model-related methods here.
 *
 * CakePHP(tm) :  Rapid Development Framework <http://www.cakephp.org/>
 * Copyright 2005-2007, Cake Software Foundation, Inc.
 *								1785 E. Sahara Avenue, Suite 490-204
 *								Las Vegas, Nevada 89104
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @filesource
 * @copyright		Copyright 2005-2007, Cake Software Foundation, Inc.
 * @link				http://www.cakefoundation.org/projects/info/cakephp CakePHP(tm) Project
 * @package			ppc
 * @subpackage		ppc.models
 * @since			CakePHP(tm) v 0.2.9
 * @version			$Revision: 4410 $
 * @modifiedby		$LastChangedBy: phpnut $
 * @lastmodified	$Date: 2007-02-02 07:31:21 -0600 (Fri, 02 Feb 2007) $
 * @license			http://www.opensource.org/licenses/mit-license.php The MIT License
 */

// We need these for the htmlLink() function
//uses('../app_helper');
uses('view/helpers/html');

uses('sanitize');

/**
 * Variables and functions that are available in every model. All other models
 * should inherit from this class.
 *
 * @package		ppc
 * @subpackage  ppc.models
 */

class AppModel extends Model{
	
	var $actsAs = array('Bindable', 'ExtendAssociations');
	
	/**
	 * Sometimes a validation function might want to generate a more customised error message (e.g. 
	 * put a link in it). Cake doesn't provide a native mechanism for this, but if the validation
	 * function sets $advancedErrorMessages['field'] with its new error, then this will be copied
	 * into the validationErrors array once the validation is complete. See also {@link invalidFields}
	 *
	 * @var string
	 */
	
	var $advancedErrorMessages = null;
	
	/**
	 * Used to define sanitization actions for fields of the model. Before the model data is validated,
	 * the actions will be applied to the fields.
	 * e.g. $this->sanitize = array('fieldname' => array('action1', 'action2'))
	 * For a list of valid actions, look at the sanitize() function.
	 *
	 * @var string
	 */
	
	var $sanitize = null;
	
	/**
	 * Used within afterSave() to prevent infinite loops
	 *
	 * @var string
	 */
	
	var $inAfterSave = false;
	
	/**
	 * Indicates whether the model has language-dependent fields in another table. If set to true
	 * the table will be associated an managed automatically (providing it follows the naming
	 * conventions)
	 *
	 * @var string
	 */
	
	var $hasLDF = false;
	
	/**
	 * The name of the associated LDF table. Can be specified directly if the table name is non-
	 * standard, otherwise is set automatically in __construct()
	 *
	 * @var string
	 */
	
	var $LDFName = null;
	
	/**
	 * 	Indicates whether the model contains the language-dependent fields of another table. If 
	 * set to true the table will be associated and managed automatically (providing it follows 
	 * the naming conventions)
	 *
	 * @var string
	 */
	
	var $isLDF = false;
	
	/**
	 * The name of the table associated with this LDF table. Can be specified directly if the 
	 * table name is non-standard, otherwise is set automatically in __construct()
	 *
	 * @var string
	 */
	
	var $LDFParent = null;
	
	/**
	 * Constructor. Checks the $hasLDF variable and if it is set to true adjusts $hasMany to
	 * include the associated model. If the $isLDF variable is set adjusts $belongsTo to
	 * include the associated model.
	 *
	 * @param string $id 
	 * @param string $table 
	 * @param string $ds 
	 * @return void
	 * @author David Roberts
	 */
	
	function __construct($id = false, $table = null, $ds = null)
	{
		if($this->hasLDF) 
		{
			if(!isset($this->LDFName)) $this->LDFName = $this->name . 'LDF'; 

			if(!isset($this->hasMany)) $this->hasMany = array();
			$this->hasMany[$this->LDFName] = array('className' => $this->LDFName,
												   'foreignKey' => low($this->name) . '_id',
												   'dependent' => true,
												   'exclusive' => true);
		} elseif($this->isLDF) {
			if(!isset($this->LDFParent)) $this->LDFParent = substr($this->name, 0, -3);
			
			if(!isset($this->belongsTo)) $this->belongsTo = array();
			$this->belongsTo[$this->LDFParent] = array('className' => $this->LDFParent,
													   'foreignKey' => low($this->LDFParent) . '_id');
		}
		
		parent::__construct($id, $table, $ds);
	}

	/**
	 * Validation function. Checks whether a field's value is the same as that of another field.
	 * NB: At the moment the two fields must be in the same model.
	 *
	 * @param string $value The value of the field we are checking
	 * @param string $params $params['field'] must contain the name of the field we are checking against
	 * @return boolean Whether the two fields' values are equal
	 * @author David Roberts
	 */
	
	function equalToField($value, $params = array()) 
	{
		if($params['field'] == null) return true;
		if(is_array($value)) $value = array_pop($value);
		
		if(isset($this->data[$this->name]))
			$data = $this->data[$this->name];
		else	
			$data = $this->data;
		
		if(!isset($data[$params['field']])) return false;
		
		return $value == $data[$params['field']];
	}
	
	/**
	 * Validation function. Checks whether a field's value is not the same as that of another field.
	 * See also {@link equalToField}
	 * NB: At the moment the two fields must be in the same model
	 *
	 * @param string $value The value of the field we are checking
	 * @param string $params $params['field'] must contain the name of the field we are checking against
	 * @return boolean Whether the two fields' values are equal
	 * @author David Roberts
	 */
	
	function notEqualToField($value, $params = array()) 
	{
		if(is_array($value)) $value = array_pop($value);
		return !$this->equalToField($value, $params);
	}
	
	/**
	 * Validation function. Checks whether a field's value is not the same in any other records 
	 * (of the same type) in the DB.
	 *
	 * @param string $value The value of the field we are checking
	 * @param string $params $params['field'] must contain the name of the field we are checking against
	 * @return boolean Whether there is another record where the given field has the same value
	 * @author David Roberts
	 */
	
	function isUniqueField($value, &$params = array())
	{
		if(!$params['field']) trigger_error("No field specified for AppModel::isUniqueField() call", E_USER_WARNING);
		$field = $params['field'];
		if(is_array($value)) $value = array_pop($value);
		
		if(isset($this->data[$this->name]))
			$data = $this->data[$this->name];
		else	
			$data = $this->data;

		// If the record is already in the DB and the value is the same as the DB one, don't complain
		if(isset($data['id']))
		{
			$result = $this->find($data['id'], array($field));
			if($result[$this->name][$field] == $value) {
				return true;
			}
		}
		
		$results = $this->findAll(array($params['field'] => $value), array('id'), null, null, null, -1);
		
		if(!$results) return true;
		
		$ids = Set::extract($results, '{n}.'.$this->name.'.id');
		return $ids && count($ids) == 1 && $ids[0] == $this->id;
	}
	
	function isTime($value, &$params = array())
	{
		if(is_array($value)) $value = array_pop($value);
		return $value == null || preg_match('/[0-2][0-9]:[0-9][0-9]/', $value) && !preg_match('/2[4-9]:/', $value);
	}
	
	/**
	 * Validation function. Verifies that the $value param is in the format dd/mm/yy
	 *
	 * @param string $value 
	 * @param string $params 
	 * @return void
	 * @author David Roberts
	 */
	
	function isSlashDate($value, &$params = array())
	{
		if(is_array($value)) $value = array_pop($value);
		
		$d = substr($value, 0, 2);
		$m = substr($value, 3, 2);
		$y = substr($value, 6, 2);
		return $value == null || 
			   (is_numeric($d) && is_numeric($m) && is_numeric($y) && checkdate($m, $d, $y));
	}
	
	/**
	 * We override Model::invalidFields - does the same, but updates $validationErrors with
	 * the contents of ${@link advancedErrorMessages}. Also calls invalidFields() on any
	 * associated LDF models
	 *
	 * @return void
	 * @author David Roberts
	 */
	
	function invalidFields()
	{
		parent::invalidFields();
		
		// Validate any LDF fields too
		if($this->hasLDF)
		{
			$LDFModel = $this->LDFName;
			$langs = $this->controller->Language->getAll();
			
			foreach($langs as $lang)
			{
				if(isset($this->data[$LDFModel]) && isset($this->data[$LDFModel][$lang['id']]))
				{
					$this->$LDFModel->data = $this->data[$LDFModel][$lang['id']];
				}
				$this->$LDFModel->data['language_id'] = $lang['id'];
				$this->$LDFModel->allData = $this->data[$LDFModel];
				$this->$LDFModel->invalidFields();
				$this->data[$LDFModel][$lang['id']] = $this->$LDFModel->data;
			}
		}
		
		if(isset($this->advancedErrorMessages))
		{
			$this->validationErrors = am($this->validationErrors, $this->advancedErrorMessages);
		}
		
		return $this->validationErrors;
	}
	
	/**
	 * Called before validation is performed. We sanitize any fields specified in $this->sanitize
	 * by calling the sanitize() function.
	 *
	 * @return void
	 * @author David Roberts
	 */
	
	function beforeValidate()
	{
		$this->sanitizeFields();
		return true;
	}
	
	/**
	 * Looks in the model's sanitize array for actions to be performed on the data and performs those actions
	 * by calling sanitize() on each one. If the model $hasLDF, also sanitizes any LDF data
	 *
	 * @return void
	 * @author David Roberts
	 */
	
	function sanitizeFields()
	{
		if(isset($this->sanitize) && isset($this->data[$this->name]))
		{
			foreach($this->data[$this->name] as $field => $value)
			{
				if(isset($this->sanitize[$field]))
				{
					if(!is_array($this->sanitize[$field]))
					{
						$this->data[$this->name][$field] = $this->sanitize($this->data[$this->name][$field], $this->sanitize[$field]);
					} else {
						foreach($this->sanitize[$field] as $action)
						{
							$this->data[$this->name][$field] = $this->sanitize($this->data[$this->name][$field], $action);
						}
					}
				}
			}
		}
		
	}
	
	/**
	 * Cleans up strings - applies the specified action to the value and returns the result. 
	 * For a list of valid actions, look inside the code for this function
	 *
	 * @param string $value The string to be processed
	 * @param string $action The action to be performed on the string
	 * @return string The processed string
	 * @author David Roberts
	 */
	function sanitize($value, $action)
	{
		if(is_array($action))
		{
			$options = $action;
			$action = $action['action'];
		}
		
		switch($action)
		{
			case 'trim': 				return trim($value);
			case 'escape': 				return Sanitize::escape($value);
			case 'striphtml': 			return Sanitize::html($value, true);
			case 'escapehtml': 			return Sanitize::html($value);
			case 'stripall': 			return Sanitize::stripAll($value);
			case 'stripimages': 		return Sanitize::stripImages($value);
			case 'stripscripts':		return Sanitize::stripScripts($value);
			case 'stripextraws':		return Sanitize::stripWhitespace($value);
			case 'stripdefaultvalue':	return $this->stripDefaultValue($value, $options);
		}
		
		return $action;
	}
	
	/**
	 * This should really be put in the Sanitize class
	 *
	 * @param string $value 
	 * @param string $options 
	 * @return void
	 * @author David Roberts
	 */
	
	function stripDefaultValue($value, $options)
	{
		if($value == $options['default'])
		{
			return null;
		} else {
			return $value;
		}
	}

	
	/**
	 * Generates the HTML code for a link, just like in HtmlHelper::link()
	 *
	 * @param string $title The text of the link
	 * @param string $url The URL to link to
	 * @param string $htmlAttributes Any extra attributes for the link
	 * @param string $confirmMessage A message to pop up if confirmation is needed before following the link
	 * @param string $escapeTitle Whether to escape the title or not
	 * @return string The HTML code for the link
	 * @author David Roberts
	 */
	
	function htmlLink($title, $url = null, $htmlAttributes = array(), $confirmMessage = false, $escapeTitle = true)
	{
		// This is slightly naughty: we are using a helper in a model when they are only meant for views.
		// HOWEVER: We need it to put links in validation errors, so don't feel too bad about it :)
		if(!isset($this->HtmlHelper)) $this->HtmlHelper = new HtmlHelper();
		
		return $this->HtmlHelper->link($title, $url, $htmlAttributes, $confirmMessage, $escapeTitle);
	}
	
	/**
	 * Same as Model::find() except if you pass a number as the conditions, will automatically
	 * assume that that number is the ID
	 *
	 * @param string $conditions 
	 * @param string $fields 
	 * @param string $order 
	 * @param string $recursive 
	 * @return void
	 * @author David Roberts
	 */
	
	function find($conditions = null, $fields = null, $order = null, $recursive = null)
	{
		if(is_numeric($conditions)) $conditions = array($this->escapeField() => $conditions);
		
		return parent::find($conditions, $fields, $order, $recursive);

	}
	

/**
 * Automatically updates the created_by and updated_by fields after a save if necessary
 *
 * @param string $created 
 * @return void
 * @author David Roberts
 */
	
	function afterSave($created) 
	{
		if(!$this->inAfterSave)
		{
			$this->inAfterSave = true;
			
			$userId = $this->controller->Cauth->user('id');

			if($userId)
			{
				if($created && $this->hasField('created_by'))
				{
					$this->saveField('created_by', $userId);
				}
				
				if($this->hasField('updated_by'))
				{
					$this->saveField('updated_by', $userId);
				}
			}
			
			$this->inAfterSave = false;
		}
	
	}
	
	
	function generateNameList($col = 'name', $orderBy = 'id ASC', $conditions = null)
	{
		$data = $this->findAll($conditions, array('id', $col), $orderBy, 0);
		if($data)
			return array_combine(Set::extract($data, "{n}.".$this->name.".id"), Set::extract($data, "{n}.".$this->name.".".$col));
		else
			return array();
	}
	
}
?>