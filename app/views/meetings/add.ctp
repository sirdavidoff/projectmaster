<?php 
// The already present meetings and any meeting that is in the process of being added (but whose
// validation failed) will both use the $this->data['Meeting'] array. For this reason, we put the
// editing one in $editMeeting
if(isset($editMeeting))
{
	$forms->data['Meeting'] = $editMeeting; 
}

?>

<div id="addMeetingContainer" style="<?php if(!$forms->areErrors('Meeting')) echo "display:none;" ?> border:1px solid gray; padding:10px">
	<h3>Add a Meeting</h3>
	<?php echo $ajax->form() ?>
		<?php echo $forms->hidden("Meeting.contact_id", array("value" => $contact_id)) ?>
		<table  cellspacing="0" cellpadding="0" width="100%">
			<tr>
				<td valign="top" colspan="4">
					<?php 
						$with = ''; 
						if (isset($this->data['Person'][0])) $with = $this->data['Person'][0]['name'];
					?>
					Person/People meeting with<br />
					<?php echo $forms->text("Meeting.with", Array('value' => $with)) ?>
					<?php echo $forms->error('Meeting.with') ?>
				</td>
			</tr>
			<tr>
				<td valign="top" colspan="4">
					Notes<br />
					<?php echo $forms->textarea("Meeting.text", array("rows" => 2)) ?>
					<?php echo $forms->error('Meeting.text') ?>
				</td>
			</tr>
			<tr>
				<td valign="top" style="padding-top:6px;padding-right:10px">
					Date<br />
					<?php echo $forms->calendarText('Meeting.date_readable', array('defaultValue' => 'dd/mm/yy')) ?>
				</td>
				<td valign="top" style="padding-top:6px;padding-right:10px">
					Time<br />
					<?php echo $forms->text('Meeting.time', array('defaultValue' => 'hh:mm', 'maxlength' => '5', 'style' => 'width:70px')) ?>
				</td>
				<td colspan="2" valign="bottom" align="right" style="padding-top:6px;width:90%">
					<?php echo $ajaxs->submit("Add Meeting", array('url' => '/Meetings/add', 'update'=>'addMeeting')) ?>
				</td>
			</tr>
		</table>
		<?php if($forms->areErrors('Meeting')): ?>
			<?php echo $forms->error('Meeting.date') ?>
		<?php endif ?>
		<?php echo $forms->error('Meeting.time') ?>
	<?php echo "</form>" ?>
</div>