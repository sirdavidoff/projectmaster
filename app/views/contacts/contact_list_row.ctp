<tr>
	<td valign="top" style="width:60px" class="listRow status<?php echo $contact['Status']['id'] ?>">
		<?php echo $contact['Status']['name'] ?>
	</td>
	<td valign="top" class="listRow">
		<?php echo $html->link($contact['Contact']['name'], '/contacts/view/' . $contact['Contact']['id']) ?>
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
</tr>