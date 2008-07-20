Tracker = new Object;

Tracker.trackerLength = 3;

Tracker.track = function(url, cookieName, cookiePath)
{
	if(!cookieName) cookieName = 'tracker';
	if(!cookiePath) cookiePath = '/';
	
	var urls = Tracker.readCookie(cookieName);
	Tracker.writeCookie(cookieName, Tracker.addUrlToArray(url, urls), cookiePath);
}

Tracker.addUrlToArray = function(url, urls)
{
	if(!urls || urls == ""){
		urls = new Array();
	} else if(urls.constructor.toString().indexOf("Array") == -1) {
		// urls is not an array - make it one
		var val = urls
		urls = new Array();
		urls[0] = val;
	}
	
	// Remove any pre-existing copies of url in the array
	newUrls = new Array();
	for (var i = 0; i < urls.length; i++) {
		if (urls[i] != url) newUrls[newUrls.length] = urls[i];
	}
	
	newUrls[newUrls.length] = url;
	
	// If the array is longer than Tracker.trackerLength, take the
	// oldest urls off it
	if(newUrls.length > Tracker.trackerLength) 
		newUrls.splice(0, newUrls.length - Tracker.trackerLength);	
		
	return newUrls; 
	
}

Tracker.readCookie = function(cookieName)
{
	var bigCookie = "" + document.cookie;
	var start = bigCookie.indexOf(cookieName);
	if (start == -1 || cookieName == "") return null; 
	var end = bigCookie.indexOf(';', start);
	if (end == -1) end = bigCookie.length; 
	var theCookie = unescape(bigCookie.substring(start + cookieName.length + 1, end));
	
	// Check whether it's an array that we need to process
	if(theCookie == "" || theCookie.indexOf("$") == -1) 
	{
		return theCookie;
	} else {
		return theCookie.split("$");
	}
}


Tracker.writeCookie = function(cName, cValue, cPath) 
{
	// Check whether the value is an array
	if(cValue.constructor.toString().indexOf("Array") != -1)
	{
		cValue = cValue.join("$");
	}
 
 	document.cookie = cName + "=" + escape(cValue) + ";path=" + cPath;
}