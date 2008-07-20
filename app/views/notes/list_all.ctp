<div id="addNote">
	<?php if (isset($this->data['Note']) && count($this->data['Note']) > 0): ?>
		<table style="width:100%" cellspacing="0" cellpadding="0">
			<tr>
				<td valign="top"><h2>Notes</h2></td>
				<td valign="bottom" style="text-align:right;padding-bottom:11px">
					<?php echo $this->renderElement('../notes/add_link') ?>
				</td>
			</tr>
		</table>
		
		<?php echo $this->renderElement('../notes/add', array('contact_id' => $contact_id)) ?>
		
		<div class="notesBox">
			<ul id="notes" style="margin:0px">
				<?php foreach ($this->data['Note'] as $note): ?>
					<li id='<?php echo "note_" . $note['id'] ?>' class="noteBox">
						<?php echo $ajaxs->editable($note['id'], $note['text'], 'text', 'Note', 'notes', 'div', array('rows' => 2)) ?>
						<div class="sublineNotes">
							<?php echo $time->niceShort($note['created']) ?>
							<?php if (isset($note['created_by'])): ?>
								by <?php echo $allUserList[$note['created_by']] ?>
							<?php endif ?>
							<?php if (!isset($print) || !$print): ?>
								<?php echo $ajaxs->link('remove', '/notes/delete/' . $note['id'], array('update' => 'addNote'), "If you remove this note it will be deleted forever. Are you sure you want to do this?") ?>
								<span class="handle">drag</span>
							<?php endif ?>
						</div>
					</li>
				<?php endforeach ?>
			</ul>
			<?php echo $ajax->sortable('notes', array('url'=>'/notes/reorder', 'handle' => 'handle')) ?>
		</div>
		<br />
	
	<?php else: ?>
		<div style="text-align:right">
			<?php echo $this->renderElement('../notes/add_link') ?>
		</div>
		<?php echo $this->renderElement('../notes/add', array('contact_id' => $contact_id)) ?>
	<?php endif ?>
</div>