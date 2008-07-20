<?php if ($record['created']): ?>
	Created: <?php echo $time->niceShort($record['created']) ?>
	<?php if (isset($creator) && isset($creator['id'])): ?>
		by <?php echo $allUserList[$creator['id']] ?>
	<?php endif ?>
	<br />
<?php endif ?>


<?php if ($record['updated'] && $record['updated'] != $record['created']): ?>
	Last updated: <?php echo $time->niceShort($record['updated']) ?>
	<?php if (isset($updater) && isset($updater['id'])): ?>
		by <?php echo $allUserList[$updater['id']] ?>
	<?php endif ?>
	<br />
<?php endif ?>