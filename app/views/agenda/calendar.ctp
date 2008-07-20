<h1><?php echo $pageTitle;?></h1>

<table style="width:100%" cellspacing="0" cellpadding="0">
	<tr>
		<td valign="top" class="h1b"><?php echo $format->dateRange($start, $end) ?></td>
		<td valign="top" style="text-align:right">
			<?php $before = date('Y-m-d', mktime(0, 0, 0, substr($start, 5, 2), substr($start, 8, 2)-30, substr($start, 0, 4))) ?>
			<?php $justBefore = date('Y-m-d', mktime(0, 0, 0, substr($start, 5, 2), substr($start, 8, 2)-1, substr($start, 0, 4))) ?>
			<?php $justAfter = date('Y-m-d', mktime(0, 0, 0, substr($end, 5, 2), substr($end, 8, 2)+1, substr($end, 0, 4))) ?>
			<?php $after = date('Y-m-d', mktime(0, 0, 0, substr($end, 5, 2), substr($end, 8, 2)+30, substr($end, 0, 4))) ?>
			<?php echo $html->link('< earlier', "calendar/$pid/$before/$justBefore") ?> |
			<?php echo $html->link('later >', "calendar/$pid/$justAfter/$after") ?>
		</td>
	</tr>
</table>

<br /><br />

<!--<div id="testwin" style="width:400px;background:pink">
	HERE IS SOMETHING
</div>
<script type="text/javascript" charset="utf-8">
	//new Ajax.floatWindow('testwin', 'testwin');
</script>-->

<table cellspacing="0" cellpadding="0" style="width:100%">
	
	<tr>
		<td width="16%" valign="top" class="calendarTitleCell">Monday</td>
		<td width="16%" valign="top" class="calendarTitleCell">Tuesday</td>
		<td width="16%" valign="top" class="calendarTitleCell">Wednesday</td>
		<td width="16%" valign="top" class="calendarTitleCell">Thursday</td>
		<td width="16%" valign="top" class="calendarTitleCell">Friday</td>
		<td width="8%" valign="top" class="calendarTitleCell">Saturday</td>
		<td width="8%" valign="top" class="calendarTitleCell">Sunday</td>
	</tr>
	
	<?php for ($w = 0; $w < $numDays/7; $w++): ?>
		<tr>
			<?php for ($d = 0; $d < 7; $d++): ?>
				<?php
					$currentDate = date('Y-m-d', mktime(0, 0, 0, substr($start, 5, 2), substr($start, 8, 2)+($w*7)+$d, substr($start, 0, 4)));
					
					// Work out if we're in a week where the month changes - if so, adjust
					// the table cell's class accordingly
					$tableClass = '';
					$daysLeftInMonth = $calendar->daysLeftInMonth($currentDate);
					if($daysLeftInMonth <= (6-$d)) $tableClass .= 'monthEnding ';
					if($daysLeftInMonth == 0 && $d < 6) $tableClass .= 'monthEnd ';
					
					$dayOfMonth = $calendar->dayOfMonth($currentDate);
					if($dayOfMonth < ($d + 1)) $tableClass .= 'monthStarting ';
				?>
				<td valign="top" class="<?php echo $tableClass ?>">
					<div id="day<?php echo $currentDate ?>" class="calendarDay <?php if($d > 4) echo 'weekend' ?> <?php if($currentDate == date('Y-m-d')) echo 'today' ?>">
						<div id="addControl<?php echo $currentDate ?>" class="calendarAdd">
							+
						</div>
						<div id="name" style="padding-bottom:2px">
							<?php echo $calendar->calendarCellDate($currentDate, $start) ?>
						</div>
						<div style="clear:both">
							 <?php while(isset($events) && count($events) > 0 && $currentDate == $calendar->getEventDate($events[0])): ?>
								<?php $event = array_shift($events); ?>

								<?php
									if(isset($event['Meeting'])) echo $this->renderElement('../meetings/list_row_calendar', array('data' => $event));
									if(isset($event['Contract'])) true;
								?>
							<?php endwhile ?>
						</div>
					</div>
					<script type="text/javascript" charset="utf-8">
						new Ajax.bindControl('day<?php echo $currentDate ?>', 'addControl<?php echo $currentDate ?>');
					</script>
				</td>
			<?php endfor ?>
		</tr>
	<?php endfor ?>
	
	
	<?php //echo $this->renderElement('../agenda/list_events', array('events' => $events)) ?>
	
</table>



