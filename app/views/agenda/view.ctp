<table style="width:100%" cellspacing="0" cellpadding="0">
	<tr>
		<td valign="top">
			<h1><?php echo $pageTitle;?></h1>
			<div class="h1b">
				<?php echo $format->dateRange($start, $end) ?>
			</div>
		</td>
		<td valign="top" style="text-align:right; padding-top:10px">
			<?php if (isset($userList) && count($userList) > 1): ?>
				Show for
				<?php $i = 0 ?>
				<?php foreach ($userList as $id => $name): ?>
					<?php if ($id == $userId): ?>
						<?php echo $name ?>
					<?php else: ?>
						<?php if ($current): ?>
							<?php echo $html->link($name, "/agenda/view/$pid/$id") ?>
						<?php else: ?>
							<?php echo $html->link($name, "/agenda/view/$pid/$id/$start/$end") ?>
						<?php endif ?>
					<?php endif ?>
					<?php if ($i < count($userList)-1): ?>
						<?php echo " | " ?>
					<?php endif ?>
					<?php $i++ ?>
				<?php endforeach ?>
				<br /><br />
			<?php endif ?>
			<?php echo $html->link('< earlier', "/agenda/view/$pid/$userId/$eStart/$eEnd") ?> | <?php echo $html->link('later >', "/agenda/view/$pid/$userId/$lStart/$lEnd") ?><br />
			<?php //echo $this->renderElement('../actions/add_link') ?>
			
			<?php if (!$print): ?>
				<?php //echo $html->link('Printable version', "/agenda/view/$pid/$userId/$start/$end/1") ?>
			<?php endif ?>
		</td>
	</tr>
</table>

<?php echo $forms->includeCalendar() ?>

<div id="addAction">
	<?php echo $this->renderElement('../actions/add') ?>
</div>
<?php if ((!isset($events) && !isset($datelessActions) && !isset($overdueActions)) || (count($events) < 1 && count($datelessActions) < 1 && (!isset($overdueActions) || count($overdueActions) < 1))): ?>
	
	<br />
	There is nothing scheduled between these dates.
	
<?php else: ?>
	<table  cellspacing="0" cellpadding="0" style="width:100%">
		
		<?php if (isset($overdueActions) && count($overdueActions) > 0): ?>
			<?php echo $this->renderElement('../agenda/list_overdue_actions', array('actions' => $overdueActions)) ?>
		<?php endif ?>
		
		<?php echo $this->renderElement('../agenda/list_events', array('events' => $events)) ?>
		
		<?php if (isset($events) && count($events) > 0): ?>
			<tr>
				<td  colspan="4" valign="top" style="text-align:right">
					<br />
					<?php echo $html->link('< earlier', "/agenda/view/$pid/$userId/$eStart/$eEnd") ?> | <?php echo $html->link('later >', "/agenda/view/$pid/$userId/$lStart/$lEnd") ?>
				</td>
			</tr>
		<?php endif ?>
		
		<?php if ($current): ?>
			<?php echo $this->renderElement('../agenda/list_dateless_actions', array('actions' => $datelessActions)) ?>
		<?php endif ?>
		
	</table>

<?php endif ?>


