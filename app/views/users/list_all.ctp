<table style="width:100%" cellspacing="0" cellpadding="0">
	<tr>
		<td valign="top">
			<h1><?php echo $pageTitle;?></h1>
		</td>
		<td valign="top" style="text-align:right; padding-top:10px">
			<?php echo $html->link('Add New User', array('controller' => 'users', 'action' => 'add')) ?>
		</td>
	</tr>
</table>
<br />

<?php if (count($users) > 0): ?>
	
	<table cellspacing="0" cellpadding="0" style="width:100%">
		<?php $deactivated = false ?>
		<?php foreach ($users as $user): ?>
			<?php if (!$user['User']['is_active'] && !$deactivated): ?>
				<?php $deactivated = true ?>
				<tr>
					<td valign="top" colspan="3">
						<h2>Deactivated Users*</h2>
					</td>
					<td valign="bottom" colspan="2" style="text-align:right; padding-bottom:7px">
						*these users cannot log in
					</td>
				</tr>
			<?php endif ?>
		<tr>
			<td valign="top" class="listRow">
				<div class="h1c">
					<?php echo $html->link($format->name($user['User']['first_name'], $user['User']['last_name']), array('controller' => 'users', 'action' => 'view/' . $user['User']['id'])); ?>
				</div>
			</td>
			<td valign="top" class="listRow">
				<?php $started = false ?>
				<?php foreach ($user['TeamMember'] as $team): ?>
					<?php if (isset($projectList[$team['project_id']]) && $projectStatuses[$team['project_id']] == 1): ?>
						<?php if ($started): ?>
							<?php echo "," ?>
						<?php endif ?>
						<?php echo $html->link($projectList[$team['project_id']], array('controller' => 'projects', 'action' => 'view/' . $team['project_id'])) ?>
						<?php $started = true ?>
					<?php endif ?>
				<?php endforeach ?>
				<?php $started = false ?>
				&nbsp;
			</td>
			<td valign="top" class="listRow">
				<?php if (isset($user['User']['last_login'])): ?>
					<?php echo $format->date($user['User']['last_login']) ?>
				<?php else: ?>
					<?php echo "Never" ?>
				<?php endif ?>
			</td>
			<td valign="top" class="listRow" style="width:120px">
				<?php if ($cauth->user('id') != $user['User']['id'] && $user['User']['is_active']): ?>
					<?php echo $html->link('reset password', array('controller' => 'users', 'action' => 'resetPassword/' . $user['User']['id']), null, "If you change this person's password, they will not be able to log in until you tell them the new one. Are you sure you want to do this?") ?>
				<?php elseif($cauth->user('id') == $user['User']['id']): ?>
					<?php echo $html->link('change password', array('controller' => 'users', 'action' => 'changePassword')) ?>
				<?php else: ?>
					&nbsp;
				<?php endif ?>
			</td>
			<td valign="top" class="listRow" style="text-align:right;width:55px">
				<?php if ($cauth->user('id') != $user['User']['id']): ?>
					<?php if (!$user['User']['is_active']): ?>
						<?php echo $html->link('activate', array('controller' => 'users', 'action' => 'activate/' . $user['User']['id'])) ?>
					<?php else: ?>
						<?php echo $html->link('deactivate', array('controller' => 'users', 'action' => 'deactivate/' . $user['User']['id']), null, "If you deactivate this person they will not be allowed to log in. Are you sure you want to do this?") ?>
					<?php endif ?>
				<?php endif ?>
			</td>
		</tr>
		<?php endforeach ?>
	</table>
	
<?php endif ?>