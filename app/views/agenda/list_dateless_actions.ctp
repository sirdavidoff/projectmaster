<?php if (isset($actions) && count($actions) > 0): ?>
	<tr>
		<td colspan="3">
			<h2>
				Other actions
			</h2>
		</td>
	</tr>
	<?php foreach ($actions as $da): ?>
		<?php echo $this->renderElement('../actions/list_row', array('data' => $da)) ?>
	<?php endforeach ?>
<?php endif ?>