<div id="addAction">	
	<?php if (isset($this->data['Action']) && count($this->data['Action']) > 0): ?>
		
		<?php
		// Work out whether there are any hidden actions
		$completedActions = false;
		foreach($this->data['Action'] as $action) 
		{
			if($action['completed'] == 1) $completedActions = true;
		}
		?>
		
		<table style="width:100%" cellspacing="0" cellpadding="0">
			<tr>
				<td valign="top"><h2>Actions</h2></td>
				<td valign="bottom" style="padding-bottom:11px" align="right">
					<?php echo $this->renderElement('../actions/add_link') ?>
					<?php if ($completedActions && (!isset($print) || !$print)): ?>
						|
						<?php if(!isset($showAllActions) || $showAllActions == 0): ?>
							<?php echo $ajaxs->link('Show all actions', '/actions/listAll/' . $contact_id . '/1', array('update' => 'addAction')) ?>
						<?php else: ?>
							<?php echo $ajaxs->link('Show only uncompleted actions', '/actions/listAll/' . $contact_id . '/0', array('update' => 'addAction')) ?>
						<?php endif ?>
					<?php endif ?>
					
				</td>
			</tr>
		</table>

		<?php echo $this->renderElement('../actions/add', array('contact_id' => $contact_id)) ?>
		
		<div class="actionsBox">
			<ul id="actions" style="margin:0px">
				<?php foreach ($this->data['Action'] as $action): ?>
					<?php if ((isset($showAllActions) && $showAllActions == 1 && (!isset($print) || !$print)) || $action['completed'] == 0): ?>	
						<li id='<?php echo "action_" . $action['id'] ?>' class="actionBox<?php if($action['completed'] == 1) echo "Completed" ?>">
							
							<table style="width:100%" cellspacing="0" cellpadding="0">
								<tr>
									<td valign="top" style="padding:10px">
										<?php echo $ajaxs->editable($action['id'], $action['text'], 'text', 'Action', 'actions', 'div', array('rows' => 2)) ?>
										<?php 
										if(isset($action['user_id']) && $action['user_id'] != 0) 
										{
											$userName = $userList[$action['user_id']];
										} else {
											$userName = 'everyone';
										}
										?>
										
										<?php if($action['completed'] == 0): ?>
											Assigned to <?php echo $ajaxs->editable($action['id'], $userName, 'user_id', 'Action', 'actions', 'span', array('collection' => $userList)) ?>
										<?php else: ?>
											Completed <?php echo $time->niceShort($action['completed_at']) ?>
											<?php if ($action['completed_by']): ?>
												by <?php echo $allUserList[$action['completed_by']] ?>
											<?php endif ?>
										<?php endif ?>
										
										<?php if (!isset($print) || !$print): ?>
											<?php echo $ajaxs->link('remove', '/actions/delete/' . $action['id'], array('update' => 'addAction'), "If you remove this action it will be deleted forever. Are you sure you want to do this? (If you've finished it just click 'done' instead)") ?>
											<?php if ($action['completed'] == 0): ?>
												<?php echo $ajaxs->link('done', '/actions/done/' . $action['id'], array('update' => 'addAction')) ?>
											<?php endif ?>
										<?php endif ?>
									</td>
									<td valign="top" class="actionDateCell">
										<div class="shortDate"><?php echo $ajaxs->editable($action['id'], $format->shortDate($action['deadline_date']), 'deadline_date_readable', 'Action', 'actions', 'span', array('emptyText' => "'set date'", 'calendar' => true, 'editValue' => "'".$format->slashDate($action['deadline_date'])."'")) ?></div>
										<?php echo $ajaxs->editable($action['id'], $action['deadline_time'], 'deadline_time', 'Action', 'actions', 'span', array('emptyText' => "'set time'")) ?>
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
			<?php echo $this->renderElement('../actions/add_link') ?>
		</div>
		<?php echo $this->renderElement('../actions/add', array('contact_id' => $contact_id)) ?>
		
	<?php endif  ?>
</div>