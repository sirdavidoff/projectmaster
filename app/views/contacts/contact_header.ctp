<table  cellspacing="0" cellpadding="0" style="width:100%">
	<tr>
		<td valign="top">
			
			<h1><?php echo $ajaxs->editable($this->data['Contact']['id'], $this->data['Contact']['name'], 'name', 'Contact', 'contacts') ?></h1>

			<div class="h1b">
				<?php if($this->data['Contacttype']['id'] == 1): ?>
					<?php echo $this->data['Contacttype']['name'] ?>	
				<?php elseif(isset($this->data['Market']['name'])): ?>
					Market <?php echo $ajaxs->editable($this->data['Contact']['id'], $this->data['Market']['name'], 'market_id', 'Contact', 'contacts', 'span', array('collection' => $marketList)) ?>
				<?php endif ?>

				<?php if ($this->data['Sector']['name']): ?>
					(<?php echo $ajaxs->editable($this->data['Contact']['id'], $this->data['Sector']['name'], 'sector_id', 'Contact', 'contacts', 'span', array('collection' => $sectorList)) ?>)
				<?php endif ?>
			</div>

			<?php if($this->data['Contact']['revenue'] != null || $this->data['Contact']['growth'] != null): ?>
				<br />
				<?php if($this->data['Contact']['revenue'] != null): ?>
					Revenue <?php echo $this->data['Contact']['revenue'] ?>
				<?php endif ?>
				<?php if($this->data['Contact']['revenue'] != null && $this->data['Contact']['growth'] != null): ?>-<?php endif ?>
				<?php if($this->data['Contact']['growth'] != null): ?>
					<?php echo $this->data['Contact']['growth'] ?> growth
				<?php endif ?>
			<?php endif ?>
			
		</td>
		
		<td valign="top" style="text-align: right">
			<?php if (!$print): ?>
				<h1><?php echo $ajaxs->editable($this->data['Contact']['id'], $this->data['Status']['name'], 'status_id', 'Contact', 'contacts', 'span', array('collection' => $statusList)) ?></h1>
			<?php endif ?>
		</td>
	</tr>
</table>

