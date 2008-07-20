<div id="addContract">	
	<?php if (isset($this->data['Contract']) && count($this->data['Contract']) > 0): ?>
		
		<table style="width:100%" cellspacing="0" cellpadding="0">
			<tr>
				<td valign="top"><h2>Contracts</h2></td>
				<td valign="bottom" style="padding-bottom:11px" align="right">
					<?php echo $this->renderElement('../contracts/add_link') ?>
				</td>
			</tr>
		</table>

		<?php echo $this->renderElement('../contracts/add', array('contact_id' => $contact_id, 'editContract' => $editContract)) ?>
		
		<div class="contractsBox">
			<ul id="contracts" style="margin:0px">
				<?php foreach ($this->data['Contract'] as $contract): ?>
					<li id='<?php echo "contract_" . $contract['id'] ?>' class="contractBox<?php if (isset($contract['paid_on'])) echo 'Completed' ?>">
						
						<table style="width:100%" cellspacing="0" cellpadding="0">
							<tr>
								<td valign="top" style="padding:10px">
									<span class="shortDate">
										<?php echo $ajaxs->editable($contract['id'], $contract['cost'], 'cost', 'Contract', 'contracts', 'span', array('emptyText' => "'Set cost'")) ?>€
									</span>
									for
									<?php echo $ajaxs->editable($contract['id'], $contract['space'], 'space', 'Contract', 'contracts', 'span', array('emptyText' => "'set space'")) ?>
									<?php echo $ajaxs->editable($contract['id'], $contract['notes'], 'notes', 'Contract', 'contracts', 'div', array('rows' => 2, 'emptyText' => "'add note'")) ?>
									
									<?php if (!isset($print) || !$print): ?>
										<?php echo $ajaxs->link('paid', '/contracts/pay/' . $contract['id'], array('update' => 'addContract')) ?>
										<?php echo $ajaxs->link('remove', '/contracts/delete/' . $contract['id'], array('update' => 'addContract'), "If you remove this contract it will be deleted forever. Are you sure you want to do this?") ?>
									<?php endif ?>
								</td>
								<td valign="top" class="contractDateCell">
									<?php if (isset($contract['paid_on'])): ?>
										<div class="shortDate">Paid</div>
										<?php echo $format->shortDate($contract['paid_on']) ?>
									<?php else: ?>
									Payment due
									<div class="shortDate"><?php echo $ajaxs->editable($contract['id'], $format->shortDate($contract['payment_by']), 'payment_by_readable', 'Contract', 'contracts', 'span', array('emptyText' => "'On Pub'", 'calendar' => true, 'editValue' => "'".$format->slashDate($contract['payment_by'])."'")) ?></div>
									<?php endif ?>
								</td>
							</tr>
						</table>
					</li>
				<?php endforeach ?>
			</ul>
		</div>
		
	<?php else: ?>
		<div style="text-align:right">
			<?php echo $this->renderElement('../contracts/add_link') ?>
		</div>
		<?php echo $this->renderElement('../contracts/add', array('contact_id' => $contact_id, 'editContract' => $editContract)) ?>
		
	<?php endif  ?>
</div>