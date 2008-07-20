<span id="addMeetingLink" style="<?php if($forms->areErrors('Meeting')) echo "display:none;" ?>">
	<?php echo $html->link('Add a meeting', "javascript:(function(){Effect.toggle('addMeetingContainer', 'appear');$('addMeetingLink').style.display = 'none'}())") ?>
</span>