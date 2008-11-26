
<?php if ($rendering->contactHasPhone($contact)): ?>
<tr>
	<td valign="top" class="listRow printMini">
		
		<?php $num = 0 ?>
		<span class="status<?php echo $contact['Status']['id'] ?> printMiniHeading"><?php echo $contact['Contact']['name'] ?></span>
		<?php if (isset($contact['Contact']['tel']) && $contact['Contact']['tel'] != ''): ?>
			<span class="printContent<?php echo $num ?>">
				<?php echo $contact['Contact']['tel'] ?>
			</span>
			|
			<?php $num = ($num+1)%2 ?>
		<?php endif ?>
		<?php if (isset($contact['Person'])): ?>
			<?php foreach ($contact['Person'] as $person): ?>
				<?php if ((isset($person['tel']) && $person['tel'] != '') || (isset($person['mobile']) && $person['mobile'] != '')): ?>
					<span class="printContent<?php echo $num ?>">
					<?php echo $person['name'] ?>
					<?php if (isset($person['position'])): ?>
						(<?php echo $person['position'] ?>)
					<?php endif ?>
					<?php if (isset($person['mobile']) && $person['mobile'] != ''): ?>
						<?php echo $person['mobile'] ?>
						<?php if (isset($person['tel']) && $person['tel'] != ''): ?>
							,
						<?php endif ?>
					<?php endif ?>
					<?php if (isset($person['tel']) && $person['tel'] != ''): ?>
						<?php echo $person['tel'] ?>
					<?php endif ?>
					</span>
					|
					<?php $num = ($num+1)%2 ?>
				<?php endif ?>
			<?php endforeach ?>
		<?php endif ?>
	</td>
</tr>
<?php endif ?>