<h1>User Added</h1>

<br />
Login info for <?php echo $html->link($format->name($this->data['User']['first_name'], $this->data['User']['last_name']), '/users/view/' . $this->data['User']['id']) ?>:

<div class="flashNotice" style="margin: 15px 0px">
	
	<table cellspacing="0" cellpadding="4">
		<tr>
			<td valign="top">Username:</td>
			<td valign="top"><div style="margin-left: 15px"><?php echo $this->data['User']['username'] ?></div></td>
		</tr>
		<tr>
			<td valign="top">Password:</td>
			<td valign="top"><div style="margin-left: 15px"><?php echo $this->data['User']['password'] ?></div></td>
		</tr>
	</table>

</div>

You should probably let <?php echo $this->data['User']['first_name'] ?> know about this.
<br /><br />
Would you like to <?php echo $html->link('add another user', '/users/add') ?>?

