<table style="width:100%" cellspacing="0" cellpadding="0">
	<tr>
		<td valign="top"><h1><?php echo $pageTitle;?></h1></td>
		<td valign="bottom" align="right">
			Order:
			<?php if ($order != 'sector'): ?>
				<?php echo $html->link('By sector', Array('action' => "listAll/$pid/sector/$assignee")) ?>
			<?php else: ?>
				By sector
			<?php endif ?>
			|
			<?php if ($order != 'status'): ?>
				<?php echo $html->link('By status', Array('action' => "listAll/$pid/status/$assignee")) ?>
			<?php else: ?>
				By status
			<?php endif ?>
			|
			<?php if ($order != 'type'): ?>
				<?php echo $html->link('By market', Array('action' => "listAll/$pid/type/$assignee")) ?>
			<?php else: ?>
				By market
			<?php endif ?>
			
			<br />
			
			<?php if (count($teamUserList) > 0): ?>
				Assigned to:
				<?php if ($assignee != 'any'): ?>
					<?php echo $html->link('Any', Array('action' => "listAll/$pid/$order/any")) ?>
				<?php else: ?>
					Any
				<?php endif ?>
				|
				<?php foreach ($teamUserList as $key => $name): ?>
					<?php if ($assignee != $key): ?>
						<?php echo $html->link($name, Array('action' => "listAll/$pid/$order/$key")) ?>
					<?php else: ?>
						<?php echo $name ?>
					<?php endif ?>
					|	
				<?php endforeach ?>
				<?php if ($assignee != 'noone'): ?>
					<?php echo $html->link('No-one', Array('action' => "listAll/$pid/$order/noone")) ?>
				<?php else: ?>
					No-one
				<?php endif ?>
				<br />
			<?php endif ?>
			
			<?php echo $html->link('List phone numbers', Array('action' => "phoneNumbers/$pid")) ?>
			
		</td>
	</tr>
</table>

<?php if (!isset($contacts) || count($contacts) < 1): ?>
	There are currently no contacts to display. Why don't you <?php echo $html->link('add some', "/contacts/add/$pid") ?>?
	
<?php else: ?>
	
	<?php 
		switch($order)
		{
			case 'status':
				$criterion1 = 'Status';
				$criterion2 = 'Status';
				break;
			case 'sector':
				$criterion1 = 'Sector';
				$criterion2 = 'Sector';
				break;
			default:
			case 'type':
				$criterion1 = 'Contacttype';
				$criterion2 = 'Market';
		}
	?>
	
	<?php $currentValue1 = ''; ?>
	<?php $currentValue2 = ''; ?>
	
	<?php echo $this->renderElement('../contacts/contact_multi_action_start') ?>
	
	<table  cellspacing="0" cellpadding="0" style="width:100%">
		<?php $isFirstHeading = true ?>
		<?php foreach ($contacts as $c): ?>
			<?php if ($currentValue1 != $c[$criterion1]['name'] || $currentValue2 != $c[$criterion2]['name']): ?>
				<?php $oldValue1 = $currentValue1 ?>
				<?php $currentValue1 = $c[$criterion1]['name'] ?>
				<?php $currentValue2 = $c[$criterion2]['name'] ?>
				
				<?php // Don't create a new header if we're ordering by type and we're in the 'opener' or 'other' sections ?>
				<?php if ($order != 'type' || $c[$criterion1]['id'] == "2" || $currentValue1 != $oldValue1): ?>
				<tr>
					<td colspan="3">
						<?php if ($isFirstHeading): ?>
							<h2 style="padding-top:0px;">
							<?php $isFirstHeading = false ?>
						<?php else: ?>
							<h2>
						<?php endif ?>
							<?php if ($order != 'type' || $c[$criterion1]['id'] == "1" || $c[$criterion1]['id'] == "3"): ?>
								<?php echo $currentValue1 ?>
							<?php else: ?>
								Market <?php echo $currentValue2 ?>
							<?php endif ?>
						</h2>
					</td>
				</tr>
				<?php endif ?>
			<?php endif ?>
			
			<?php echo $this->renderElement('../contacts/contact_list_row', array('contact' => $c, 'order' => $order)) ?>
		
		<?php endforeach ?>
	</table>
	
	<?php echo $this->renderElement('../contacts/contact_multi_action_end') ?>
	
	<br />
	<div align="right">
		<?php echo $this->renderElement('../contacts/meeting_indicator_key') ?>
	</div>
	

<?php endif ?>