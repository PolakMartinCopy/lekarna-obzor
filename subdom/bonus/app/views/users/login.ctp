<h1>Přihlášení uživatele</h1>
<?php echo $session->flash('auth') ?>
<?php echo $form->create('User', array('url' => array('controller' => 'users', 'action' => 'login')))?>
<table class="left_heading">
	<tr>
		<th>Přihlašovací jméno</th>
		<td><?php echo $form->input('User.login', array('label' => false))?></td>
	</tr>
	<tr>
		<th>Heslo</th>
		<td><?php echo $form->input('User.password', array('label' => false))?></td>
	</tr>
</table>
<?php echo $form->submit('Vstoupit')?>
<?php echo $form->end() ?>