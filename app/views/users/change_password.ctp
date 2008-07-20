<h1><?php echo $pageTitle ?></h1>
<br />
<div class="outerFormBox">

	<?php echo $forms->create('User', array('action' => 'changePassword')) ?>

	<table cellspacing="0" cellpadding="0">
		<tr>
			<td valign="top">
				<div class="formElementTitle"><?php echo "Current password" ?></div>
				<?php echo $forms->password('User.old_passwd', array('size' => '40', 'focus' => true))?>
				<?php echo $forms->error('User.old_passwd') ?>	
			</td>
		</tr>
		<tr>
			<td valign="top">
				<div class="formElementTitle"><?php echo "New password" ?></div>
				<?php echo $forms->password('User.passwd', array('size' => '40'))?>
				<?php echo $forms->error('User.passwd') ?>	
			</td>
		</tr>
		<tr>
			<td valign="top">
				<div class="formElementTitle"><?php echo "Re-type new password" ?></div>
				<?php echo $forms->password('User.passwd2', array('size' => '40'))?>
				<?php echo $forms->error('User.passwd2') ?>	
			</td>
		</tr>
		<tr>
			<td valign="top">
				<br />
				<?php echo $forms->submit('Change Password') ?>
			</td>
		</tr>
	</table>

	</form>

</div>

