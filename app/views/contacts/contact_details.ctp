<h2>Contact Details</h2>
<table cellspacing="0" cellpadding="0">
	<?php //if ($this->data['Contact']['website']): ?>
		<tr>
			<td valign="top" class="fieldTitle" style="padding-right:15px">Website</td>
			<td valign="top">
				<?php //echo $html->link($this->data['Contact']['website'], $format->url($this->data['Contact']['website'])) ?>
				<?php echo $ajaxs->editable($this->data['Contact']['id'], $this->data['Contact']['website'], 'website', 'Contact', 'contacts') ?>
				</td>
		</tr>
	<?php //endif ?>
	<?php //if ($this->data['Contact']['address']): ?>
		<tr>
			<td valign="top" class="fieldTitle" style="padding-right:15px">Address</td>
			<td valign="top"><?php echo $ajaxs->editable($this->data['Contact']['id'], $this->data['Contact']['address'], 'address', 'Contact', 'contacts') ?></td>
		</tr>
	<?php //endif ?>
	
	
	<?php //if ($this->data['Contact']['website'] || $this->data['Contact']['address']): ?>
		<tr>
			<td valign="top" style="padding-right:15px">&nbsp;</td>
			<td valign="top">&nbsp;</td>
		</tr>
	<?php //endif ?>
	
	
	<?php //if ($this->data['Contact']['tel']): ?>
		<tr>
			<td valign="top" class="fieldTitle" style="padding-right:15px">General Tel</td>
			<td valign="top"><?php echo $ajaxs->editable($this->data['Contact']['id'], $this->data['Contact']['tel'], 'tel', 'Contact', 'contacts') ?></td>
		</tr>	 
	<?php //endif ?>
	<?php //if ($this->data['Contact']['fax']): ?>
		<tr>
			<td valign="top" class="fieldTitle" style="padding-right:15px">General Fax</td>
			<td valign="top"><?php echo $ajaxs->editable($this->data['Contact']['id'], $this->data['Contact']['fax'], 'fax', 'Contact', 'contacts') ?></td>
		</tr>
	<?php //endif ?>
	<?php //if ($this->data['Contact']['email']): ?>
		<tr>
			<td valign="top" class="fieldTitle" style="padding-right:15px">General Email</td>
			<td valign="top"><?php echo $ajaxs->editable($this->data['Contact']['id'], $this->data['Contact']['email'], 'email', 'Contact', 'contacts') ?></td>
		</tr>
	<?php //endif ?>
</table>