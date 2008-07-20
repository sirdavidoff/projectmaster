<?php if (!isset($print) || !$print): ?>
	<span id="addContractLink" style="<?php if($forms->areErrors('Contract')) echo "display:none;" ?>">
		<?php echo $html->link('Add a contract', "javascript:(function(){Effect.toggle('addContractContainer', 'appear');$('addContractLink').style.display = 'none'}())") ?>
	</span>
<?php endif ?>