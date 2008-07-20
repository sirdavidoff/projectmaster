<?php echo $forms->includeCalendar() ?>

<?php echo $this->renderElement('../contacts/contact_header') ?>
<br />
<?php echo $this->renderElement('../contacts/contact_links') ?>

<table cellspacing="0" cellpadding="0" style="width:100%">
	<tr>
		<td valign="top" style="width:50%;padding-right:20px">
			
			<?php echo $this->renderElement('../contacts/contact_details') ?>
			<br />
			
			<?php echo $this->renderElement('../people/list_from_contact', array('contact_id' => $this->data['Contact']['id'])) ?>
			
		</td>
		<td valign="top" style="width:50%">

			<?php echo $this->renderElement('../contracts/list_all', array('contact_id' => $this->data['Contact']['id'], 'editContract' => $editContract)) ?>
			<?php echo $this->renderElement('../meetings/list_all', array('contact_id' => $this->data['Contact']['id'])) ?>
			<?php echo $this->renderElement('../actions/list_all', array('contact_id' => $this->data['Contact']['id'])) ?>
			<?php echo $this->renderElement('../notes/list_all', array('contact_id' => $this->data['Contact']['id'])) ?>

		</td>
	</tr>
</table>

<div class="footer">
	<?php if (!$print): ?>
		<?php echo $this->renderElement('created_updated', array('record'  => $this->data['Contact'], 'creator' => $this->data['Creator'], 'updater' => $this->data['Updater'])) ?>
		<br />
		<?php echo $html->link('Printable version', "/contacts/view/" . $this->data['Contact']['id'] . "/print", array('target' => '_blank')) ?>
		<br />
		<?php echo $html->link('Delete this contact', '/contacts/delete/'.$this->data['Contact']['id'], null, "If you delete this contact, all its information will be lost forever. Are you sure you want to do this?") ?>
	<?php endif ?>		
</div>
