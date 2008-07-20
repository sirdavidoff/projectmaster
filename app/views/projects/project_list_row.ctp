<?php
	if($project['Project']['project_status_id'] == 1) {
		$duration = $format->duration($project['Project']['started_on'], date('Y-m-d'), 'd');
	} else {
		$duration = $format->duration($project['Project']['started_on'], $project['Project']['finished_on'], 'd');
	}
?>

<tr>
	<td valign="top" class="listRow">
		<div class="h1c">
			<?php echo $html->link($format->projectName($project), array('controller' => 'projects', 'action' => "view/" . $project['Project']['id'])) ?>
		</div>
	</td>
	<?php if ($order != 'type' && !isset($multiProj)): ?>
		<td valign="top" class="listRow">
			<?php if (count($project['TeamMember']) > 0): ?>
				<?php $started = false ?>
				<?php foreach ($project['TeamMember'] as $value): ?>
					<?php if ($value['status'] == 1): ?>
						<?php if ($started): ?>
							<?php echo "," ?>
							<?php $started = true ?>
						<?php endif ?>
						<?php echo $html->link($userList[$value['user_id']], array('controller' => 'users', 'action' => 'view/' . $value['user_id'])) ?>
						<?php $started = true ?>
					<?php endif ?>
				<?php endforeach ?>
				<?php $started = false ?>
			<?php endif ?>
			&nbsp;
		</td>
	<?php endif ?>
	<?php if (!isset($multiProj)): ?>
		<td valign="top" class="listRow">
			<?php $s =  $project['Project']['started_on'] ?>
			<?php if ($format->isInFuture($project['Project']['started_on'])): ?>
				<?php echo "Starts" ?>
			<?php else: ?>
				<?php echo "Started" ?>
			<?php endif ?>
			<?php echo date('M y', mktime(0, 0, 0, substr($s, 5, 2), substr($s, 8, 2), substr($s, 0, 4))) ?>
		</td>
		<td valign="top" class="listRow">
			<?php if ($format->isInFuture($project['Project']['started_on'])): ?>
				Not yet started
			<?php else: ?>
				<?php echo $duration ?> days
				<?php if ($project['Project']['project_status_id'] == 1): ?>
					and counting
				<?php endif ?>
			<?php endif ?>
		</td>
	<?php else: ?>
		<td valign="top" class="listRow">
			<?php foreach ($project['TeamMember'] as $member): ?>
				<?php if ($member['user_id'] == $this->data['User']['id']): ?>
					<?php echo $member['Role']['name'] ?>
				<?php endif ?>
			<?php endforeach ?>
		</td>
	<?php endif ?>
	
	<td valign="top" class="listRow">
		<?php echo $project['ProjectStatus']['name'] ?>
	</td>
</tr>