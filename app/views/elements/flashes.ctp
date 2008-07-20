<?php if ($session->check('Message.flash')): ?>
	<div id="flashDiv" style="margin-bottom:10px">
		<?php $session->flash(); ?>
	</div>
<?php else: ?>
	<?php // We need this div to put any javascript flashes in ?>
	<div id="flashDiv" style="margin-bottom:10px; display:none">
	</div>
<?php endif ?>