<?php 
// The already present actions and any action that is in the process of being added (but whose
// validation failed) will both use the $this->data['Action'] array. For this reason, we put the
// editing one in $editAction
if(isset($editAction))
{
	$forms->data['Action'] = $editAction; 
}

?>

<div id="addActionContainer" style="<?php if(!$forms->areErrors('Action')) echo "display:none;" ?> border:1px solid gray; padding:10px">
	<h3>Add an Action</h3>
		<?php if (isset($contact_id)): ?>
			<?php echo $ajaxs->form(array('action' => 'add'), 'post', array('model' => 'action')) ?>
			<?php echo $forms->hidden("Action.contact_id", array("value" => $contact_id)) ?>
		<?php else: ?>
			<?php echo $forms->create('Action') ?>
			<?php echo $forms->hidden("Action.contactSet", array("value" => 1)) ?>
		<?php endif ?>
		<table  cellspacing="0" cellpadding="0" width="100%">
			<?php if (!isset($contact_id)): ?>
				<tr>
					<td valign="top" colspan="4">
						Contact (if any)<br />
						<?php echo $forms->select('Action.contact_id', $contactList, 0, null, 'None') ?>
					</td>
				</tr>
			<?php endif ?>
			<tr>
				<td valign="top" colspan="4">
					Description<br />
					<?php echo $forms->textarea("Action.text", array("rows" => 2)) ?>
					<?php echo $forms->error('Action.text') ?>
				</td>
			</tr>
			<tr>
				<td valign="top" style="padding-top:6px;padding-right:10px">
					Final&nbsp;Date<br />
					<?php echo $forms->calendarText('Action.deadline_date_readable', array('defaultValue' => 'dd/mm/yy')) ?>
				</td>
				<td valign="top" style="padding-top:6px;padding-right:10px">
					Final&nbsp;Time<br />
					<?php echo $forms->text('Action.deadline_time', array('defaultValue' => 'hh:mm', 'maxlength' => '5', 'style' => 'width:70px')) ?>
				</td>
				<td valign="top" style="padding-top:6px;padding-right:10px">
					Assigned to<br />
					<?php 
						if(isset($forms->data['Action']['user_id']))
						{
							$selectedVal = $forms->data['Action']['user_id'];
						} else {
							$selectedVal = $cauth->user('id');
						}
					?>
					<?php echo $forms->select('Action.user_id', $userList, $selectedVal, null, false) ?>
				</td>
				<td valign="bottom" align="right" style="padding-top:6px;width:90%">
					<?php if (isset($contact_id)): ?>
						<?php echo $ajaxs->submit("Add Action", array('url' => '/Actions/add', 'update'=>'addAction')) ?>
					<?php else: ?>
						<?php echo $forms->submit("Add Action", array('url' => '/Actions/add')) ?>
					<?php endif ?>	
				</td>
			</tr>
		</table>
		<?php if($forms->areErrors('Action')): ?>
			<?php echo $forms->error('Action.deadline_date_readable') ?>
		<?php endif ?>
		<?php echo $forms->error('Action.deadline_time') ?>
	<?php echo "</form>" ?>
</div>