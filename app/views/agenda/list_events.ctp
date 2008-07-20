<?php $currentDate = ''; ?>

	<?php if (isset($events) && count($events) > 0): ?>

		<?php foreach ($events as $e): ?>
		
			<?php 
				if(isset($e['Meeting'])) 
				{
					$date = $e['Meeting']['date'];
				} elseif(isset($e['Action'])) {
					$date = $e['Action']['deadline_date'];
				} else {
					$date = $e['Contract']['payment_by'];
				}
			?>
		
			<?php if ($currentDate != $date): ?>
				<?php $currentDate = $date ?>
				<tr>
					<td colspan="3">
						<h2>
							<?php echo $format->date($currentDate, true) ?>
						</h2>
					</td>
				</tr>
			<?php endif ?>
		
			<?php if (isset($e['Contract'])): ?>
				<?php echo $this->renderElement('../contracts/list_row', array('data' => $e)) ?>
			<?php elseif (isset($e['Meeting'])): ?>
				<?php echo $this->renderElement('../meetings/list_row', array('data' => $e)) ?>
			<?php else: ?>
				<?php echo $this->renderElement('../actions/list_row', array('data' => $e)) ?>
			<?php endif ?>
	
		<?php endforeach ?>

	<?php endif ?>
