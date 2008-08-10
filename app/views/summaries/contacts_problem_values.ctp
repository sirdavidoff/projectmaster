<h1><?php echo $pageTitle;?></h1>
<br />
Some of the contacts in your file have information that doesn't fit in the database. Please choose what they should be changed to.

<?php echo $forms->create(null, array('type' => 'file', 'url' => array('action' => 'contacts'))) ?>
	<fieldset style="display:none;">
		<input type="hidden" name="data[sessionKey]" value="<?php echo $sessionKey ?>" />
		<input type="hidden" name="data[pid]" value="<?php echo $pid ?>" />
	</fieldset>
	
	<table cellspacing="5" cellpadding="0">
		
		<?php foreach ($problemValues as $colId => $valueList): ?>
			<tr>
				<td valign="top" colspan="3"><h2><?php echo $colNames[$colId] ?></h2></td>
			</tr>
			<?php $i = 0; ?>
			<?php foreach ($valueList as $value): ?>
				<tr>
					<td valign="top" style="padding-right:10px"><?php echo $value ?></td>
					<td valign="top" style="padding-right:10px">becomes</td>
					<td valign="top"><?php echo $forms->select("map.$colId.$i", $allowedValues[$colId], null, array('class' => 'addContactSelect'), null) ?></td>
				</tr>
				<?php $i++; ?>
			<?php endforeach ?>
		<?php endforeach ?>
	</table>
<br />	
<?php echo $forms->end('Import Contacts') ?>