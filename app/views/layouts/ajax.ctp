<?php echo $content_for_layout;?>

<?php // Here we check to see whether any AJAX flashes have been set, and if so
      // we call the appropriate Javascript to display them ?>
<?php if (isset($flashMsg)): ?>
	<script type="text/javascript" charset="utf-8">
		Flash.setFlash('<?php echo $flashMsg ?>', '<?php echo $flashType ?>');
	</script>
<?php endif ?>