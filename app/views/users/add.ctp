<h1><?php echo $pageTitle;?></h1>

<div class="outerFormBox">

	<?php echo $forms->create('User', array('action' => 'add')) ?>
	
	<table cellspacing="0" cellpadding="0">
		<tr>
			<td valign="top">
				<div class="formElementTitle"><?php echo "First Name" ?></div>
				<?php echo $forms->text('User.first_name', array('size' => '40', 'focus' => true))?>
				<?php echo $forms->error('User.first_name') ?>
			</td>
		</tr>
		<tr>
			<td valign="top">
				<div class="formElementTitle"><?php echo "Last Name" ?></div>
				<?php echo $forms->text('User.last_name', array('size' => '40'))?>
				<?php echo $forms->error('User.last_name') ?>	
			</td>
		</tr>
		<tr>
			<td valign="top">
				<div class="formElementTitle"><?php echo "Email Address" ?></div>
				<?php echo $forms->text('User.email', array('size' => '40'))?>
				<?php echo $forms->error('User.email') ?>	
			</td>
		</tr>
		<tr>
			<td valign="top">
				<br />
				<?php echo $forms->submit('Add User') ?>
			</td>
		</tr>
	</table>
	
	</form>

</div>

