<div id="addMeeting">	
	<?php if (isset($this->data['Meeting']) && count($this->data['Meeting']) > 0): ?>

		<?php
		// Work out whether there are any hidden meetings
		$completedMeetings = false;
		foreach($this->data['Meeting'] as $meeting) 
		{
			if($meeting['date'] < date('Y-m-d')) $completedMeetings = true;
		}
		?>
		
		<table style="width:100%" cellspacing="0" cellpadding="0">
			<tr>
				<td valign="top"><h2>Meetings</h2></td>
				<td valign="bottom" style="padding-bottom:11px" align="right">
					<?php echo $this->renderElement('../meetings/add_link') ?>
					<!--<?php if ($completedMeetings): ?>
						|
						<?php if(!isset($showAllMeetings) || $showAllMeetings == 0): ?>
							<?php echo $ajaxs->link('Show all meetings', '/meetings/listAll/' . $contact_id . '/1', array('update' => 'addMeeting')) ?>
						<?php else: ?>
							<?php echo $ajaxs->link('Show only future meetings', '/meetings/listAll/' . $contact_id . '/0', array('update' => 'addMeeting')) ?>
						<?php endif ?>
					<?php endif ?>-->
					
				</td>
			</tr>
		</table>

		<?php echo $this->renderElement('../meetings/add', array('contact_id' => $contact_id)) ?>
		
		<div class="meetingsBox">
			<ul id="meetings" style="margin:0px">
				<?php foreach ($this->data['Meeting'] as $meeting): ?>
					<?php if ((isset($showAllMeetings) && $showAllMeetings == 1) || $meeting['date'] >= date('Y-m-d')): ?>	
						<li id='<?php echo "meeting_" . $meeting['id'] ?>' class="meetingBox<?php if($meeting['date'] < date('Y-m-d') || ($meeting['date'] == date('Y-m-d') && $meeting['time'] < date('H:i'))) echo "Completed" ?>">
							
							<table style="width:100%" cellspacing="0" cellpadding="0">
								<tr>
									<td valign="top" style="padding:10px">
										With <?php echo $ajaxs->editable($meeting['id'], $meeting['with'], 'with', 'Meeting', 'meetings', 'span', array('emptyText' => "'add person'")) ?>
										<?php echo $ajaxs->editable($meeting['id'], $meeting['text'], 'text', 'Meeting', 'meetings', 'div', array('rows' => 2, 'emptyText' => "'add note'")) ?>

										<?php if (!isset($print) || !$print): ?>
											<?php echo $ajaxs->link('remove', '/meetings/delete/' . $meeting['id'], array('update' => 'addMeeting'), "If you remove this meeting it will be deleted forever. Are you sure you want to do this?") ?>
										<?php endif ?>
									</td>
									<td valign="top" class="meetingDateCell">
										<div class="shortDate"><?php echo $ajaxs->editable($meeting['id'], $format->shortDate($meeting['date']), 'date_readable', 'Meeting', 'meetings', 'span', array('emptyText' => "'set date'", 'calendar' => true, 'editValue' => "'".$format->slashDate($meeting['date'])."'")) ?></div>
										<?php echo $ajaxs->editable($meeting['id'], $meeting['time'], 'time', 'Meeting', 'meetings', 'span', array('emptyText' => "'set time'")) ?>
									</td>
								</tr>
							</table>
						</li>
					<?php endif ?>
				<?php endforeach ?>
			</ul>
		</div>
		
	<?php else: ?>
		<div style="text-align:right">
			<?php echo $this->renderElement('../meetings/add_link') ?>
		</div>
		<?php echo $this->renderElement('../meetings/add', array('contact_id' => $contact_id)) ?>
		
	<?php endif  ?>
</div>