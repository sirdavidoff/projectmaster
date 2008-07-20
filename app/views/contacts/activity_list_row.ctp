<tr>
	<td valign="top" style="width:60px" class="listRow status<?php echo $contact['Contact']['historical_status'] ?>">
		<?php echo $statuses[$contact['Contact']['historical_status']] ?>
	</td>
	<td valign="top" class="listRow">
		<?php echo $html->link($contact['Contact']['name'], '/contacts/view/' . $contact['Contact']['id']) ?>
	</td>
	<td valign="top" class="listRow">
		
		<?php 
			$dayStart = $day . " 00:00:00";
			$dayEnd = $day . " 23:59:59"; 
		?>
		
		<?php if (isset($contact['Contract']) && count($contact['Contract']) > 0): ?>
			<?php foreach ($contact['Contract'] as $contract): ?>
				<?php if ($contract['paid_on'] == $day): ?>
					Contract paid (<?php echo $contract['cost'] ?>€ for <?php echo $contract['space'] ?>)
					<br />
				<?php endif ?>
				<?php if($contract['signed_on'] == $day): ?>
					Signed contract for <?php echo $contract['cost'] ?>€ for <?php echo $contract['space'] ?>
				<?php endif ?>
			<?php endforeach ?>
		<?php endif ?>
		
		<?php if (isset($contact['Meeting']) && count($contact['Meeting']) > 0): ?>
			<?php foreach ($contact['Meeting'] as $meeting): ?>
				<?php if ($meeting['date'] == $day): ?>
					Had meeting
					<?php if (isset($meeting['with'])): ?>
						with <?php echo $meeting['with'] ?>
					<?php endif ?>
					<br />
				<?php endif ?>
				<?php if ($meeting['created'] >= $dayStart && $meeting['created'] <= $dayEnd): ?>
					Added meeting
					<?php if (isset($meeting['with'])): ?>
						with <?php echo $meeting['with'] ?>
					<?php endif ?>
					<br />
				<?php endif ?>
			<?php endforeach ?>
		<?php endif ?>
		
		<?php if (isset($contact['Action']) && count($contact['Action']) > 0): ?>
			<?php foreach ($contact['Action'] as $action): ?>
				<?php if ($action['completed'] && $action['completed_at'] >= $dayStart && $action['completed_at'] <= $dayEnd): ?>
					Completed: <?php echo $action['text'] ?>
					<br />
				<?php endif ?>
			<?php endforeach ?>
		<?php endif ?>
		
		<?php if (isset($contact['Note']) && count($contact['Note']) > 0): ?>
			<?php foreach ($contact['Note'] as $note): ?>
				<?php if ($note['created'] >= $dayStart && $note['created'] <= $dayEnd): ?>
					<?php echo $note['text'] ?>
					<br />
				<?php endif ?>
			<?php endforeach ?>
		<?php endif ?>
		
		<?php if (isset($contact['ContactStatusChange']) && count($contact['ContactStatusChange']) > 0): ?>
			<?php $done = false; ?>
			<?php foreach ($contact['ContactStatusChange'] as $change): ?>
				<?php if (!$done && $change['changed_at'] >= $dayStart && $change['changed_at'] <= $dayEnd): ?>
					<?php $done = true; ?>
					<?php if ($change['status_id'] == 1 || $change['status_id'] == 5): ?>
						Status changed to <?php echo strtolower($statuses[$change['status_id']]) ?>
						<br />
					<?php endif ?>
				<?php endif ?>
			<?php endforeach ?>
		<?php endif ?>
		
	</td>
	<td valign="top" class="listRow">
		<?php if ($contact['Contacttype']['id'] == 1): ?>
			<?php echo $contact['Contacttype']['name'] ?>
		<?php else: ?>
			Market <?php echo $contact['Market']['name'] ?>
		<?php endif ?>
	</td>
	<td valign="top" class="listRow">
		<?php echo $contact['Sector']['name'] ?>
	</td>
</tr>