<div class="calendarMeeting">
	<span class="calendarTime">
		<?php echo $data['Meeting']['time'] ?>
	</span>
	<div class="calendarEventInner">
		<?php echo $html->link($data['Contact']['name'], '/contacts/view/' . $data['Contact']['id']) ?>
	</div>
	<div style="clear:both"></div>
</div>