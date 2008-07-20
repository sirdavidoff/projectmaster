<?php // Javascript code to track the pages the user has visited on the site ?>

<?php if ('DEBUG' == 2): ?>
	<div id='cookieDebug'></div>
<?php endif ?>

<?php if (isset($tracker['url'])): ?>
	
	<?php echo $javascript->link('tracking.js') ?>
	
	<script type="text/javascript" charset="utf-8">
		Tracker.trackerLength = '<?php echo $tracker['length'] ?>';
		Tracker.track('<?php echo $tracker['url'] ?>', '<?php echo $tracker['name'] ?>', '<?php echo $tracker['path'] ?>');
	</script>

<?php endif ?>

<?php if ('DEBUG' == 2 && isset($tracker)): ?>
	<script type="text/javascript" charset="utf-8">
		document.getElementById('cookieDebug').innerHTML = Tracker.readCookie('<?php echo $tracker['name'] ?>');
	</script>
<?php endif ?>