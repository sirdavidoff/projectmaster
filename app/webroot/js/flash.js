var Flash = new Object;
Flash.flashDivId = 'flashDiv';

Flash.setFlash = function(msg, type)
{
	switch(type)
	{
		case 'error' : type = 'flashError'; break;
		case 'warning': type = 'flashWarning'; break;
		case 'notice' :
		default: type = 'flashNotice'; break;
	}
	flashDiv = document.getElementById(Flash.flashDivId);
	flashDiv.innerHTML = msg;
	flashDiv.className = type;
	flashDiv.style.display = "block";
	Flash.scrollTo(flashDiv);
}

Flash.setFlashDiv = function(divID)
{
	Flash.flashDivId = divID;
}

Flash.scrollTo = function(element)
{
	var selectedPosX = 0;
	var selectedPosY = 0;
	
	while(element != null)
	{
		selectedPosX += element.offsetLeft;
		selectedPosY += element.offsetTop;
		element = element.offsetParent;
	}
	
	selectedPosY -= 10;
	
	window.scrollTo(selectedPosX,selectedPosY);
}
