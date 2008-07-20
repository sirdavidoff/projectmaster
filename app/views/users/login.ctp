<h1 style="margin-top:0px"><?php echo $pageTitle ?></h1>

<?php echo $forms->create('User', array('action' => 'login')) ?>

<div class="formElementTitle"><?php echo "Username" ?></div>
<?php echo $forms->text('User.username', array('size' => '40', 'focus' => true))?>
<br />
<div class="formElementTitle"><?php echo "Password" ?></div>
<?php echo $forms->password('User.passwd', array('size' => '40'))?>
<br />
<br />
<?php echo $forms->end('Login') ?>
