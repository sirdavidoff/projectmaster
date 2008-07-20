
<table style="width:100%" cellspacing="0" cellpadding="0">
	<tr>
		<td valign="top" style="padding-top:3px; width:80px">Opened by</td>
		<td valign="top">
			
			<?php 
				$list = Array();
				if(isset($this->data['Openee']) && count($this->data['Openee']) > 0) 
				{
					$list = array_combine(Set::extract($this->data['Openee'], '{n}.id'), Set::extract($this->data['Openee'], '{n}.name'));
				}
			 ?>
			<?php echo $ajaxs->editableList($list, Array(
													'viewUrl' => '/contacts/view/', 
													'removeUrl' => '/mediatise/contacts/removeOpenee/'.$this->data['Contact']['id'].'/',
													'addUrl' => '/mediatise/contacts/addOpenee/'.$this->data['Contact']['id'].'/',
													'addList' => $openeeContactList,
													'emptyText' => 'no-one'	
													)) ?>
			
		</td>
	</tr>
	<tr>
		<td valign="top" style="padding-top:3px">Opens</td>
		<td valign="top">
			
			<?php 
				$list = Array();
				if(isset($this->data['Opener']) && count($this->data['Opener']) > 0) 
				{
					$list = array_combine(Set::extract($this->data['Opener'], '{n}.id'), Set::extract($this->data['Opener'], '{n}.name'));
				}
			 ?>
			<?php echo $ajaxs->editableList($list, Array(
													'viewUrl' => '/contacts/view/', 
													'removeUrl' => '/mediatise/contacts/removeOpener/'.$this->data['Contact']['id'].'/',
													'addUrl' => '/mediatise/contacts/addOpener/'.$this->data['Contact']['id'].'/',
													'addList' => $openerContactList,
													'emptyText' => 'no-one'	
													)) ?>
			
		</td>
	</tr>
</table>

