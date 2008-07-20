<tr>
	<td valign="top" style="width:60px" class="meetingRowLight">
		<?php echo $data['Meeting']['time'] ?>
	</td>
	<td valign="top" class="meetingRow" colspan="2">
		<?php echo $html->link("Meeting with " . $data['Contact']['name'], '/contacts/view/' . $data['Contact']['id']) ?> -
		<?php echo $ajaxs->editable($data['Meeting']['id'], $data['Meeting']['with'], 'with', 'Meeting', 'meetings', 'span', array('emptyText' => "'click to add who with'")) ?>
		<?php echo $ajaxs->editable($data['Meeting']['id'], $data['Meeting']['text'], 'text', 'Meeting', 'meetings', 'div', array('emptyText' => "'click to add note'")) ?>
		<?php echo $this->renderElement('../meetings/reschedule_box', array('id' => $data['Meeting']['id'], 'data' => $data)) ?>
	</td>
	<td valign="top" class="meetingRowLight" style="text-align:right">
		<?php echo $this->renderElement('../meetings/reschedule_link', array('id' => $data['Meeting']['id'])) ?>
	</td>
</tr>