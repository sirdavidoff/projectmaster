<h1><?php echo $pageTitle;?></h1>

<?php echo $forms->includeCalendar() ?>

<?php if (!isset($events) || count($events) < 1): ?>
	
	<br />
	There are no overdue actions.
	
<?php else: ?>
	<table  cellspacing="0" cellpadding="0" style="width:100%">
		
		<?php echo $this->renderElement('../agenda/list_events', array('events' => $events)) ?>
		
	</table>

<?php endif ?>

