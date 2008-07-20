<div class="formBox" id="rescheduleMeetingContainer<?php echo $id ?>" style="margin-top:7px; display:none">
	<?php echo $forms->create('Meeting', array('url' => '/meetings/reschedule')) ?>
		<?php echo $forms->hidden('Meeting.id', array('value' => $data['Meeting']['id'])) ?>
		<table style="width:100%" cellspacing="0" cellpadding="0">
			<tr>
				<td valign="middle">
					<?php 
						if(isset($data['Meeting']['time']) && trim($data['Meeting']['time']) != "") 
						{
							$defaultTime = $data['Meeting']['time'];
						} else {
							$defaultTime = "hh:mm";
						}
					?>
					Reschedule for the
					<?php echo $forms->calendarText('Meeting.date_readable', array('value' => $format->slashDate($data['Meeting']['date']))) ?>
					at
					<?php echo $forms->text('Meeting.time', array('value' => $defaultTime, 'maxlength' => '5', 'style' => 'width:70px')) ?>
				</td>
				<td valign="middle" align="right">
					<?php echo $html->link('cancel', "javascript:(function(){\$('rescheduleMeetingContainer$id').style.display = 'none';\$('rescheduleMeetingLink$id').style.display = 'inline'}())") ?>
					<?php echo $forms->submit("Reschedule", array('url' => '/Meetings/add', 'update'=>'addMeeting')) ?>
				</td>
			</tr>
		</table>
	</form>
</div>