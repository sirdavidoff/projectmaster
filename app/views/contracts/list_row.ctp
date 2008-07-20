<tr>
	<td valign="top" style="width:60px" class="contractRowLight">
		&nbsp;
	</td>
	<td valign="top" class="contractRow" colspan="2">
		<?php echo $html->link($data['Contact']['name'], '/contacts/view/' . $data['Contact']['id']) ?>
		- payment of contract due
		<?php echo $ajaxs->editable($data['Contract']['id'], $data['Contract']['notes'], 'notes', 'Contract', 'contracts', 'div', array('emptyText' => "'click to add note'")) ?>
		<?php echo $this->renderElement('../contracts/reschedule_box', array('id' => $data['Contract']['id'], 'data' => $data)) ?>
	</td>
	<td valign="top" class="contractRowLight" style="text-align:right">
		<?php if ($data['Contract']['paid_on'] == null): ?>
			<?php echo $html->link('paid', '/contracts/pay/' . $data['Contract']['id']) ?>
		<?php endif ?>
		<?php echo $this->renderElement('../contracts/reschedule_link', array('id' => $data['Contract']['id'])) ?>
	</td>
</tr>