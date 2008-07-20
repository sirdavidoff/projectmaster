<?php echo $forms->includeCalendar() ?>

<table style="width:100%" cellspacing="0" cellpadding="0">
	<tr>
		<td valign="top">
			<h1>
				<?php echo $ajaxs->editable($this->data['Project']['id'], $this->data['Media']['name'], 'media_id', 'Project', 'projects', 'span', array('collection' => $mediaList, 'class' => 'inlineEditable')) ?>
				<?php echo $ajaxs->editable($this->data['Project']['id'], $this->data['Project']['subject'], 'subject', 'Project', 'projects', 'span', array('class' => 'inlineEditable')) ?>
			<?php echo substr($this->data['Project']['started_on'], 2, 2) ?>
			<?php if ($this->data['Project']['project_status_id'] != 1): ?>
				<span class="annotation">(Closed)</span>
			<?php endif ?>
			</h1>
		</td>
		<td valign="bottom" align="right">
			<?php if ($this->data['Project']['project_status_id'] == 1): ?>
				<?php echo $html->link('Close project', array('controller' => 'projects', 'action' => 'close/' . $this->data['Project']['id'])) ?>
			<?php else: ?>
				<?php echo $html->link('Reopen project', array('controller' => 'projects', 'action' => 'open/' . $this->data['Project']['id'])) ?>
			<?php endif ?>
		</td>
	</tr>
</table>


<br />

<?php
	if($this->data['Project']['project_status_id'] == 1) {
		$duration = $format->duration($this->data['Project']['started_on'], date('Y-m-d'), 'd');
	} else {
		$duration = $format->duration($this->data['Project']['started_on'], $this->data['Project']['finished_on'], 'd');
	}
?>

<table style="width:100%" cellspacing="0" cellpadding="0">
	<tr>
		<td valign="top" style="width:65%">
			
			<h3>Contacts (<?php echo $nC ?>)</h3>
			<div style="border:1px solid #aaa; height: 30px">
				<?php foreach ($nCS as $status_id => $n): ?>
					<?php if ($n > 0): ?>
						<div style="width:<?php echo $n/$nC*100 ?>%" class="statusPercentCell status<?php echo $status_id ?>">
							<div class="statusPercentCellContent">
								<?php echo $n ?>
							</div>
						</div>
					<?php endif ?>
				<?php endforeach ?>
				<div style="clear: both; padding-top:10px">
				<?php foreach ($statusList as $sId => $sName): ?>
					<div style="float: left; margin-right: 15px">
						<div class="keyBox statusKey<?php echo $sId ?>">

						</div>
						<?php echo $sName ?>
					</div>
				<?php endforeach ?>
				</div>
			</div>

			<div style="clear:both; padding-top:20px"></div>

			<?php echo $this->renderElement('../team_members/list_all', array('members' => $members)) ?>
			
		</td>
		<td valign="top" style="width:35%; padding-left:15px">
			
			<h3>Statistics</h3>
			
			<div id="statsBox">
			<table cellspacing="3" cellpadding="0">
				<tr>
					<td valign="top">
						<?php if ($format->isInFuture($this->data['Project']['started_on'])): ?>
							<?php echo "Starts on" ?>
						<?php else: ?>
							<?php echo "Started on" ?>
						<?php endif ?> 
					</td>
					<td valign="top">
						<?php echo $ajaxs->editable($this->data['Project']['id'], $format->date($this->data['Project']['started_on'], false, true), 'started_on_readable', 'Project', 'projects', 'span', array('emptyText' => "'set date'", 'calendar' => true, 'editValue' => "'".$format->slashDate($this->data['Project']['started_on'])."'")) ?>
					</td>
				</tr>
				
				<?php if ($this->data['Project']['project_status_id'] != 1): ?>
				<tr>
					<td valign="top">
						Finished on: 
					</td>
					<td valign="top">
						<?php echo $ajaxs->editable($this->data['Project']['id'], $format->date($this->data['Project']['finished_on'], false, true), 'finished_on_readable', 'Project', 'projects', 'span', array('emptyText' => "'set date'", 'calendar' => true, 'editValue' => "'".$format->slashDate($this->data['Project']['finished_on'])."'")) ?>
					</td>
				</tr>
				<?php endif ?>
				
				<?php if (!$format->isInFuture($this->data['Project']['started_on'])): ?>
				<tr>
					<td valign="top">
						Project length: 
					</td>
					<td valign="top">
						<?php echo $duration ?> days
						<?php if ($this->data['Project']['project_status_id'] == 1): ?>
							and counting
						<?php endif ?>
					</td>
				</tr>
				<?php endif ?>
				
				<?php if ($nM > 0): ?>
					
					<tr>
						<td valign="top" colspan="2">
							&nbsp;
						</td>
					</tr>
					
					<tr>
						<td valign="top">
							Total Meetings: 
						</td>
						<td valign="top">
							<?php echo $nM ?>
						</td>
					</tr>
					
					<?php if ($this->data['Project']['project_status_id'] == 1): ?>
						<tr>
							<td valign="top">
								Meetings pending: 
							</td>
							<td valign="top">
								<?php echo $nMp ?>
							</td>
						</tr>
					<?php endif ?>
					
					<tr>
						<td valign="top">
							Meetings sold in: 
						</td>
						<td valign="top">
							<?php echo round($nCon/$nM*100) ?>%
						</td>
					</tr>
					
					<tr>
						<td valign="top">
							Time to first meeting: 
						</td>
						<td valign="top">
							<?php echo $format->duration($this->data['Project']['started_on'], $dM1, 'd') ?> days
						</td>
					</tr>
					
				<?php endif ?>
				<?php if ($nCon > 0): ?>
					
					<tr>
						<td valign="top" colspan="2">
							&nbsp;
						</td>
					</tr>
					
					<tr>
						<td valign="top">
							Total sold: 
						</td>
						<td valign="top">
							<?php echo $vCon ?>€
						</td>
					</tr>
					
					<tr>
						<td valign="top">
							Number of contracts: 
						</td>
						<td valign="top">
							<?php echo $nCon ?>
						</td>
					</tr>
					
					<tr>
						<td valign="top">
							Time to first contract: 
						</td>
						<td valign="top">
							<?php echo $format->duration($this->data['Project']['started_on'], $dCon1, 'd') ?> days
						</td>
					</tr>
					
					<tr>
						<td valign="top" colspan="2">
							&nbsp;
						</td>
					</tr>
					
					<tr>
						<td valign="top">
							Productivity: 
						</td>
						<td valign="top">
							<?php echo round($vCon/($duration/7)) ?>€ per week
						</td>
					</tr>
					
				<?php endif ?>
			</table>
			</div>
			
		</td>
	</tr>
</table>