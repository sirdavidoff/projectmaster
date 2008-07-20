<?php if (!isset($print) || !$print): ?>
	<span id="addReviewLink" style="<?php if($forms->areErrors('Note')) echo "display:none;" ?>">
		<?php echo $html->link('Add a note', "javascript:(function(){Effect.toggle('addReviewContainer', 'appear');$('addReviewLink').style.display = 'none'}())") ?>
	</span>
<?php endif ?>