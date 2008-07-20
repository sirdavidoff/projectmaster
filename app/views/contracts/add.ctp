<?php 
// The already present contracts and any contract that is in the process of being added (but whose
// validation failed) will both use the $this->data['Contract'] array. For this reason, we put the
// editing one in $editContract
if(isset($editContract))
{
	$forms->data['Contract'] = $editContract; 
}
?>

<div id="addContractContainer" style="<?php if(!$forms->areErrors('Contract')) echo "display:none;" ?> border:1px solid gray; padding:10px">
	<h3>Add a Contract</h3>
	<?php echo $ajax->form() ?>
		<?php echo $forms->hidden("Contract.contact_id", array("value" => $contact_id)) ?>
		<table  cellspacing="0" cellpadding="0" width="100%">
			<tr>
				<td valign="top" colspan="4">
					<div>
						<div style="float:right">
							Cost (€)<br />
							<?php echo $forms->text("Contract.cost", array('style' => 'width:70px')) ?>
						</div>
						<div style="margin-right: 90px">
							Space<br />
							<?php echo $forms->text("Contract.space", array('style' => 'width:95%')) ?>
						</div>
					</div>
					<div>
						<?php echo $forms->error('Contract.space') ?>
						<?php echo $forms->error('Contract.cost') ?>
					</div>
				</td>
			</tr>
			<tr>
				<td valign="top" colspan="4">
					Notes<br />
					<?php echo $forms->textarea("Contract.notes", array("rows" => 2)) ?>
					<?php echo $forms->error('Contract.notes') ?>
				</td>
			</tr>
			<tr>
				<td valign="top" style="padding-top:6px;padding-right:10px">
					Signed&nbsp;on<br />
					<?php echo $forms->calendarText('Contract.signed_on_readable', array('defaultValue' => 'dd/mm/yy')) ?>
				</td>
				<td valign="top" style="padding-top:6px;padding-right:10px">
					Payment&nbsp;by*<br />
					<?php echo $forms->calendarText('Contract.payment_by_readable', array('defaultValue' => 'dd/mm/yy')) ?>
				</td>
				<td colspan="2" valign="bottom" align="right" style="padding-top:6px;width:90%">
					<?php echo $ajaxs->submit("Add Contract", array('url' => '/Contracts/add', 'update'=>'addContract')) ?>
				</td>
			</tr>
		</table>
		<?php /* TODO: The following line produces a javascript error as it looks for a signed_on field */ ?>
		<?php echo $forms->error('Contract.signed_on') ?>
		<?php echo $forms->error('Contract.payment_by_readable') ?>
		<div style="padding-top: 6px">
			* Leave the payment date blank for payment upon publication
		</div>
	<?php echo "</form>" ?>
</div>