<span id="rescheduleActionLink<?php echo $id ?>" style="<?php if($forms->areErrors('Action')) echo "display:none;" ?>">
	<?php echo $html->link('reschedule', "javascript:(function(){Effect.toggle('rescheduleActionContainer$id', 'appear');$('rescheduleActionLink$id').style.display = 'none'}())") ?>
</span>