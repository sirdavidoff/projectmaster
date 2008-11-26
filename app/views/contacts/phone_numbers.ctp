<table style="width:100%" cellspacing="0" cellpadding="0">
	<tr>
		<td valign="top"><h1><?php echo $pageTitle;?></h1></td>
		<td valign="bottom" align="right">
			Show
			<?php if ($type != 'all'): ?>
				<?php echo $html->link('All', Array('action' => "phoneNumbers/$pid/all")) ?>
			<?php else: ?>
				All
			<?php endif ?>
			|
			<?php if ($type != 'active'): ?>
				<?php echo $html->link('Active', Array('action' => "phoneNumbers/$pid/active")) ?>
			<?php else: ?>
				Active
			<?php endif ?>
		</td>
	</tr>
</table>

<?php if (!isset($contacts) || count($contacts) < 1): ?>
	There are currently no contacts to display.
	
<?php else: ?>
	
	<table  cellspacing="0" cellpadding="0" style="width:100%">
		<?php foreach ($contacts as $c): ?>
			
			<?php echo $this->renderElement('../contacts/contact_list_phone', array('contact' => $c)) ?>
		
		<?php endforeach ?>
	</table>
	

<?php endif ?>