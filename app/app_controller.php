<?php
/**
 * This file is application-wide controller file. You can put all
 * application-wide controller-related methods here.
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
 * @subpackage 		ppc.controllers
 * @since			CakePHP(tm) v 0.2.9
 * @version			$Revision: 4410 $
 * @modifiedby		$LastChangedBy: phpnut $
 * @lastmodified	$Date: 2007-02-02 07:31:21 -0600 (Fri, 02 Feb 2007) $
 * @license			http://www.opensource.org/licenses/mit-license.php The MIT License
 */

// We need these for the htmlLink() and formatter() functions
uses('view'.DS.'helpers'.DS.'app_helper');
//uses('../app_helper');
uses('view/helpers/html');
uses('../../app/views/helpers/format');

//uses('L10n');

/**
 * Variables, functions and actions that are accessible in every controller. All other
 * controllers should inherit this class.
 *
 * @package		ppc
 * @subpackage 	ppc.controllers
 */

class AppController extends Controller 
{
	/**
	 * An array of the names of actions that one should not need to be logged in to access.
	 * Controllers extending this one may want to set this var. It is used by the Cauth
	 * authentication component.
	 *
	 * @var array
	 */
	
	var $allow = array('validateField');
	
	/**
	 * An array of the names of actions that one should always be able to access providing
	 * one is logged in.
	 * Controllers extending this one may want to set this var. It is used by the Cauth
	 * authentication component.
	 *
	 * @var array
	 */
	
	var $allowIfLoggedIn = null;
	
	/**
	 * The components that are always available in every controller
	 * @var array
	 * @access public	 
	 */
	
	//var $components = array('Config', 'Language', 'RequestHandler', 'Tracking', 'Cauth');
	var $components = array('RequestHandler', 'Tracking', 'Cauth');
	
	/**
	 * The helpers that are always available in every controller
	 * @var array
	 * @access public
	 */
	
	var $helpers = array('Format', 'Forms', 'Ajaxs', 'Time', 'Javascript');
	
	var $uses = array('Project', 'TeamMember');
	
	/**
	 * Called before every action in every controller (if not overridden by
	 * the beforeFilter() in that controller). Used to prevent AJAX calls from
	 * including the normal debug info at the end of the view - it messes up
	 * the parsing of the result. Also used to set the $controller var in each
	 * model used by the controller.
	 * 
	 * @return void
	 * @author David Roberts
	 */
	
	function beforeFilter() 
	{
		// Set the controller in each of the models. This is frowned upon, but how else is
		// the model supposed to do things like tell if the user is logged in?
		if(isset($this->uses) && is_array($this->uses))
		{
			foreach($this->uses as $model) 
			{
				if(isset($this->$model)) $this->$model->controller = $this;
			}
		}
		
		// Don't forget any model with the same name as the controller
		$model = $this->modelClass;
		if(isset($this->$model)) $this->$model->controller = $this;
		
		// Don't include debug info in ajax calls
	    if ($this->RequestHandler->isAjax()) {
	        //$db =& ConnectionManager::getDataSource('default');
	        //$db->fullDebug = false;
			Configure::write('debug', '0');
	    }
	}
	
	/**
	 * Instantiates and returns a given model. Associates the controller with
	 * the model in the same way as beforeFilter() does
	 *
	 * @param string $name The name of the model to be instantiated
	 * @return model An instance of the model
	 * @author David Roberts
	 */
	
	function getModel($name)
	{
		loadModel($name);
		$model = new $name();
		$model->controller = $this;
		return $model;
	}
	
	/**
	 * Called before a view is rendered. Passes the page title to the view via the
	 * $pageTitle variable
	 *
	 * @return void
	 * @author David Roberts
	 */
	
	function beforeRender()
	{
		if(!$this->RequestHandler->isAjax())
		{
			// Get all the project details that we might need
			// TODO: getting the names of all the projects could become inefficient
			if($this->action != "login") 
			{
				$this->set('allProj', $this->Project->generateProjectNameList());
				$relProj = $this->getUserProjects();
				$this->set('relProj', $relProj);
			}
			
			$this->set('pageTitle', $this->pageTitle);
		}
	}
	
	/**
	 * Returns all the projects where the currently logged-in user is a member of the team
	 *
	 * @return void
	 * @author David Roberts
	 */
	
	function getUserProjects($userId = null, $ordering = null, $active = true)
	{
		if(!isset($userId)) $userId = $this->Cauth->user('id');
		if(!isset($ordering)) $ordering = 'project_status_id ASC, projects.started_on DESC';
		$extraClause = "";
		if($active) $extraClause = " AND team_members.status = 1";
		
		$projects = $this->Project->query("SELECT project_id FROM team_members INNER JOIN projects ON team_members.project_id = projects.id WHERE team_members.user_id = $userId $extraClause ORDER BY $ordering");
		return Set::extract($projects, '{n}.team_members.project_id');
		
		/*$where = '';
		if(count($projects) > 0) 
		{
			foreach($projects as $pid) 
			{
				$where .= "Project.id = '$pid' OR ";
			}
			$where .= "0";
			return $this->Project->findAll($where, null, "Project.project_status_id ASC, Project.started_on DESC");
		} else {
			return array();
		}*/
	}
		
	/**
	 * If we are not in an AJAX action, sets a message to be flashed on the next page.
	 * If we are in and AJAX action (and $notAjax is not set), sets a message to be flashed when
	 * the action's view is rendered.
	 * 
	 * If $to is set, we assume that we are not doing an AJAX flash and we redirect immediately
	 * to the URL specified once the flash has been set.
	 * 
	 * @param string $msg The text to be flashed. A full stop will be added if it's not a complete sentence
	 * @param string $to The location to redirect to (only for use in non-AJAX contexts)
	 * @param string $type The type of flash - determines which layout is used to render it
	 * @param boolean $notAjax If set, we will generate a non-AJAX flash even if we are in an AJAX action
	 * @author David Roberts
	 */
	
	function flash($msg, $to = null, $type = 'notice', $notAjax = false)
	{
		// Add a full stop on the end of the flash if the sentence isn't terminated properly
		// @todo: This probably isn't compatible with certain languages (l10n)
		$msgEnd = substr($msg, -1);
		if($msgEnd != "." && $msgEnd != "!" && $msgEnd != "?") $msg .= ".";
		
		if ($this->RequestHandler->isAjax() && !isset($to) && !$notAjax) 
		{
			$this->flashAjax($msg, $type);
		} else {
			$type = "flash_" . $type; // The template used for the flash is found in /views/layouts/$type
			$this->Session->setFlash($msg, $type);
			if(isset($to))
			{
				$this->redirect($to, null, true);
				exit;
			}
		}
	}
	
	/**
	 * Same as {@link flash} except the type is always set to 'error'
	 * 
	 * @param string $msg The text to be flashed
	 * @param string $to The location to redirect to (optional)
	 * @author David Roberts
	 */
	
	function flashError($msg, $to = null)
	{
		$this->flash($msg, $to, 'error');
	}
	
	/**
	 * Same as {@link flash} except the type is always set to 'warning'
	 * 
	 * @param string $msg The text to be flashed
	 * @param string $to The location to redirect to (optional)
	 * @author David Roberts
	 */
	
	function flashWarning($msg, $to = null)
	{
		$this->flash($msg, $to, 'warning');
	}
	
	/**
	 * Should only be called in AJAX actions. Sets a flash that will appear when
	 * the AJAX result is rendered. See views/layouts/ajax.ctp, webroot/js/flash.js
	 * and views/layouts/default.ctp for more information on how this actually
	 * works.
	 * 
	 * Note that the div that the flash is displayed in can be changed from the
	 * default one by calling Flash.setFlashDiv() (defined in flash.js) in the
	 * view for the relevant action.
	 *
	 * @param string $msg 
	 * @param string $type 
	 * @return void
	 * @author David Roberts
	 */
	
	function flashAjax($msg, $type = 'notice')
	{
		$this->set('flashMsg', $msg);
		$this->set('flashType', $type);
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
		// This is slightly naughty: we are using a helper in a controller when they are only meant for views.
		// HOWEVER: We need it to put links in flashes - to be honest, flashes are part of the view, so their
		// very existence breaks the MVC design pattern too. So don't feel too bad about it :)
		if(!isset($this->HtmlHelper)) $this->HtmlHelper = new HtmlHelper();
		
		return $this->HtmlHelper->link($title, $url, $htmlAttributes, $confirmMessage, $escapeTitle);
	}
	
	/**
	 * Returns a reference to the FormatHelper - so that we can format names, etc. properly
	 * in flashes
	 *
	 * @return void
	 * @author David Roberts
	 */
	
	function formatter()
	{
		if(!isset($this->formatter)) $this->formatter = new FormatHelper();
		
		return $this->formatter;
	}
	
	
	/**
	 * Action used to validate a certain field in a from via AJAX. Required for
	 * FormsHelper::error() to work properly
	 *
	 * @param string $model The model of the field that is being validated
	 * @param string $field The field that is being validated
	 * @author David Roberts
	 */
	
	function validateField($model, $field)
	{
		// If we're dealing with an LDF model, we do the validation via
		// the parent model as the LDF is not explicitly included in the
		// controller. However, the errors are accessed via the LDF model
		if(substr($model, -3) == "LDF")
		{
			$parentModel = substr($model, 0, -3);
			$errorModel = $this->$parentModel->$model;
			$model = $this->$parentModel;
		} else {
			$model = $this->$model;
			$errorModel = $model;
		}
		
		$oldData = $model->data;
		$model->data = $this->data;
		$model->validates();
		
		$error = '';
		if(isset($errorModel->validationErrors[$field]))
			$error = $errorModel->validationErrors[$field];
		
		//header('Content-type: application/x-json'); 
		//print '{"errorCode":"'.$error.'"}';
		//$this->set('error', $error);
		//$this->render('../elements/validationError');
		
		if(!is_array($error))
		{
			echo $error;
		} else { 
			$allLangs = $language->getAll();

			foreach($error as $key => $msg) 
			{
				if(!is_numeric($key)) echo "$msg<br />";
			}

			// Display any language-related errors with their language
			foreach($allLangs as $lang) 
			{
				if(isset($error[$lang['id']]))
				{
					if(count($allLangs) > 1) echo $html->link($lang['code'], '#', array('onclick' => 		"LDF.changeFieldLangs('".$lang['id']."');return false;")) . ": ";
					echo $error[$lang['id']] . "<br />";
				}
			}
		}
		
		exit();
	}
	
	/**
	 * Action used for in-place editing. Saves the field's value
	 *
	 * @param string $fieldName 
	 * @return void
	 * @author David Roberts
	 */
	
	function saveField($modelName, $fieldName, $id, $isForeignKey = false)
	{
		$model = $this->$modelName;
		
		$model->id = $id;
		$model->saveField($fieldName, $this->params['form']['value']);
		
		// If we're saving a foreign key, we return the name it references
		// rather than the ID itself
		if($isForeignKey)
		{
			$otherModelName = ucwords(substr($fieldName, 0, -3));
			$otherModel = $model->$otherModelName;
			$data = $otherModel->find($this->params['form']['value'], array('name'), null, -1);
			print $data[$otherModelName]['name'];
		} else {
			echo $this->params['form']['value'];
		}
		
		exit();
	}
	
	/**
	 * Adds the specified $msg to a log file with the same name as the controller
	 *
	 * @todo Decide exactly what we want to record in logs
	 * @param string $msg The message to be output to the log
	 * @return void
	 * @author David Roberts
	 */
	
	function logInfo($msg)
	{
		$this->log($msg, $this->name);
	}
	
	/**
	 * Interface function - this function should be overridden in every controller so that CAuth can
	 * check permissions for the controller's actions. We define it here so that it will throw an 
	 * error if it is not overridden.
	 *
	 * @param string $action The action in the controller that we want to test against
	 * @param array $objects An array containing the parameters for the action
	 * @param string $user The user performing the action (if null the logged-in user is used)
	 * @return boolean Whether the user is allowed to perform the action on the object
	 * @author David Roberts
	 */
	
	function userCan($action, $objects = null, $user = null)
	{
		// TODO: UNCOMMENT THIS WHEN HAVE PERMISSIONS SYSTEM UP AND RUNNING
		//trigger_error(_t("The permissions function userCan() has not been overridden in the $this->name controller. You must define this function, or alternatively use the \$allow and \$allowIfLoggedIn arrays in the controller", true), E_USER_WARNING);
		return true;
	}

}
?>