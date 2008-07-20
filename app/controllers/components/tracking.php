<?php 
/**
 * Tracks the user's actions whilst on the site.
 *
 * @package ppc
 * @subpackage ppc.components
 * @author David Roberts
 */

/**
 * Tracks the user's actions whilst on the site.
 * 
 * We define an array called $redirectSafe in the controller which lists the
 * actions that it is normally safe to redirect back to.
 * When the user has just finished performing some action and needs to be
 * redirected somewhere, we can use lastSafePage() to redirect back to a 
 * previous page that it is safe to return to.
 * 
 * Tracking will not work if cookies are disabled.
 * 
 * Tracking can be done in one of two ways - through this component or through
 * Javascript. The type of tracking is determined by the $jsTracking variable.
 * 
 * Component - doesn't need Javascript enabled to work but won't update if the
 * user hits the back button
 * 
 * Javascript - won't work if the client has disabled Javascript but will update
 * if the user hits the back button (in IE at least)
 * 
 * NB: If you use the Javascript option, make sure that your layout includes the
 * tracking.ctp element and that webroot/js/tracking.js is installed.
 *
 * @package ppc
 * @subpackage ppc.components
 * @author David Roberts
 */

class TrackingComponent extends Object
{
	var $components = array('Session', 'RequestHandler');
	
	/**
	 * Whether we use Javascript or this component to manage the
	 * session where we track the visited pages. Note that if we are
	 * using Javascript, the element 'tracking' needs to be included
	 * in the template, and the file 'tracking.js' needs to be present
	 * in webroot/js
	 *
	 * @var string
	 */
	
	var $jsTracking = true;
	
	/**
	 * The name of the cookie for storing the URLs of visited pages
	 *
	 * @var string
	 */
	
	var $cookieName = 'xtracker';
	
	/**
	 * The path in the site for which the cookie is valid
	 *
	 * @var string
	 */
	
	var $cookiePath = '/';
	
	/**
	 * The maxium number of URLs that we will store in the cookie. If using
	 * javascript for the tracking (see $jsTracking), remember that the
	 * maximum cookie length is 4000 characters
	 *
	 * @var string
	 */
	
	var $cookieLength = 3;
	
	/**
	 * If lastSafePage() is called and we have no safe pages stored, will
	 * return this string
	 *
	 * @var string
	 */
	
	var $defaultPage = '/users/front';
	
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
		$this->controller->set('tracking', $this);
		
		$this->trackCurrentPage();
	}
	
	/**
	 * Determines whether we need to track the current page, and if so, does so. 
	 * If we do (i.e. it's in the controller's $redirectSafe array) it will either
	 * perform the tracking itself or, if the tracking is to be done by javascript
	 * (if $jsTracking is set), will pass the appropriate variables to the view so 
	 * that the javascript tracker can track the page.
	 *
	 * @return void
	 * @author David Roberts
	 */
	
	function trackCurrentPage()
	{
		// We never track ajax actions as we can't redirect to them
		if($this->RequestHandler->isAjax()) return;
		
		if(isset($this->controller->redirectSafe)) $safeActions = $this->controller->redirectSafe;
		if(!isset($safeActions)) return;
		if(!is_array($safeActions)) $safeActions = array($safeActions);
		
		if(!in_array($this->controller->action, $safeActions) && $safeActions != array('*')) return;
				
		// If we are at the site root there may be no URL to store.
		// Really we should store it anyway, but there's no easy way to do that
		if(!$this->controller->params['url']) return;
		
		// We're in an action that should be added to the tracker
		// Strip the trailing slash of the action if there is one
		$url = "/" . rtrim($this->controller->params['url']['url'], "/");

		if($this->jsTracking)
		{
			// Pass the url plus relevant info to the view so that JS can do the tracking
			$tracker['name'] = $this->cookieName;
			$tracker['path'] = $this->cookiePath;
			$tracker['length'] = $this->cookieLength;
			$tracker['url'] = $url;
			$this->controller->set('tracker', $tracker);
		} else {
			$urls = $this->_readTrackingCookie($this->cookieName);
			$this->_writeTrackingCookie($this->_addToArray($url, $urls));
		}
	}
	
	/**
	 * Used if we are not tracking using Javascript. Will add a url on to the
	 * end of an array, making sure that the array remains unique and below
	 * the maximum length specified by $cookieLength.
	 *
	 * @param string $item The item to be added on the end of the array
	 * @param string $array The array to add the item to
	 * @return array The array with the item added on the end
	 * @author David Roberts
	 */
	
	function _addToArray($item, $array)
	{
		// Remove any pre-existing copies of $item already in the array
		for($i=0; $i < count($array); $i++) 
		{ 
			if($array[$i] == $item) 
			{
				array_splice($array, $i, 1);
				$i--;
			}
		}
		
		// Add the item onto the end of the array
		$array[] = $item;
		
		// We limit the max array size to $cookieLength, trimming the oldest item
		// if it is too long
		return array_slice($array, -($this->cookieLength), $this->cookieLength);
	}
	
	/**
	 * Reads the tracking cookie and returns its contents in an array.
	 *
	 * @return array The contents of the cookie
	 * @author David Roberts
	 */
	
	function _readTrackingCookie()
	{
		if(isset($_COOKIE[$this->cookieName]))
		{
			return explode("$", $_COOKIE[$this->cookieName]);
		}
	}
	
	/**
	 * Writes the value passed to the tracking cookie. If the value is
	 * an array, will automatically delimit it.
	 * 
	 * Note that this function can only be called once, and must be
	 * called before any output has been sent as it relies on the
	 * setcookie() function.
	 *
	 * @param string $value The string/array that should be stored in the cookie
	 * @return void
	 * @author David Roberts
	 */
	
	function _writeTrackingCookie($value)
	{
		if(is_array($value)) $value = implode("$", $value);
		setcookie($this->cookieName, $value, null, $this->cookiePath);
	}
	
	/**
	 * Removes URLs matching $patterns from the tracking cookie. Note that 
	 * because of the use of _writeTrackingCookie(), this function may be called
	 * only once, and should not be called before lastSafePage() (call 
	 * lastSafePage($patterns) instead - it will do the same thing).
	 *
	 * @param string|array $patterns The patterns to be matched. If they start with '/' they are assumed to be regexps
	 * @return void
	 * @author David Roberts
	 */
	
	function untrack($patterns)
	{
		$urls = $this->_readTrackingCookie();
		
		if(!$urls || !$patterns) return $urls;
		if(!is_array($patterns)) $patterns = array($patterns);
		
		$newUrls = array();
		for($u=0; $u < count($urls); $u++) 
		{
			$remove = false; 
			for($p=0; $p < count($patterns); $p++) 
			{ 
				// If the pattern is not a regular expression, make it one
				if($patterns[$p][0] != "/") $patterns[$p] = "/" . preg_quote($patterns[$p], '/') . "/";
				if(preg_match($patterns[$p], $urls[$u]) > 0) $remove = true;
			}
			
			if(!$remove) $newUrls[] = $urls[$u];
		}
		
		$this->_writeTrackingCookie($newUrls);
		
		return $newUrls;
	}
	
	/**
	 * Returns the last entry in the tracking cookie. Must not be called
	 * again once called with $patterns, and should not be used in the
	 * same action as untrack().
	 *
	 * @param string|array $patterns URLs matching these patterns are removed from the tracking cookie
	 * @return string The URL of the latest safe page the user has visited
	 * @author David Roberts
	 */
	
	function lastSafePage($patterns = null)
	{
		$urls = $this->untrack($patterns);
		
		if(isset($urls))
		{
			if(!is_array($urls))
			{
				return $urls;
			} else if(count($urls) > 0) {
				return array_pop($urls);
			}
		}
		
		return $this->defaultPage;
	}
}