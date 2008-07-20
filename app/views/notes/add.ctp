<div id="addReviewContainer" style="<?php if(!$forms->areErrors('Note')) echo "display:none;" ?> border:1px solid gray; padding:10px">
	<h3>Add a Note</h3>
	<?php echo $ajax->form() ?>
		<?php echo $forms->hidden("Note.contact_id", array("value" => $contact_id)) ?>
		<?php echo $forms->textarea("Note.text", array("rows" => 2)) ?>
		<?php echo $forms->error('Note.text') ?>
		<div style="padding-top:6px;" align="right">
			<?php echo $ajaxs->submit("Add Note", array('url' => '/notes/add', 'update'=>'addNote')) ?>
		</div>
	<?php echo "</form>" ?>
</div>