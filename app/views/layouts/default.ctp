<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title>
		<?php echo $title_for_layout;?> - ProjectMaster
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
<body>
	<?php echo $this->renderElement('tracking'); ?>
	<div id="container">
		<div id="header">
			<div id="headerTabs">
				<?php if (isset($pid) && !in_array($pid, $relProj)): ?>
					<div class="headerTab activeTab">
						<?php echo $allProj[$pid] ?>
					</div>
				<?php endif ?>
				
				<?php if (count($relProj) > 3): ?>
					<?php $limit = 3; ?>
					<?php $moreProj = true; ?>
				<?php else: ?>
					<?php $limit = count($relProj); ?>
				<?php endif ?>
				<?php for($i = 0; $i < $limit; $i++): ?>
					<?php if (isset($pid) && $pid == $relProj[$i]): ?>
						<div class="headerTab activeTab">
							<?php echo $allProj[$relProj[$i]] ?>
						</div>
					<?php else: ?>
						<div class="headerTab">
							<?php echo $html->link($allProj[$relProj[$i]], array('controller' => 'projects', 'action' => 'view/' . $relProj[$i])) ?>
						</div>
					<?php endif ?>
				<?php endfor ?>
				<?php if (isset($moreProj)): ?>
					<div class="headerTabBlank">
						<?php echo $html->link('More', array('controller' => 'users', 'action' => 'view')) ?>
					</div>
				<?php endif ?>
				<div class="headerRightTab">
					Logged in as <?php echo $cauth->user('first_name') ?> |
					<?php echo $html->link('Profile', array('controller' => 'users', 'action' => 'view')) ?> | 
					<?php echo $html->link('Logout', '/users/logout') ?>
				</div>
				<div style="clear:both"></div>
			</div>
			<?php if (isset($pid)): ?>
				<div id="menu" style="clear:both">
					<table  cellspacing="0" cellpadding="0" style="width:100%">
						<tr>
							<td valign="middle">
								<?php echo $html->link('Overview', "/projects/overview/$pid") ?> |
								<?php echo $html->link('Agenda', "/agenda/view/$pid") ?> |
								<?php echo $html->link('Calendar', "/agenda/calendar/$pid") ?> |
								<?php echo $html->link('Contacts', "/contacts/listAll/$pid") ?>
								<?php echo $html->link('Add', "/contacts/add/$pid", array('style' => 'font-weight:normal;text-decoration:none')) ?> |
								<?php echo $html->link('Activity', "/summaries/activity/$pid/today") ?> |
								<?php echo $html->link('Export', "/export/summary/$pid") ?>
							</td>
							<td valign="middle" align="right">
								<?php echo $forms->create(null, Array('type' => 'get', 'url' => "/contacts/search/$pid", 'id' => 'ContactSearchForm')) ?>
									<?php echo $forms->text('query', array('size' => '40', 'focus' => true, 'style' => "width:150px"))?>
									<?php echo $forms->submit('Search', array('style' => 'height:22px')) ?>
								<?php echo $forms->end() ?>
							</td>
						</tr>
					</table>
				</div>
			<?php endif ?>
		</div>
		<div id="content" style="padding-top:10px; clear:both">
			<?php echo $this->renderElement('flashes'); ?>

			<?php echo $content_for_layout;?>

		</div>
	</div>
	<?php echo $cakeDebug?>
</body>
</html>