
<div id="addPersonLink" style="<?php if($forms->areErrors('Person')) echo "display:none;" ?>">
	<?php echo $html->link('Add a person', "javascript:(function(){Effect.toggle('addPersonContainer', 'appear');$('addPersonLink').style.display = 'none'}())") ?>
</div>
<div id="addPersonContainer" style="<?php if(!$forms->areErrors('Person')) echo "display:none;" ?> border:1px solid gray; padding:10px">
	<h3>Add a Person</h3>
	<?php echo $ajax->form() ?>
		<?php echo $forms->hidden("Person.contact_id", array("value" => $contact_id)) ?>
		<table  cellspacing="0" cellpadding="0" style="width:100%">
			<tr>
				<td valign="top" colspan="2" class="formCell">
					Position<br />
					<?php echo $forms->text("Person.position") ?>
					<?php echo $forms->error('Person.position') ?>
				</td>
			</tr>
			<tr>
				<td valign="top" style="width:50%" class="formCell">
					Name<br />
					<?php echo $forms->text("Person.name") ?>
				</td>
				<td valign="top" style="width:50%" class="formCell">
					Tel<br />
					<?php echo $forms->text("Person.tel") ?>
				</td>
			</tr>
			<tr>
				<td valign="top" style="width:50%" class="formCell">
					Email<br />
					<?php echo $forms->text("Person.email") ?>
				</td>
				<td valign="top" style="width:50%" class="formCell">
					Mobile<br />
					<?php echo $forms->text("Person.mobile") ?>
				</td>
			</tr>
			<tr>
				<td valign="top" style="width:50%" class="formCell">
					Address<br />
					<?php echo $forms->text("Person.address") ?>
				</td>
				<td valign="top" style="width:50%" class="formCell">
					Fax<br />
					<?php echo $forms->text("Person.fax") ?>
				</td>
			</tr>
			<tr>
				<td valign="top" colspan="2" class="formCell">
					Notes<br />
					<?php echo $forms->textarea("Person.notes", array('rows' => 2)) ?>
				</td>
			</tr>
			<tr>
				<td valign="top" colspan="2" class="formCell" align="right">
					<?php echo $ajaxs->submit("Add Person", array('url' => '/people/add', 'update'=>'addPerson')) ?>
				</td>
			</tr>
		</table>
	<?php echo "</form>" ?>
</div>