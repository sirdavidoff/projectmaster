<?php
uses('controller/components/auth');
/**
 * User login and authentication.
 * 
 * @package ppc
 * @subpackage ppc.components
 * @author David Roberts
 */

/**
 * Class to handle user login and authentication.
 * 
 * Most of the functionality comes from Cake's Auth class with some exceptions:
 * 
 * 1) You can add an array to your controller called $allow - any actions in this array can be
 *    accessed without logging in
 * BFS: I've removed the above; it seems more obvious to just have a case that returns true for these actions
 * 2) You can set the type of authorization to a new type: 'this' ($this->authorize = 'this'). 
 *    If you do this, the authorization is determined by the function userIsAuthorized(), 
 *    defined in this component (override it if you want). By default, userIsAuthorized will
 *    call a function called userCan() in the controller, so make sure you define it
 *    (see app_controller.php for more information). When using the current userIsAuthorized,
 *    you can also add an array to your controller called $allowIfLoggedIn - actions in this 
 *    array can always be accessed provided the user is logged in
 *    BFS, again, allowIfLoggedIn will now not function. You can explicitly handle it in userCan though
 * 3) You can specify the action that a logged-in user will be redirected to if they are not 
 *    authorized to view a page by setting $authErrorAction. If this is not set, the user will 
 *    be redirected back to the last safe page they were on (using the Tracker component, so it must
 *    be included in app_controller)
 * 4) You can specify a field containing the datetime of last login, and it will automatically
 *    be updated (see $fields)
 * 5) You can set a message to flash if the login is successful ($loginSuccess)
 * 6) You can set a message to flash if the user is taken to the login page because they must
 *    log in to see the requested page ($loginRequired)
 * 7) You can store data from models other than the $userModel in the session (see
 *    $extraSessionData)
 * 8) You can tell Cauth to check for cookies and javascript on login and refuse login if they
 *    are not enabled
 * 
 * This class also fixes some bugs in Auth.
 *
 * @package ppc
 * @subpackage ppc.components
 * @author David Roberts
 */

class CauthComponent extends AuthComponent 
{
	var $components = array('Session', 'RequestHandler');

	/**
	 * The name of the action that lets users logout. Use the format 'controller/action'.
	 * 
	 * Can be defined here so that Cauth won't prevent you from accessing it
	 * (alternatively you could put it in the controller's $allow array mentioned above)
	 *
	 * @var string
	 */
	
	var $logoutAction = null;
	
	/**
	 * The action that the user should be redirected to if they are not authorized to view the page.
 	 * If this is not set, the user will be redirected back to the last page they were on
	 *
	 * @var string
	 */
	
	var $authErrorAction = null;
	
	/**
 	 * Allows you to specify non-default login name and password fields used in $userModel.
 	 * If a last_login key is specified, will auto-update this field at login
 	 * 
	 * @var string
	 */
	
	var $fields = array('username' => 'username', 'password' => 'password', 'last_login' => '');
	
	/**
	 * A message to flash when the login is successful. If it is null, nothing will be flashed
	 *
	 * @var string
	 */
	
	var $loginSuccess = null;
	
	/**
	 * A message to flash when a user logs in for the first time
	 *
	 * @var string
	 */
	
	var $firstLogin = null;
	
	/**
	 * A message to flash when redirecting to the login page because the user must be logged in
	 * to access the action. If it is null, nothing will be flashed
	 *
	 * @var string
	 */
	
	var $loginRequired = null;
	
	/**
	 * Any fields from models other than $userModel that should be stored in the session.
	 * 
	 * It is assumed that the table of any model specified uses the same id as $userModel
	 * e.g. array('People' => array('first_name', 'last_name')). You can then call
	 * or user('first_name', 'People') or just user('first_name') to get the value
	 *
	 * @var string
	 */
	
	var $extraSessionData = null;
	
	/**
	 * Determines whether Cauth requires the user to have cookies enabled. Possible options are null
	 * (don't check) or 'onLogin' (check on login).
	 *
	 * @var string
	 */
	
	var $checkCookies = null;
	
	/**
	 * Message to flash on login if $checkCookies is set to 'onLogin' and the user does not
	 * have cookies enabled
	 *
	 * @var string
	 */
	
	var $cookiesRequired = null;
	
	/**
	 * Determines whether Cauth requires the user to have Javascript enabled. Possible options are null
	 * (don't check) or 'onLogin' (check on login). Note that for this to work, you must create your
	 * login forms using FormsHelper::create() - special code is inserted that allows Cauth to check
	 * whether Javascript is enabled.
	 *
	 * @var string
	 */
	
	var $checkJs = null;
	
	/**
	 * Message to flash on login if $checkJs is set to 'onLogin' and the user does not
	 * have Javascript enabled
	 *
	 * @var string
	 */
	
	var $jsRequired = null;
	
	/**
	 * All the configuration variables are set here.
	 *
	 * @todo Perhaps we should get rid of this function and do it in AppController instead?
	 * @todo With the way permissions are being done, a lot of this stuff is going into the controllers.
	 * @todo Possibly it might end up being in AppController though, since everything uses it
	 * @param string $controller 
	 * @return void
	 * @author David Roberts
	 */
	
	function initialize(&$controller)
	{
		parent::initialize($controller);
		
		$this->userModel = 'User';
		$this->fields = array('username' => 'username', 'password' => 'passwd', 'last_login' => 'last_login');
		//$this->extraSessionData = array('Person' => array('first_name', 'last_name'));
		$this->authorize = 'this';
		$this->checkCookies = 'onLogin';
		$this->checkJs = 'onLogin';
		
		// Actions
		$this->loginAction = 'users/login';
		$this->logoutAction = 'users/logout';
		$this->loginRedirect = 'users/front';
		
		// Flash messages. The _t() function is a wrapper for cake's __() that will always work in views
		$this->loginRequired = "You must log in to view this page.";
		$this->loginSuccess = "You have been successfully logged in.";
		$this->firstLogin = "For security reasons, please change your password.";
		$this->loginError = "Login failed. Please check that you don't have your caps lock key on.";
		$this->authError = "You are not authorised to perform this action.";
		$this->cookiesRequired = "You do not have cookies enabled in your browser. Please enable cookies, refresh this page then try again.";
		$this->jsRequired = "You do not have javascript enabled in your browser. Please enable javascript, refresh this page then try again.";
		$this->userInactive = "Your account has been deactivated. Please contact an administrator for more information.";
	}
	
	
	/**
	 * Called when the component is initialised, which with proper usage is before any action.
	 * Performs login if we are in the $loginAction, otherwise performs authorization.
	 *
	 * @param string $controller The controller of the action we are in
	 * @return void
	 * @author David Roberts
	 */
	
	function startup(&$controller) {
		if (low($controller->name) == 'app' || (low($controller->name) == 'tests' && Configure::read() > 0)) {
			return;
		}

		if (!$this->__setDefaults()) {
			return false;
		}
		
		// Make the controller available to other cauth methods
		$this->controller =& $controller;
		// Make this component available in views
		$this->controller->set('cauth', $this);
		// Make the component available in helpers
		$this->controller->params['cauth'] = $this;

		// Get the url of the action that we're currently in
		if (!isset($controller->params['url']['url'])) {
			$url = '';
		} else {
			$url = $controller->params['url']['url'];
		}

		// Check to see whether we are in the login action
		if ($this->_normalizeURL($this->loginAction) == $this->_normalizeURL($url)) {
			if (empty($controller->data) || !isset($controller->data[$this->userModel])) {
				$this->prepareForLogin();
				return false;
			}
			
			// Only hash the passwords if we're in the login action
			$this->data = $controller->data = $this->hashPasswords($controller->data);

			if ($this->login($this->data) && $this->autoRedirect) {
				if(!$this->cookiesOK())
				{
					$this->logout();
					$controller->flashError($this->cookiesRequired);
				} elseif(!$this->javascriptOK()) {
					$this->logout();
					$controller->flashError($this->jsRequired);
				} elseif(!$this->user('is_active')) {
					$this->logout();
					$controller->flashError($this->userInactive);
				} else {
					if(!$this->user('last_login'))
					{
						$this->setLastLogin();
						if(isset($this->firstLogin)) $controller->flash($this->firstLogin);
						$controller->redirect(array('controller' => 'users', 'action' => 'changePassword'), null, true);
					} else {
						$this->setLastLogin();
						if(isset($this->loginSuccess)) $controller->flash($this->loginSuccess);
						$controller->redirect($this->redirect(), null, true);
					}
					return true;
				}
			} else {
				$controller->flashError($this->loginError);
			}
			
			// Clear the password field for security
			unset($controller->data[$this->userModel][$this->fields['password']]);
			
			// Always allow access to the login function
			return false;
		}	
		
		// Must be logged in for everything but the login page
		if (!$this->user())
		{
			$this->mustBeLoggedIn($controller);
			return false;
		}
		
		//bfs-----------------------------------------------------------------------------
		//From here on down is where I have commented out a load of auth stuff and replaced it with
		//our nascent permissions system. I've left it in for now to show how the new system replaces
		//various elements of the old.
		

		// if (!$this->isLoginRequired($controller)) {
		// 			return false;
		// 		}
		
		// We're in an action where we need to check authorization, so make sure that we're logged in
		//bfs Again, we don't need to check this. Not sure what to do with the ajax stuff here?
		// if (!$this->user()) {
		// 			if (!$this->RequestHandler->isAjax()) {
		// 				$this->mustBeLoggedIn($controller);
		// 				return false;
		// 			} elseif (!empty($this->ajaxLogin)) {
		// 				$controller->viewPath = 'elements';
		// 				$controller->render($this->ajaxLogin, 'ajax');
		// 				exit();
		// 			}
		// 		}

		// We're logged in, so do the authorization
		// I haven't touched this part of the code, so it is a direct copy from Auth
		if($this->authorize) {
			extract($this->__authType());
			if(in_array($type, array('crud', 'actions'))) {
				if(isset($controller->Acl)) {
					$this->Acl =& $controller->Acl;
					if ($this->isAuthorized($type)) {
						return true;
					}
				} else {
					trigger_error(__('Could not find AclComponent. Please include Acl in Controller::$components.', true), E_USER_WARNING);
				}
			} else if($type == 'model') {
				if(!isset($object)) {
					if (isset($controller->{$controller->modelClass}) && is_object($controller->{$controller->modelClass})) {
						$object = $controller->modelClass;
					} elseif (!empty($controller->uses) && isset($controller->{$controller->uses[0]}) && is_object($controller->{$controller->uses[0]})) {
						$object = $controller->uses[0];
					} else {
						$object = $this->objectModel;
					}
				}
				if ($this->isAuthorized($type, null, $object)) {
					return true;
				}
			} else if($type == 'controller'){
				if($controller->isAuthorized()) {
					return true;
				}
			} else if($type == 'this'){
				if($this->userIsAuthorized()) {
					return true;
				}
			}
			
		
			// Authorization failed
			$controller->flashError($this->authError);
			if(isset($this->authErrorAction)) {
				$controller->redirect($controller->referer(), null, true);
			} else {
				$controller->redirect($controller->Tracking->lastSafePage(), null, true);
			}
			return false;
		} else {
			return true;
		}
		
	}
	
	
	/**
	 * Checks to see whether we need to login to view the current action
	 *
	 * @param string $controller The controller of the action we are in
	 * @return boolean Whether login is required
	 * @author David Roberts
	 */
	
	function isLoginRequired($controller)
	{
		// We don't need to log in if either:
		// 1) The action is specified in this component's $allowedActions
		// 2) The action is specified in the controller's $allow
		/*if ($this->allowedActions == array('*') || in_array($controller->action, $this->allowedActions) ||
			(isset($controller->allow) && ($controller->allow == array('*') || in_array($controller->action, $controller->allow))) ) 
		{
			return false;
		}*/
		
		// We have to log in to do anything
		return true;
	}	
	
	
	/**
	 * Called if the user must be logged in to proceed any further.
	 * 
	 * If the user is not logged in, stores the current action in the
	 * session (so we can return to it later) and redirects to the login page
	 *
	 * @param string $controller The controller of the action we are currently in
	 * @return void
	 * @author David Roberts
	 */
	
	function mustBeLoggedIn($controller = null)
	{
		if(!isset($controller)) $controller = $this->controller;
		
		// Get the url of the action that we're currently in
		if (!isset($controller->params['url']['url'])) {
			$url = '';
		} else {
			$url = $controller->params['url']['url'];
		}
		
		$this->Session->write('Auth.redirect', $url);
		if(isset($this->loginRequired)) {
			$controller->flashError($this->loginRequired);
		}
		$controller->redirect($this->_normalizeURL($this->loginAction), null, true);
	}
	
	/**
	 * Prepares for the login of the user. If checkCookies is set to 'onLogin', sets a cookie
	 * which can be tested for once the user logs in to check whether cookies are enabled.
	 * Note that this function needs to be called in any action that may generate a login form.
	 * It is called automatically in the login function.
	 *
	 * @return void
	 * @author David Roberts
	 */
	
	function prepareForLogin()
	{
		if($this->checkCookies == 'onLogin')
		{
			$this->Session->write('Auth.cookieTest', "testing cookies enabled");
		}
	}
	
	/**
	 * Checks whether cookies are enabled, if checkCookies has been set. Otherwise, always
	 * returns true.
	 * 
	 * Note that the cookie check will fail if you don't call Cauth::prepareForLogin() in any action
	 * that contains a login form in its view. prepareForLogin() is automatically called in the login
	 * action, however.
	 *
	 * @return boolean Whether the cookies have no problems
	 * @author David Roberts
	 */
	
	function cookiesOK()
	{
		if($this->checkCookies != null)
		{
			return $this->Session->read('Auth.cookieTest') != null;
		}
		
		return true;
	}
	
	/**
	 * Checks whether javascript is enabled, if checkJs has been set. Otherwise, always
	 * returns true.
	 * 
	 * Note that the javascript check will fail unless you use Forms::create() to create
	 * the login form.
	 *
	 * @return boolean Whether there is no problem with javascript
	 * @author David Roberts
	 */
	
	function javascriptOK()
	{
		if($this->checkJs != null)
		{
			return $this->controller->data['jsCheck'];
		}
		
		return true;
	}
	
	/**
	 * Attempts to log the user in.
	 * 
	 * Worryingly, Auth::login() will succeed if either the username or password is blank.
	 * We make the login fail in this case.
	 * We also load any extra session data that there may be and update the last_login
	 * field if the login is successful
	 *
	 * @param string $data The data to use to try to log in the user. If not set, will use $this->data
	 * @return void
	 * @author David Roberts
	 */
	
	function login($data = null) 
	{
		$this->__setDefaults();
		$this->_loggedIn = false;

		if (empty($data)) {
			$data = $this->data;
		}
		
		if(empty($data[$this->userModel][$this->fields['username']]) ||
		   empty($data[$this->userModel][$this->fields['password']]))
		{
			return $this->_loggedIn;
		}
		
		$success = parent::login($data);		
		
		if($success)
		{
			$this->loadExtraSessionData();
		}

		return $success;
	}
	
	/**
	 * Loads data from other models (apart from the default user model) into the session. 
	 * The other models are defined by setting $extraSessionData
	 *
	 * @return void
	 * @author David Roberts
	 */
	
	function loadExtraSessionData()
	{
		$id = $this->user('id');
		if(!is_array($this->extraSessionData) || !$id) return;
		
		foreach ($this->extraSessionData as $modelName => $fields) 
		{
			$model =& $this->getModel($modelName);
			$data = $model->find(array($model->escapeField() => $id), $fields);
			
			if (!empty($data) && !empty($data[$modelName])) {
				$this->Session->write('Auth.' . $modelName, $data[$modelName]);
			}
		}
		
	}
	
	/**
	 * Sets the 'last login' field of the currently logged-in user to the current time.
	 *
	 * The field which stores the datetime of the last login should be specified
	 * by the key 'last_login' in the $fields array
	 * 
	 * @return void
	 * @author David Roberts
	 */
	
	function setLastLogin()
	{
		$id = $this->user('id');
		if(!$id || !$this->fields['last_login']) return;
		
		$model =& $this->getModel();
		
		if($model->hasField($this->fields['last_login']))
		{
			$model->id = $id;
			$date = date('Y-m-d H:m:s');
			// We update the session manually. If we didn't set the last true in the line below
			// it would be reloaded automatically, but it's not worth it when we can just manually
			// make the change and save an unnecessary DB query
			$model->saveField($this->fields['last_login'], $date, false, true);
		}
	}
	
	/**
	 * Determines whether the user is logged in or not.
	 *
	 * @return boolean Whether the user is logged in
	 * @author David Roberts
	 */
	
	function loggedIn()
	{
		if($this->_loggedIn) return true;
		return $this->user() != null;
	}

	/**
	 * Retrieves information about the currently logged-in user from the session.
	 *
	 * We extend Auth::user() to also return any data in $extraSessionData.
	 * Normally, the model is not needed as the function will look in all the models
	 * for the key. However, if the $userModel contains that key, this will be returned
	 * first - set $model to choose which model it is returned from
	 * 
	 * @param string $key The field whose value should be returned. If not specified will return all fields
	 * @param string $model The model that the field belongs to (optional)
	 * @return string|array The value of the corresponding field(s) in the session
	 * @author David Roberts
	 */
	
	function user($key = null, $model = null) {
		$this->__setDefaults();
		if (!$this->Session->check($this->sessionKey)) {
			return null;
		}

		if ($key == null) {
			// We're returning all the data for a particular model
			if($model == null)
			{
				$modelKey = $this->sessionKey;
				$model = $this->userModel;
			} else {
				$modelKey = "Auth." . $model;
			}
			$data = array($model => $this->Session->read($modelKey));
			if(isset($data[$model]) && is_array($data[$model])) $data = $data[$model];
			return $data;
		} else {
			// We want a specific key for a particular model
			if($model == null)
			{
				$user = $this->Session->read($this->sessionKey);
				if (isset($user[$key])) {
					return $user[$key];
				} else {
					if(is_array($this->extraSessionData))
					{
						foreach ($this->extraSessionData as $model => $fields) {
							if(in_array($key, $fields))
							{
								$user = $this->Session->read("Auth." . $model);
								if (isset($user[$key])) return $user[$key];
							}
						}
					}
				}
			} else {
				$user = $this->Session->read("Auth." . $model);
				if (isset($user[$key])) return $user[$key];
			}
		}
			
		return null;
	}
	
	/**
	 * Sets one of the current user's fields in the session. Used when we make a small change
	 * to the user's info and want that change to be reflected in the session without having
	 * to query the database again
	 *
	 * @param string $fieldName 
	 * @param string $value 
	 * @param string $modelName 
	 * @return void
	 * @author David Roberts
	 */
	
	function setUser($fieldName, $value, $modelName = null)
	{
		if(!isset($modelName)) $modelName = $this->userModel;
		$this->Session->write('Auth.' . $modelName . '.' . $fieldName, $value);
		
	}
	
	/**
	 * Updates the user info in the session from the database. Call if changes might
	 * have been made to the info
	 *
	 * @return void
	 * @author David Roberts
	 */
	
	function reloadUser()
	{
		$user = $this->identify($this->user('id'));
		$this->Session->write($this->sessionKey, $user);
		$this->loadExtraSessionData();
	}

	/**
	 * Determines whether a user is authorized to execute the current action. Only called
	 * if $authorize is set to 'this'. It can also be called in views to determine whether to display
	 * certain options (e.g. an 'edit' link).
	 * 
	 * If no params are passed, will use the current controller and infer the action and objects from it.
	 * If the user is not passed, will use the currently logged-in user
	 *
	 * @param string $action The action of the controller to be executed
	 * @param string $objects The parameters of the action
	 * @param string $user The user performing the action
	 * @param string $controller The controller containing the action
	 * @return boolean Whether the user is allowed to execute the action or not
	 * @author David Roberts
	 */
	
	
	function userIsAuthorized($action = null, $objects = null, $user = null, $controller = null)
	{
		if(!isset($controller)) $controller = $this->controller;
		if(!isset($action)) $action = $controller->action;
		if(!isset($objects)) $objects = $controller->params['pass'];
		if(!isset($user)) $user = $this->user();
		
		// Always allow the user to log out
		if(isset($this->logoutAction) && (low($controller->name . "/" . $controller->action) == low($this->logoutAction))){
			return true;
		}

		//bfs Removed: permission now decide this, or in some cases the hardcoding of userCan takes care of it
		
		// Check whether we have explicitly authorized this action by putting it in the controller's
		// $allowIfLoggedIn variable
		// if (isset($controller->allowIfLoggedIn) && 
		// 		($controller->allowIfLoggedIn == array('*') || in_array($controller->action, $controller->allowIfLoggedIn))) 
		// 	{
		// 		return true;
		// 	}

		// Check with the appropriate controller to see if the user is authorized
		return $controller->userCan($action, $objects, $user);
	}
	
	/**
	 * bfs This was previously in auth; not in the most recent cake. Moved here instead.
	 */
		function _normalizeURL($url = '/') {
			if (is_array($url)) {
				$url = Router::url($url);
			}

			$paths = Router::getPaths();
			if(stristr($url, $paths['base'])) {
				$url = r($paths['base'], '', $url);
			}

			$url = '/' . $url . '/';

			while (strpos($url, '//') !== false) {
				$url = r('//', '/', $url);
			}
			return $url;
		}	
	
}
?>