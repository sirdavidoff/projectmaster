<?php echo $forms->includeCalendar() ?>

<table style="width:100%" cellspacing="0" cellpadding="0">
	<tr>
		<td valign="top">
			<h1>
				<?php echo $ajaxs->editable($this->data['User']['id'], $this->data['User']['first_name'], 'first_name', 'User', 'users', 'span', array('class' => 'inlineEditable')) ?>
				<?php echo $ajaxs->editable($this->data['User']['id'], $this->data['User']['last_name'], 'last_name', 'User', 'users', 'span', array('class' => 'inlineEditable')) ?>
			<?php if ($this->data['User']['is_active'] != 1): ?>
				<span class="annotation">(Deactivated)</span>
			<?php endif ?>
			</h1>
		</td>
		<td valign="bottom" align="right">
			<?php if ($this->data['User']['id'] == $cauth->user('id')): ?>
				<?php echo $html->link('Change password', array('controller' => 'users', 'action' => 'changePassword')) ?>
				<br />
			<?php else: ?>
				<?php if (!$this->data['User']['is_active']): ?>
					<?php echo $html->link('Activate', array('controller' => 'users', 'action' => 'activate/' . $this->data['User']['id'])) ?>
				<?php else: ?>
					<?php echo $html->link('Reset password', array('controller' => 'users', 'action' => 'resetPassword/' . $this->data['User']['id']), null, "If you change this person's password, they will not be able to log in until you tell them the new one. Are you sure you want to do this?") ?>
					<br />
					<?php echo $html->link('Deactivate', array('controller' => 'users', 'action' => 'deactivate/' . $this->data['User']['id']), null, "If you deactivate this person they will not be allowed to log in. Are you sure you want to do this?") ?>
				<?php endif ?>
			<?php endif ?>
		</td>
	</tr>
</table>


<div class="h1b">
	<?php echo $ajaxs->editable($this->data['User']['id'], $this->data['User']['email'], 'email', 'User', 'users', 'span') ?>
</div>


<table style="width:100%" cellspacing="0" cellpadding="0">
	<tr>
		<td valign="top" style="width:66%">

			<?php if ((!isset($events) && !isset($datelessActions) && !isset($overdueActions)) || (count($events) < 1 && count($datelessActions) < 1 && (!isset($overdueActions) || count($overdueActions) < 1))): ?>
	
				<h2>Agenda</h2>
				<?php if ($this->data['User']['id'] == $cauth->user('id')): ?>
					<?php echo "You have" ?>
				<?php else: ?>
					<?php echo $this->data['User']['first_name'] ?> has
				<?php endif ?>
				no agenda for the next week.
	
			<?php else: ?>
				<table  cellspacing="0" cellpadding="0" style="width:100%">
					<?php echo $this->renderElement('../agenda/list_overdue_actions', array('actions' => $overdueActions)) ?>

					<?php echo $this->renderElement('../agenda/list_events', array('events' => $events)) ?>

					<?php echo $this->renderElement('../agenda/list_dateless_actions', array('actions' => $datelessActions)) ?>
				</table>
			<?php endif ?>
		
		</td>
		<td valign="top" style="width:33%; padding-left:10px">
			<table style="width:100%" cellspacing="0" cellpadding="0">
				<tr>
					<td valign="top"><h2>Projects</h2></td>
					<td  valign="bottom" align="right">
						<?php if (count($otherProjList) > 0): ?>
							<?php //echo $this->renderElement('../team_members/add_link', array('title' => 'Join new project')) ?>
						<?php endif ?>
					</td>
				</tr>
			</table>
			
			<?php echo $this->renderElement('../team_members/add', array('userId' => $this->data['User']['id'], 'title' => 'Join New Project')) ?>
			<?php if (count($projects) < 1): ?>
				<?php if ($this->data['User']['id'] == $cauth->user('id')): ?>
					<?php echo "You have" ?>
				<?php else: ?>
					<?php echo $this->data['User']['first_name'] ?> has
				<?php endif ?>
				not been associated with any projects.
			<?php endif ?>
			<table  cellspacing="0" cellpadding="0" style="width:100%">
				<?php foreach ($projects as $p): ?>
					<?php echo $this->renderElement('../projects/project_list_row', array('project' => $p, 'order' => 'start')) ?>
				<?php endforeach ?>
			</table>
		</td>
	</tr>
</table>
<br />
<br />

<?php //echo $html->link('User List', array('controller' => 'users', 'action' => 'listAll')) ?><br />
<?php //echo $html->link('Project List', array('controller' => 'projects', 'action' => 'listAll')) ?><br />