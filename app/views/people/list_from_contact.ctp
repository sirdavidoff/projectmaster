<div id="addPerson">
	
	<?php if (isset($this->data['Person']) && count($this->data['Person']) > 0): ?>
		<ul id="people" style="margin:0px">
			<?php foreach ($this->data['Person'] as $person): ?>
				<li id='<?php echo "person_" . $person['id'] ?>' class="personBox">
					<table cellspacing="0" cellpadding="0" style="width:100%">
						<tr>
							<td valign="top"><h3><?php echo $ajaxs->editable($person['id'], $person['position'], 'position', 'Person', 'people') ?></h3></td>
							<td valign="middle">
								<?php if (!$print): ?>
									<div class="subline">
										<?php echo $ajaxs->link('remove', '/people/delete/' . $person['id'], array('update' => 'addPerson'), "If you delete this person, all their information will be permanently removed. Are you sure you want to do this?") ?>
										<span class="handle">drag</span>
									</div>
								<?php endif ?>
							</td>
						</tr>
					</table>
					
					
					<table cellspacing="0" cellpadding="0" style="width:100%">
						<tr>
							<td valign="top" style="width:50%">
					
								<table cellspacing="0" cellpadding="0">
									<tr>
										<td valign="top" class="fieldTitle" style="padding-right:15px">Name</td>
										<td valign="top">
											<?php echo $ajaxs->editable($person['id'], $person['name'], 'name', 'Person', 'people') ?>
										</td>
									</tr>
									<tr>
										<td valign="top" class="fieldTitle" style="padding-right:15px">Email</td>
										<td valign="top"><?php echo $ajaxs->editable($person['id'], $person['email'], 'email', 'Person', 'people') ?></td>
									</tr>
									<tr>
										<td valign="top" class="fieldTitle" style="padding-right:15px">Address</td>
										<td valign="top"><?php echo $ajaxs->editable($person['id'], $person['address'], 'address', 'Person', 'people') ?></td>
									</tr>
								</table>
							</td>
							<td  valign="top" style="width:50%">
								<table cellspacing="0" cellpadding="0">
									<tr>
										<td valign="top" class="fieldTitle" style="padding-right:15px">Tel</td>
										<td valign="top"><?php echo $ajaxs->editable($person['id'], $person['tel'], 'tel', 'Person', 'people') ?></td>
									</tr>	
									<tr>
										<td valign="top" class="fieldTitle" style="padding-right:15px">Mobile</td>
										<td valign="top"><?php echo $ajaxs->editable($person['id'], $person['mobile'], 'mobile', 'Person', 'people') ?></td>
									</tr> 
									<tr>
										<td valign="top" class="fieldTitle" style="padding-right:15px">Fax</td>
										<td valign="top"><?php echo $ajaxs->editable($person['id'], $person['fax'], 'fax', 'Person', 'people') ?></td>
									</tr>
									
								</table>
								
							</td>
						</tr>
					</table>
					
					<br />
					
					
					<table cellspacing="0" cellpadding="0">
						<tr>
							<td valign="top" class="fieldTitle" style="padding-right:15px">Notes</td>
							<td valign="top"><?php echo $ajaxs->editable($person['id'], $person['notes'], 'notes', 'Person', 'people', 'div', array('rows' => 2)) ?></td>	
						</tr>
					</table>
					
					
					
					
				</li>
			<?php endforeach ?>
		</ul>
		<br />
		<?php echo $ajax->sortable('people', array('url'=>'/people/reorder', 'handle' => 'handle')) ?>
	<?php endif ?>
	
	
	
	<?php if (!$print): ?>
		<?php echo $this->renderElement('../people/add', array('contact_id' => $contact_id)) ?>
	<?php endif ?>
	
</div>