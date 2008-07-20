<span id="rescheduleContractLink<?php echo $id ?>" style="<?php if($forms->areErrors('Contract')) echo "display:none;" ?>">
	<?php echo $html->link('reschedule', "javascript:(function(){Effect.toggle('rescheduleContractContainer$id', 'appear');$('rescheduleContractLink$id').style.display = 'none'}())") ?>
</span>