<span id="addMemberLink" style="<?php if($forms->areErrors('TeamMember')) echo "display:none;" ?>">
	<?php if (!isset($title)): ?>
		<?php $title = "Add a Team Member" ?>
	<?php endif ?>
	<?php echo $html->link($title, "javascript:(function(){Effect.toggle('addMemberContainer', 'appear');$('addMemberLink').style.display = 'none'}())") ?>
</span>
