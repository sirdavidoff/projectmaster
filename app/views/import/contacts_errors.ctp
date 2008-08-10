<h1><?php echo $pageTitle;?></h1>
<br />
<?php if (count($errors) == count($data)): ?>
	There were errors found in all the rows of your file. Please adjust your file accordingly and 
	<?php echo $html->link('try again', array('controller' => 'import', 'action' => "contacts/$pid")) ?>.
<?php else: ?>
	There were errors found in <?php echo count($errors) ?> of the <?php echo count($data) ?> rows of your file.
	You can continue importing, but the rows with errors will be skipped.
	Alternatively, you can edit your file and <?php echo $html->link('try again', array('controller' => 'import', 'action' => "contacts/$pid")) ?>.
	<br /><br />
	<?php echo $forms->create(null, array('type' => 'file', 'url' => array('action' => 'contacts'))) ?>
		<fieldset style="display:none;">
			<input type="hidden" name="data[sessionKey]" value="<?php echo $sessionKey ?>" />
			<input type="hidden" name="data[ignoreErrors]" value="1" />
			<input type="hidden" name="data[pid]" value="<?php echo $pid ?>" />
		</fieldset>
	<?php echo $forms->end('Import Contacts (Ignoring rows with errors)') ?>
<?php endif ?>

<h2>Errors</h2>

<div class="fileUploadBox">
	<table cellspacing="5" cellpadding="0">
		<?php foreach ($errors as $rowId => $rowErrors): ?>
		<tr>
			<td valign="top" style="padding-right:10px">Row <?php echo $rowId+1 ?></td>
			<td valign="top" style="padding-bottom:10px">
				<?php if (isset($rowErrors['Contact'])): ?>
					<?php foreach ($rowErrors['Contact'] as $error): ?>
					<div class="error">
						<?php echo $error ?>
					</div>
					<?php endforeach ?>
				<?php endif ?>
				<?php if (isset($rowErrors['Person'])): ?>
					<?php foreach ($rowErrors['Person'] as $personNumber => $personErrors): ?>
						<?php foreach ($personErrors as $error): ?>
						<div class="error">
							Person <?php echo $personNumber+1 ?>: <?php echo $error ?>
						</div>
						<?php endforeach ?>
					<?php endforeach ?>
				<?php endif ?>
			</td>
		</tr>
		<?php endforeach ?>
	</table>
</div>