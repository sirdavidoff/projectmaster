<?php echo $forms->create('Contact', array('action' => "multi", 'onSubmit' => "if($('ContactMultiAction').options[$('ContactMultiAction').selectedIndex].value == '3'){return confirm('If you delete these contacts, all their information will be lost forever. Are you sure you want to do this?')}")) ?>

<div style="width:100%;padding:6px;border:1px solid gray;margin:10px 0px">
	<div style="float: right">
		<?php echo $forms->select('multiAction', $multiActions, null, null, 'Choose Action...')?>
		<?php echo $forms->submit('Go') ?>
	</div>
	<div style="float: right; margin-top: 4px; margin-right: 5px">With selected contacts:</div>
	<div style="float: left; margin-top: 4px; margin-left: 2px">
		Select:
		<?php echo $html->link('All', "javascript:(function(){\$\$('.multiCheck').each(function(s){if(!s.checked){s.click()}})}())") ?>
		|
			<?php echo $html->link('None', "javascript:(function(){\$\$('.multiCheck').each(function(s){if(s.checked){s.click()}})}())") ?>
	</div>
	<div style="clear:both"></div>
</div>