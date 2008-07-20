<h1><?php echo $pageTitle ?></h1>
This page should be a dashboard.
<br />
<br />
<?php echo $html->link('Logout', array('controller' => 'users', 'action' => 'logout')) ?><br />
<?php echo $html->link('Change password', array('controller' => 'users', 'action' => 'changePassword')) ?><br />
<?php //echo $html->link(_"Delete account", array('controller' => 'accounts', 'action' => 'delete/' . $cauth->user('id')), null, "Are you sure you want to delete you account? Your profile will no longer appear on this site and you will not be able to log in.") ?><br />
<?php echo $html->link('User List', array('controller' => 'users', 'action' => 'listAll')) ?><br />
<?php echo $html->link('Project List', array('controller' => 'projects', 'action' => 'listAll')) ?><br />