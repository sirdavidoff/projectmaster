<?php

class ContactStatusChange extends AppModel 
{
	var $name = 'ContactStatusChange';

	var $belongsTo = array('Contact' => array('className' => 'Contact', 'foreignKey' => 'contact_id'));
}
?>