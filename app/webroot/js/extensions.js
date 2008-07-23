
/*
 * InPlaceEditor extension that adds a 'click to edit' text when the field is 
 * empty.
 */
Ajax.InPlaceEditor.prototype.__initialize = Ajax.InPlaceEditor.prototype.initialize;
Ajax.InPlaceEditor.prototype.__getText = Ajax.InPlaceEditor.prototype.getText;
Ajax.InPlaceEditor.prototype.__onComplete = Ajax.InPlaceEditor.prototype.onComplete;
Ajax.InPlaceEditor.prototype.__createEditField = Ajax.InPlaceEditor.prototype.createEditField;
Ajax.InPlaceEditor.prototype.__enterEditMode = Ajax.InPlaceEditor.prototype.enterEditMode;
Ajax.InPlaceEditor.prototype = Object.extend(Ajax.InPlaceEditor.prototype, {

    initialize: function(element, url, options){
        this.__initialize(element,url,options)
        this.setOptions(options);
        this._checkEmpty();
		this._boundComplete = (this.onComplete || Prototype.emptyFunction).bind(this);
    },

    setOptions: function(options){
        this.options = Object.extend(Object.extend(this.options,{
            emptyText: 'click to add',
            emptyClassName: 'inplaceeditor-empty'
        }),options||{});
    },

    _checkEmpty: function(){
        if( this.element.innerHTML.length == 0 ){
            this.element.appendChild(
                Builder.node('span',{className:this.options.emptyClassName},this.options.emptyText));
        }
    },

    getText: function(){
        /*document.getElementsByClassName(this.options.emptyClassName,this.element).each(function(child){
            this.element.removeChild(child);
        }.bind(this)); BREAKS IN FF3 */
		this.element.getElementsBySelector('.'+this.options.emptyClassName).invoke('remove');
		if(this.options.editValue)
		{
			return this.options.editValue;
		} else {
        	return this.__getText();
		}
    },

    onComplete: function(transport){
		this._checkEmpty();
		var obj = transport.responseText.evalJSON();
		if (transport.responseText[0] == '{') 
		{
			if(obj.editValue != "undefined") {
				this.options.editValue = obj.editValue;
				this.element.innerHTML = obj.value;
			}
		};
		this._checkEmpty();
        this.__onComplete(transport);
    },

	createEditField: function() {
		this.__createEditField();
		
		if(this.options.calendar)
		{
			var field = this._form.getElementsByClassName('editor_field')[0];
			field.id = 'currentInlineEdit';
			var calendarScript = document.createElement("div");
			calendarScript.innerHTML = "<script type='text/javascript'>Calendar.setup({inputField : 'currentInlineEdit', ifFormat : '%d/%m/%y'});</script>";
	
		    this._form.appendChild(calendarScript);
		}
	}
	
});



Ajax.InPlaceListEditor = Class.create({
  initialize: function(element, url, options) {
	this.url = url;
    this.element = element = $(element);
	this._options = options;
	//this._options['collection'] = {'1':'one', '2':'two', '3':'three'};
	this.buttonClassName = 'listAddButton';
	this.formDivClassName = 'listAddForm';
	this.listItemClassName = 'listItem';
	this.setupElements();
	this.createAddForm();
	this.createAddButton();
    this.registerListeners();
  },
  setupElements: function() {
	
	var listObjects = this.element.getElementsByTagName("span");
	
	if(this._options['emptyText']) {
		this.emptyContent = document.createElement("div");
		this.emptyContent.innerHTML = this._options['emptyText'];
		this.emptyContent.className = this.listItemClassName + " empty";
		if (listObjects.length > 0) this.emptyContent.style.display = 'none';
		this.element.appendChild(this.emptyContent);
	}
	
	this.listElements = Array();
	var options = Object.extend(this._options, {
		parent: this
	});
	for (var i = 0; i < listObjects.length; i++) 
	{
		var listElement = new Ajax.InPlaceListElement(listObjects[i].id, this.url, options);
		this.listElements += listElement;
	};
	
	this.numElements = listObjects.length;
  },
  createAddButton: function() {
	this.addButton = document.createElement('div');
	this.addButton.className = this.buttonClassName;
	this.addButton.style.display = 'none';
	
	this.addButtonLink = document.createElement('a');
	this.addButtonLink.id = this.element.id + "AddButton";
	this.addButtonLink.innerHTML = "+";
	
	this.addButton.appendChild(this.addButtonLink);
	this.element.appendChild(this.addButton);
	
	var listener = this['enterAdd'].bind(this);
	Event.observe(this.addButtonLink.id, 'click', listener, false);
	
	var clearDiv = document.createElement('br');
	clearDiv.style.clear = 'both';
	this.element.appendChild(clearDiv);
  },
  createAddForm: function() {
	this.formDiv = document.createElement('div');
	this.formDiv.className = this.formDivClassName;
	this.formDiv.style.display = 'none';
	
	var form = document.createElement('form');
	form.onsubmit = this.handleFormSubmission.bind(this);
	
	var selectBox = document.createElement('select');
	$H(this._options['collection']).each(function(pair) {
      	var option = document.createElement('option');
		option.value = pair.key;
		option.innerHTML = pair.value;
		selectBox.appendChild(option);
    }.bind(this));
	this.addControl = selectBox;
	
	var submitButton = document.createElement('input');
	submitButton.type = 'submit';
	submitButton.value = 'Add';
	submitButton.onclick = this.handleFormSubmission.bind(this);
	
	form.appendChild(selectBox);
	form.appendChild(submitButton);
	this.formDiv.appendChild(form);
	this.element.appendChild(this.formDiv);
	
  },
  handleFormSubmission: function(e) {
	this.addName = this.addControl.options[this.addControl.selectedIndex].text;
	this.addId = this.addControl.options[this.addControl.selectedIndex].value;
	var url = this._options['addUrl'] + this.addId;
	//new Ajax.Updater({ success: this._options.update }, this._options['addUrl'] + value, null);
	var options = Object.extend({ method: 'get' }, {
		onComplete: this.handleAddSuccess.bind(this)
	});
	new Ajax.Request(url, options);
	if (e) Event.stop(e);
  },
  handleAddSuccess: function(e) {
	if (this.emptyContent) this.emptyContent.style.display = 'none';
	
	var newDiv = document.createElement('div');
	newDiv.id = this.element.id + "_" + this.addId;
	newDiv.className = this.listItemClassName;
	
	var newLink = document.createElement('a');
	newLink.href = this._options['viewUrl'] + this.addId;
	newLink.innerHTML = this.addName;
	
	newDiv.appendChild(newLink);
	//this.element.appendChild(newDiv);
	this.element.insertBefore(newDiv, this.formDiv);
	this.listElements += new Ajax.InPlaceListElement(newDiv.id, this.url, this._options);
	
	this.numElements++;
	this.leaveAdd();
	
	if (this._options['unique']) 
	{
		// Remove the added element from the select box list
		var addOptions = this.addControl.options;
		for (var i = 0; i <= addOptions.length-1; i++) {
			if (addOptions[i].value == this.addId) addOptions[i] = null;
		}
	}
  },
  enterHover: function(e) {
	if(!this.isAddButtonClicked) this.addButton.style.display = 'block';
  },
  leaveHover: function(e) {
	this.addButton.style.display = 'none';
  },
  enterAdd: function(e) {
	this.formDiv.style.display = 'block';
	this.addButton.style.display = 'none';
	this.isAddButtonClicked = true;
  },
  leaveAdd: function(e) {
	this.formDiv.style.display = 'none';
	this.addButton.style.display = 'block';
	this.isAddButtonClicked = false;
  },
  registerListeners: function() {
    this._listeners = { };
    var listener;
    $H(Ajax.InPlaceListEditor.Listeners).each(function(pair) {
      listener = this[pair.value].bind(this);
      this._listeners[pair.key] = listener;
      this.element.observe(pair.key, listener);
    }.bind(this));
  }
});

Object.extend(Ajax.InPlaceListEditor, {
  Listeners: {
    mouseover: 'enterHover',
    mouseout: 'leaveHover'
  }
});


Ajax.InPlaceListElement = Class.create({
  initialize: function(element, url, options) {
	this.url = url + element.split("_")[1];
    this.element = element = $(element);
	this._options = options;

	this.normalClassName = this.element.className;
	this.normalInnerClassName = 'listItemInner';
	this.hoverClassName = 'listItemHover';
	this.buttonClassName = 'listItemRemoveButton';
	this.setupElement();
    this.registerListeners();
  },
  setupElement: function() {
	var oldCode = this.element.innerHTML;
	this.element.innerHTML = '';
	var mainId = this.element.id;
	
	this.innerDiv = document.createElement('div');
	this.innerDiv.id = "inner" + mainId;
	this.innerDiv.className = this.normalInnerClassName;
	this.innerDiv.innerHTML = oldCode;
	this.element.appendChild(this.innerDiv);
	
	this.button = document.createElement('div');
	this.button.id = "button"+ mainId;
	this.button.className = this.buttonClassName;
	this.button.style.display = 'none';
	this.element.appendChild(this.button);
	
	this.buttonLink = document.createElement('a');
	this.buttonLink.href = this.url;
	this.buttonLink.id = "buttonLink"+mainId;
	this.buttonLink.innerHTML = 'x';
	this.button.appendChild(this.buttonLink);
	this.buttonLink.onclick = this.handleRemove.bind(this);
	
	//var update = this._options.update;
	//Event.observe('buttonLink'+mainId, 'click', function(event) { new Ajax.Updater(update, url, {asynchronous:true, evalScripts:true, requestHeaders:['X-Update', update]}); }, false);
  },
  enterHover: function(e) {
	this.element.className = this.hoverClassName;
	this.button.style.display = 'block';
  },
  leaveHover: function(e) {
	this.element.className = this.normalClassName;
	this.button.style.display = 'none';
  },
  handleRemove: function(e) {
	var options = Object.extend({ method: 'get' }, {
		onComplete: this.handleRemoveSuccess.bind(this)
	});
	new Ajax.Request(this.url, options);
	if (e) Event.stop(e);
  },
  handleRemoveSuccess: function(e) {
	this.element.parentNode.removeChild(this.element);
	//this.destroy();
	
	var list = this._options['parent'];
	list.numElements--;
	if(list.numElements < 1 && list.emptyContent) list.emptyContent.style.display = 'block';
  },
  registerListeners: function() {
    this._listeners = { };
    var listener;
    $H(Ajax.InPlaceListElement.Listeners).each(function(pair) {
      listener = this[pair.value].bind(this);
      this._listeners[pair.key] = listener;
      this.element.observe(pair.key, listener);
    }.bind(this));
  }
});

Object.extend(Ajax.InPlaceListElement, {
  Listeners: {
    mouseover: 'enterHover',
    mouseout: 'leaveHover'
  }
});


Ajax.bindControl = Class.create({
  initialize: function(element, controlElement) {
    this.element = $(element);
    this.controlElement = $(controlElement);
	this.controlElement.style.display = 'none';
	this.element.observe('mouseover', this.showElement.bind(this));
	this.element.observe('mouseout', this.hideElement.bind(this));
  },
  showElement: function(event) {
    this.controlElement.style.display = 'block';
  },
  hideElement: function(event) {
    this.controlElement.style.display = 'none';
  }
});


Ajax.floatWindow = Class.create({
  initialize: function(element, closeElement) {
    this.element = $(element);
    this.closeElement = $(closeElement);

	this.element.style.position = 'absolute';
	this.element.style.left = '50%';
	this.element.style.top = '100px';
	this.element.style.marginLeft = '-' + this.element.offsetWidth/2 + 'px';
	alert('width: ' + this.element.style.marginLeft + ', height ' + this.element.offsetHeight);
	/*this.element.observe('mouseover', this.showElement.bind(this));
	this.element.observe('mouseout', this.hideElement.bind(this));*/
  },
  showElement: function(event) {
    this.controlElement.style.display = 'block';
  },
  hideElement: function(event) {
    this.controlElement.style.display = 'none';
  }
});


/*
 * Sortable list that you can dynamically add rows to
 */
Ajax.DynamicSortable = Class.create({
	initialize: function(element, options) {
		this.element = $(element);
		this.options = Object.extend({ 
	      innerCode:   'error: innerCode not specified',
	      innerId:     'fieldList',
	      innerClass:  'fieldListItem'
	    }, options || { });
		this.update();
	},
	addRow: function(code) {
		var newRow = document.createElement('li');
		newRow.className = this.options.innerClass;
		newRow.id = this.options.innerId;
		
		if(code != null)
			newRow.innerHTML = code;
		else
			newRow.innerHTML = this.options.innerCode;

		this.element.appendChild(newRow);
		this.update();
	},
	update: function() 
	{
		Sortable.create(this.element.id, this.options);
	}
});











