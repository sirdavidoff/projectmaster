<div id="addMemberContainer" style="<?php if(!$forms->areErrors('TeamMember')) echo "display:none;" ?> border:1px solid gray; padding:10px">
	<?php if (!isset($title)): ?>
		<?php $title = "Add a Team Member" ?>
	<?php endif ?>
	<h3><?php echo $title ?></h3>
	<?php echo $ajax->form() ?>
		<?php if (!isset($multiProj)): ?>
			<?php echo $forms->hidden("TeamMember.project_id", array("value" => $pid)) ?>
		<?php else: ?>
			<?php echo $forms->hidden("TeamMember.user_id", array("value" => $userId)) ?>
		<?php endif ?>
		
		
		<table style="width:100%" cellspacing="0" cellpadding="0">
			<tr>
				<td valign="top" style="width:40%; padding-top:6px;padding-right:10px">
					<?php if (!isset($multiProj)): ?>
						Name<br />
						<?php echo $forms->select('TeamMember.user_id', $otherUsersList, null, array('class' => 'addMemberSelect'), 'Choose...')?>
					<?php else: ?>
						Project<br />
						<?php echo $forms->select('TeamMember.project_id', $otherProjList, null, array('class' => 'addMemberSelect'), 'Choose...')?>
					<?php endif ?>
				</td>
				<td valign="top" style="width:60%; padding-top:6px;padding-right:10px">
					Phone Number<br />
					<?php echo $forms->text('TeamMember.tel', array('size' => '40'))?>
				</td>
			</tr>
			<tr>
				<td valign="top" colspan="2">
					<?php echo $forms->error('TeamMember.user_id') ?>
				</td>
			</tr>
			<tr>
				<td valign="top" style="padding-top:6px;padding-right:10px">
					Role<br />
					<?php echo $forms->select('TeamMember.role_id', $roleList, null, array('class' => 'addMemberSelect'), 'Choose...')?>
				</td>
				<td valign="top" style="padding-top:6px;padding-right:10px">
					Email<br />
					<?php echo $forms->text('TeamMember.email', array('size' => '40'))?>
				</td>
			</tr>
			<tr>
				<td valign="top" colspan="2">
					<?php echo $forms->error('TeamMember.role_id') ?>
					<?php echo $forms->error('TeamMember.email') ?>
				</td>
			</tr>
			<tr>
				<td valign="top" colspan="2" style="padding-top:6px;padding-right:10px; text-align:right">
					<?php echo $ajaxs->submit("Add Member", array('url' => '/projects/addTeamMember', 'update'=>'addMember')) ?>
				</td>
			</tr>
		</table>
		
	<?php echo "</form>" ?>
</div>