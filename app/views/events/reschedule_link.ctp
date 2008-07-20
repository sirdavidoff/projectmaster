<span id="rescheduleMeetingLink<?php echo $id ?>" style="<?php if($forms->areErrors('Meeting')) echo "display:none;" ?>">
	<?php echo $html->link('reschedule', "javascript:(function(){Effect.toggle('rescheduleMeetingContainer$id', 'appear');$('rescheduleMeetingLink$id').style.display = 'none'}())") ?>
</span>