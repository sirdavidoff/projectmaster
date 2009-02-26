<tr>
	<td valign="top" style="width:60px" class="listRow status<?php echo $contact['Status']['id'] ?>">
		<?php echo $ajaxs->editable($contact['Contact']['id'], $contact['Status']['name'], 'status_id', 'Contact', 'contacts', 'span', array('collection' => $statusList)) ?>
	</td>
	<td valign="top" class="listRow">
		<?php echo $html->link($contact['Contact']['name'], '/contacts/view/' . $contact['Contact']['id']) ?>
		<?php if (count($contact['Action'] > 0)): ?>
			<?php $actionFound = false ?>
			<?php foreach ($contact['Action'] as $action): ?>
				<?php if (!$actionFound && $action['completed'] == 0): ?>
					<span class="printContent0">
						- <?php echo substr($action['text'], 0, 70) ?>
						<?php if (strlen($action['text']) > 70) echo "..." ?>
					</span>
					<?php $actionFound = true ?>
				<?php endif ?>
			<?php endforeach ?>
		<?php endif ?>
	</td>
	<td valign="middle" class="listRow">
		<?php echo $this->renderElement('../contacts/meeting_indicator', Array('contact' => $contact)) ?>
	</td>
	<?php if ($order != 'type'): ?>
		<td valign="top" class="listRow">
			<?php if ($contact['Contacttype']['id'] == 1): ?>
				<?php echo $contact['Contacttype']['name'] ?>
			<?php else: ?>
				Market <?php echo $contact['Market']['name'] ?>
			<?php endif ?>
		</td>
	<?php endif ?>
	<?php if ($order != 'sector'): ?>
		<td valign="top" class="listRow">
			<?php echo $contact['Sector']['name'] ?>
		</td>
	<?php endif ?>
	<td valign="top" style="width:15px;" class="listRow">
		<?php echo $forms->multiCheckBox('ids', array('value' => $contact['Contact']['id'], 'class' => 'multiCheck', 'onchange' => "javascript:(toggleBackground(this))")) ?>
	</td>
</tr>