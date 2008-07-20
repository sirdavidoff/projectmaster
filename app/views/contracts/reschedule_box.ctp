<div class="formBox" id="rescheduleContractContainer<?php echo $id ?>" style="margin-top:7px; display:none">
	<?php echo $forms->create('Contract', array('url' => '/contracts/reschedule')) ?>
		<?php echo $forms->hidden('Contract.id', array('value' => $data['Contract']['id'])) ?>
		<table style="width:100%" cellspacing="0" cellpadding="0">
			<tr>
				<td valign="middle">
					Reschedule payment deadline to the
					<?php echo $forms->calendarText('Contract.payment_by_readable', array('value' => $format->slashDate($data['Contract']['payment_by']))) ?>
				</td>
				<td valign="middle" align="right">
					<?php echo $html->link('cancel', "javascript:(function(){\$('rescheduleContractContainer$id').style.display = 'none';\$('rescheduleContractLink$id').style.display = 'inline'}())") ?>
					<?php echo $forms->submit("Reschedule", array('url' => '/Contracts/add', 'update'=>'addContract')) ?>
				</td>
			</tr>
		</table>
	</form>
</div>