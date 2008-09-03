<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title>
		MediaLinks: <?php echo $title_for_layout;?>
	</title>

	<?php echo $html->charset();?>

	<link rel="icon" href="<?php echo $this->webroot;?>favicon.ico" type="image/x-icon" />
	<link rel="shortcut icon" href="<?php echo $this->webroot;?>favicon.ico" type="image/x-icon" />
	<?php echo $html->css('cake.generic');?>
	<?php echo $html->css('print');?>
	<?php echo $html->css('calendar');?>
	
	<?php
	if(isset($javascript)) 
	{
	  echo $javascript->link('flash.js');
	  echo $javascript->link('prototype.js');
	  echo $javascript->link('scriptaculous.js?load=effects,controls,builder,dragdrop,extensions');
	}
	?>
</head>
<body>

	<?php echo $this->renderElement('flashes'); ?>

	<?php if (isset($pid)): ?>
		<div id="printHeader">
			<div id="printTime">
				printed on <?php echo $format->date(date('Y-m-d'), false, true, false) ?>
			</div>
			<?php if (isset($allProj[$pid])): ?>
				<?php echo $allProj[$pid] ?>
			<?php endif ?>
		</div>
		<div style="clear:both"></div>
	<?php endif ?>

	<?php echo $content_for_layout;?>

</body>
</head>