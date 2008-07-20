<div class="formBox" id="rescheduleActionContainer<?php echo $id ?>" style="margin-top:7px; display:none">
	<?php echo $forms->create('Action', array('url' => '/actions/reschedule')) ?>
		<?php echo $forms->hidden('Action.id', array('value' => $data['Action']['id'])) ?>
		<table style="width:100%" cellspacing="0" cellpadding="0">
			<tr>
				<td valign="middle">
					<?php 
						if(isset($data['Action']['deadline_time']) && trim($data['Action']['deadline_time']) != "") 
						{
							$defaultTime = $data['Action']['deadline_time'];
						} else {
							$defaultTime = "hh:mm";
						}
					?>
					Reschedule for the
					<?php echo $forms->calendarText('Action.deadline_date_readable', array('value' => $format->slashDate($data['Action']['deadline_date']))) ?>
					at
					<?php echo $forms->text('Action.deadline_time', array('value' => $defaultTime, 'maxlength' => '5', 'style' => 'width:70px')) ?>
					<!--<input id="ActionDeadlineDateReadable" class="calendarText" type="text" value="<?php echo $format->slashDate($data['Action']['deadline_date']) ?>" maxlength="8" name="data[Action][deadline_date_readable]"/>
					at
					<input id="ActionDeadlineTime" type="text" value="<?php echo $defaultTime ?>" style="width: 70px;" maxlength="5" name="data[Action][deadline_time]"/>-->
				</td>
				<td valign="middle" align="right">
					<?php echo $html->link('cancel', "javascript:(function(){\$('rescheduleActionContainer$id').style.display = 'none';\$('rescheduleActionLink$id').style.display = 'inline'}())") ?>
					<?php echo $forms->submit("Reschedule", array('url' => '/Actions/add', 'update'=>'addAction')) ?>
				</td>
			</tr>
		</table>
	</form>
</div>