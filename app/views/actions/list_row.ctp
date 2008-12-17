<tr>
	<td valign="top" style="width:60px" class="actionRowLight">
		<?php if (!isset($isOverdue) || !$isOverdue): ?>
			<?php if (isset($data['Action']['deadline_time'])): ?>
				<?php echo $data['Action']['deadline_time'] ?>
			<?php else: ?>
				&nbsp;
			<?php endif ?>
		<?php else: ?>
			<?php if (isset($data['Action']['deadline_date'])): ?>
				<?php echo $format->shortDate($data['Action']['deadline_date']) ?>
			<?php else: ?>
				&nbsp;
			<?php endif ?>
		<?php endif ?>
		
	</td>
	<td valign="top" class="actionRow">
		<?php echo $html->link($data['Contact']['name'], '/contacts/view/' . $data['Contact']['id']) ?> -
		<?php echo $ajaxs->editable($data['Action']['id'], $data['Action']['text'], 'text', 'Action', 'actions', 'span', array('rows' => 2, 'emptyText' => "'click to add note'")) ?>
		<?php echo $this->renderElement('../actions/reschedule_box', array('id' => $data['Action']['id'], 'data' => $data)) ?>
		<?php if ($print): ?>
			<div>
				<?php echo $this->renderElement('../contacts/contact_list_phone', array('contact' => $data)) ?>
			</div>
		<?php endif ?>
	</td>
	
	<td valign="top" class="actionRow">
		<?php if (!isset($multiProj)): ?>
			<?php 
			if(isset($data['User']['id'])) 
			{
				$uid = $data['User']['id'];
				$userName = $allUserList[$uid];
			} else {
				$userName = 'everyone';
			}
			?>
			<?php echo $ajaxs->editable($data['Action']['id'], $userName, 'user_id', 'Action', 'actions', 'span', array('collection' => $userList)) ?>
		<?php else: ?>
			<?php echo $allProj[$data['Contact']['project_id']] ?>
		<?php endif ?>
	</td>
	<?php if (!$print): ?>
	<td valign="top" class="actionRowLight" style="text-align:right; width:130px">
		<?php if ($data['Action']['completed'] == 0): ?>
			<?php echo $html->link('done', '/actions/done/' . $data['Action']['id'] . "/true") ?>
		<?php endif ?>
		<?php echo $this->renderElement('../actions/reschedule_link', array('id' => $data['Action']['id'])) ?>
		
	</td>
	<?php endif ?>
</tr>