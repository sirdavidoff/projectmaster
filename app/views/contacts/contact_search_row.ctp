<tr>
	<td valign="top" style="width:60px" class="listRow status<?php echo $contact['Status']['id'] ?>">
		<?php echo $contact['Status']['name'] ?>
	</td>
	<td valign="top" class="listRow">
		<?php echo $html->link($contact['Contact']['name'], '/contacts/view/' . $contact['Contact']['id']) ?>
	</td>
	<td valign="top" class="listRow">
		<?php if ($contact['Contacttype']['id'] == "1"): ?>
			<?php echo $contact['Contacttype']['name'] ?>
		<?php else: ?>
			Market <?php echo $contact['Market']['name'] ?>
		<?php endif ?>
	</td>
	<td valign="top" class="listRow">
		<?php echo $contact['Sector']['name'] ?>
	</td>
</tr>