<table style="width:100%" cellspacing="0" cellpadding="0">
	<tr>
		<td valign="top"><h1><?php echo $pageTitle;?></h1></td>
		<td valign="bottom" align="right">
			<?php echo $html->link('Add New Project', array('controller' => 'projects', 'action' => 'add')) ?><br /><br />
			<?php if ($order != 'start'): ?>
				<?php echo $html->link('By start date', Array('action' => 'listAll/start')) ?>
			<?php else: ?>
				By start date
			<?php endif ?>
			|
			<?php if ($order != 'media'): ?>
				<?php echo $html->link('By media', Array('action' => 'listAll/media')) ?>
			<?php else: ?>
				By media
			<?php endif ?>
		</td>
	</tr>
</table>
<br /><br />


<?php if (!isset($projects) || count($projects) < 1): ?>
	There are currently no projects to display. Why don't you <?php echo $html->link('create one', '/projects/add') ?>?
	
<?php else: ?>
	
	
	<?php $currentTitle = ''; ?>
	
	<table  cellspacing="0" cellpadding="0" style="width:100%">
		<?php foreach ($projects as $p): ?>
			
			<?php if ($order == 'media'): ?>
				<?php if ($currentTitle != $p['Media']['name']): ?>
					<?php $currentTitle = $p['Media']['name'] ?>
					<tr>
						<td colspan="3">
							<h2>
								<?php echo $currentTitle ?>
							</h2>
						</td>
					</tr>
				<?php endif ?>
			<?php endif ?>
			
			<?php echo $this->renderElement('../projects/project_list_row', array('project' => $p, 'order' => $order, 'userList' => $userList)) ?>
		
		<?php endforeach ?>
	</table>
	<br />
	<div align="right">
		<?php //echo $this->renderElement('../projects/meeting_indicator_key') ?>
	</div>

<?php endif ?>