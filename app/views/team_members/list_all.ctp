<div id="addMember">

	<table style="width:100%" cellspacing="0" cellpadding="0">
		<tr>
			<td valign="top"><h2>Team</h2></td>
			<td valign="bottom" style="text-align:right;padding-bottom:11px">
				<?php echo $this->renderElement('../team_members/add_link') ?>
			</td>
		</tr>
	</table>
	
	<?php echo $this->renderElement('../team_members/add', array('project_id' => $pid)) ?>
		
	<?php if (isset($members) && count($members) > 0): ?>
		
		<div class="membersBox">
			<ul id="members" style="margin:0px">
				<?php foreach ($members as $member): ?>
					<li id='<?php echo "member_" . $member['TeamMember']['id'] ?>' class="memberBox">
						<div style="float:left; width:30%">
							<div class="h1c">
								<?php echo $format->name($member['User']['first_name'], $member['User']['last_name']); ?>
							</div>
							<?php echo $ajaxs->editable($member['TeamMember']['id'], $member['Role']['name'], 'role_id', 'TeamMember', 'projects', 'span', array('collection' => $roleList)) ?>
							<?php if (!$member['User']['is_active']): ?>
								<?php echo "(Inactive)" ?>
							<?php endif ?>
						</div>
						<div style="float:left; padding-top: 7px; width:50%">
							<?php echo $ajaxs->editable($member['TeamMember']['id'], $member['TeamMember']['tel'], 'tel', 'TeamMember', 'projects', 'span', array('emptyText' => "'add tel'")) ?><br />
							<?php echo $ajaxs->editable($member['TeamMember']['id'], $member['TeamMember']['email'], 'email', 'TeamMember', 'projects', 'span', array('emptyText' => "'add email'")) ?><br />
						</div>
						<div style="float:left; width:20%; text-align: right">
							<?php echo $ajaxs->link('remove', '/projects/removeTeamMember/' . $member['TeamMember']['id'], array('update' => 'addMember'), "If you remove this member they will not be allowed to view the project details. Are you sure you want to do this?") ?>
						</div>
						<div class="sublineMembers">
							<?php /*echo $time->niceShort($member['created']) ?>
							<?php if (isset($member['created_by'])): ?>
								by <?php echo $allUserList[$member['created_by']] ?>
							<?php endif ?>
							<?php if (!$print): ?>
								<?php echo $ajaxs->link('remove', '/members/delete/' . $member['id'], array('update' => 'addMember'), "If you remove this member it will be deleted forever. Are you sure you want to do this?") ?>
							<?php endif*/ ?>
						</div>
						<div style="clear:both"></div>
					</li>
				<?php endforeach ?>
			</ul>
		</div>
		<br />
	
	<?php else: ?>
		There are no team members associated with this project
	<?php endif ?>
</div>