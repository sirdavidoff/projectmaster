<table style="width:100%" cellspacing="0" cellpadding="0">
	<tr>
		<td valign="top"><h1><?php echo $pageTitle;?> for <?php echo $format->date($day, false, true) ?></h1></td>
		<td valign="bottom" align="right">
			<?php $dayBefore = date('Y-m-d', mktime(0, 0, 0, substr($day, 5, 2), substr($day, 8, 2)-1, substr($day, 0, 4))) ?>
			<?php $dayAfter = date('Y-m-d', mktime(0, 0, 0, substr($day, 5, 2), substr($day, 8, 2)+1, substr($day, 0, 4))) ?>
			<?php echo $html->link('< earlier', "activity/$pid/" . $dayBefore) ?>
			<?php if ($dayAfter <= date('Y-m-d')): ?>
				| <?php echo $html->link('later >', "activity/$pid/" . $dayAfter) ?>
			<?php else: ?>
				| later >
			<?php endif ?>
		</td>
	</tr>
</table>
<?php if (!isset($contacts) || count($contacts) < 1): ?>
	
	<br />
	<?php if ($day == date('Y-m-d')): ?>
		There has been no activity so far today.
	<?php else: ?>
		There there was no activity on this day.
	<?php endif ?>
	
<?php else: ?>
	
	<?php $currentStatus = ''; ?>
	
	<table  cellspacing="0" cellpadding="0" style="width:100%">
		<?php foreach ($contacts as $c): ?>
			
			<?php if ($currentStatus != $c['Contact']['historical_status']): ?>
				<?php $currentStatus = $c['Contact']['historical_status'] ?>
				<tr>
					<td colspan="3">
						<h2>
							<?php echo $statuses[$currentStatus] ?>
						</h2>
					</td>
				</tr>
			<?php endif ?>
			
			<?php echo $this->renderElement('../contacts/activity_list_row', array('contact' => $c, 'day' => $day, 'statuses' => $statuses)) ?>
		
		<?php endforeach ?>
	</table>

<?php endif ?>

