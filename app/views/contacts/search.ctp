<h1><?php echo $pageTitle;?></h1>

<br />

<?php if (!isset($contacts) || count($contacts) < 1): ?>
	No contacts were found
	<br />
	<br />
	<?php echo $html->link('List all contacts', '/contacts/listAll') ?>
	
<?php else: ?>
	
	<table  cellspacing="0" cellpadding="0" style="width:100%">
		<?php foreach ($contacts as $c): ?>
			
			<?php echo $this->renderElement('../contacts/contact_search_row', array('contact' => $c)) ?>
		
		<?php endforeach ?>
	</table>
	

<?php endif ?>