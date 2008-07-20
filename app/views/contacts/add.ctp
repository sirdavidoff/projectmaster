<h1><?php echo $pageTitle;?></h1>

<?php echo $forms->create('Contact', array('action' => "add/$pid")) ?>
<?php echo $forms->hidden("Contact.project_id", array("value" => $pid)) ?>

<table  cellspacing="0" cellpadding="0" style="width:500px">
	<tr>
		<td valign="top" colspan="4">
			<div class="formElementTitle"><?php echo "Name" ?></div>
			<?php echo $forms->text('Contact.name', array('size' => '40'))?>
			<?php echo $forms->error('Contact.name') ?>
		</td>
	</tr>
	<tr>
		<td valign="top" style="width:25%">
			<div class="formElementTitle"><?php echo "Type" ?></div>
			<?php echo $forms->select('Contact.contacttype_id', $contacttypeList, null, array('class' => 'addContactSelect'), 'Choose...')?>
		</td>
		<td valign="top" style="width:25%">
			<div class="formElementTitle"><?php echo "Sector" ?></div>
			<?php echo $forms->select('Contact.sector_id', $sectorList, null, array('class' => 'addContactSelect'), 'Choose...')?>
		</td>
		<td valign="top" style="width:25%">
			<div class="formElementTitle"><?php echo "Market" ?></div>
			<?php echo $forms->select('Contact.market_id', $marketList, null, array('class' => 'addContactSelect'), 'Choose...')?>
		</td>
		<td valign="top" style="width:25%">
			<div class="formElementTitle"><?php echo "Status" ?></div>
			<?php echo $forms->select('Contact.status_id', $statusList, null, array('class' => 'addContactSelect'), 'Choose...')?>
		</td>
	</tr>
	<tr>
		<td valign="top" colspan="4">
			<?php echo $forms->error('Contact.contacttype_id') ?>
			<?php echo $forms->error('Contact.sector_id') ?>
			<?php echo $forms->error('Contact.market_id') ?>
			<?php echo $forms->error('Contact.status_id') ?>
			
			<div class="formElementTitle"><?php echo "Website" ?></div>
			<?php echo $forms->text('Contact.website', array('size' => '40'))?>
			<?php echo $forms->error('Contact.website') ?>
		</td>
	</tr>
	<tr>
		<td valign="top" colspan="4">
			<div class="formElementTitle"><?php echo "Address" ?></div>
			<?php echo $forms->text('Contact.address', array('size' => '40'))?>
			<?php echo $forms->error('Contact.address') ?>
		</td>
	</tr>
	<tr>
		<td valign="top" colspan="4">
			<div class="formElementTitle"><?php echo "General Tel" ?></div>
			<?php echo $forms->text('Contact.tel', array('size' => '40'))?>
			<?php echo $forms->error('Contact.tel') ?>
		</td>
	</tr>
	<tr>
		<td valign="top" colspan="4">
			<div class="formElementTitle"><?php echo "General Fax" ?></div>
			<?php echo $forms->text('Contact.fax', array('size' => '40'))?>
			<?php echo $forms->error('Contact.fax') ?>
		</td>
	</tr>
	<tr>
		<td valign="top" colspan="4">
			<div class="formElementTitle"><?php echo "General Email" ?></div>
			<?php echo $forms->text('Contact.email', array('size' => '40'))?>
			<?php echo $forms->error('Contact.email') ?>
		</td>
	</tr>
	<tr>
		<td valign="top" colspan="2" style="width:50%">
			<div class="formElementTitle"><?php echo "Revenue (last year)" ?></div>
			<?php echo $forms->text('Contact.revenue', array('size' => '40'))?>
			<?php echo $forms->error('Contact.revenue') ?>
		</td>
		<td valign="top" colspan="2" style="width:50%">
			<div class="formElementTitle"><?php echo "Growth (last year)" ?></div>
			<?php echo $forms->text('Contact.growth', array('size' => '40'))?>
			<?php echo $forms->error('Contact.growth') ?>
		</td>
	</tr>
</table>

<div class="formElementTitle" style="width:500px" align="right">
	<?php echo $forms->end('Add Contact') ?>
</div>
