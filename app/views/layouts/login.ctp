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
	<?php echo $html->css('calendar');?>
	
	<?php
	if(isset($javascript)) 
	{
	  echo $javascript->link('flash.js');
	  echo $javascript->link('prototype.js');
	  echo $javascript->link('scriptaculous.js?load=effects,controls,builder,dragdrop,extensions');
	  //echo $javascript->link('controls.js');
	}
	?>
</head>
<body style="background: #fdfdfd">
	<div align="center" style="align: center">
		<div style="margin-top: 50px; width: 300px; background: white; border: 1px solid #ccc; padding: 20px" align="left">
			<?php echo $this->renderElement('flashes'); ?>

			<?php echo $content_for_layout;?>
		</div>
	</div>
</body>
</head>