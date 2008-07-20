<?php echo $forms->includeCalendar() ?>

<h1><?php echo $pageTitle;?></h1>

<?php echo $forms->create('Project', array('action' => "add")) ?>

<table  cellspacing="0" cellpadding="0" style="width:500px">
	
	<tr>
		<td valign="top" style="width:150px">
			<div class="formElementTitle"><?php echo "Media" ?></div>
			<?php echo $forms->select('Project.media_id', $mediaList, null, array('style' => 'width:140px'), 'Choose...')?>
		</td>
		<td valign="top" style="width:250px">
			<div class="formElementTitle"><?php echo "Subject" ?></div>
			<?php echo $forms->text('Project.subject', array('size' => '40', 'style' => 'width: 235px'))?>
		</td>
		<td valign="top" style="width:100px">
			<div class="formElementTitle"><?php echo "Starts On" ?></div>
			<?php echo $forms->calendarText('Project.started_on_readable', array('defaultValue' => date('d/m/y'))) ?>
		</td>
	</tr>
	<tr>
		<td valign="top" colspan="4">
			<?php echo $forms->error('Project.media_id') ?>
			<?php echo $forms->error('Project.subject') ?>
			<?php echo $forms->error('Project.started_on_readable') ?>
			
			<div>
				<br />
				<?php echo $forms->submit("Add Project") ?>
			</div>
		</td>
	</tr>
	
</table>

</form>
