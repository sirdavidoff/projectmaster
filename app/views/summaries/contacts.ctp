<h1><?php echo $pageTitle;?></h1>

<?php echo $forms->create(null, array('type' => 'file', 'url' => array('action' => 'contacts'))) ?>
	<fieldset style="display:none;">
		<input type="hidden" name="data[pid]" value="<?php echo $pid ?>" />
	</fieldset>
	
	<h2>File to Import From</h2>
	The file must be in CSV format. In Excel, go to 'File->Save As...' and choose CSV as the format.
	<br /><br />
	<div class="fileUploadBox">
		<?php echo $forms->input('Contacts.file', array('type' => 'file'));?>
	</div>


	<table style="width:100%" cellspacing="0" cellpadding="0">
		<tr>
			<td valign="top"><h2>Columns in File</h2></td>
			<td valign="bottom" align="right">
				<?php echo $customjs->dynamicOptionListAddLink('Add column') ?>
			</td>
		</tr>
	</table>

	Please specify the order of the columns in your file. To add new columns, click on the 'Add column' link.
	<br /> <br />

	<div class="fieldsBox">
		<ul id="fields" style="margin:0px">

		</ul>
	</div>

	<br />
	<br />
<?php echo $forms->end('Import Contacts') ?>

<?php if(isset($this->data['field'])) 
	{
		$rows = $this->data['field'];
	} else {
		$rows = array();
	}
?>
<?php echo $customjs->dynamicOptionList('fields', $fieldList, array('rows' => $rows, 'innerId' => 'field', 'innerClass' => 'fieldBox')) ?>