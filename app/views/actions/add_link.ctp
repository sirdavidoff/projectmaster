<?php if (!isset($print) || !$print): ?>
	<span id="addActionLink" style="<?php if($forms->areErrors('Action')) echo "display:none;" ?>">
		<?php echo $html->link('Add an action', "javascript:(function(){Effect.toggle('addActionContainer', 'appear');$('addActionLink').style.display = 'none'}())") ?>
	</span>
<?php endif ?>