var Validate = new Object;
Validate.errors = [];

Validate.addField = function(model, field, errorCode, msg)
{
	if(!Validate.errors[model]) Validate.errors[model] = [];
	if(!Validate.errors[model][field]) Validate.errors[model][field] = [];
	
	if(!errorCode) errorCode = 0;
	Validate.errors[model][field][errorCode] = msg;
}

Validate.getError = function(model, field, errorCode)
{
	if(!errorCode) return;
	
	if(Validate.errors[model] && Validate.errors[model][field])
	{
		if(Validate.errors[model][field][errorCode])
		{
			return Validate.errors[model][field][errorCode];
		}
	}
	
	return errorCode;
}


Validate.setErrorDiv = function(model, field, divId, errorCode)
{
	var div = $(divId);
	
	if(errorCode.strip() == '')
	{
		div.style.display = 'none';
	} else {
		div.innerHTML = Validate.getError(model, field, errorCode);
		div.style.display = 'block';
	}
}

Validate.processError = function(response, model, field, div)
{
	//var resp = eval("(" + response.responseText + ")");
	//Validate.setErrorDiv(model, field, div, resp.errorCode);
	Validate.setErrorDiv(model, field, div, response.responseText);
}
