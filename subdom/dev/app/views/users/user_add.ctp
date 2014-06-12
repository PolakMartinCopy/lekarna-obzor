<h1>Přidat uživatele</h1>

<?php echo $form->create('User', array('url' => array('controller' => 'users', 'action' => 'add')))?>
<table class="left_heading">
	<tr>
		<th>Křestní jméno<sup>*</sup></th>
		<td><?php echo $form->input('User.first_name', array('label' => false))?></td>
	</tr>
	<tr>
		<th>Příjmení<sup>*</sup></th>
		<td><?php echo $form->input('User.last_name', array('label' => false))?></td>
	</tr>
	<tr>
		<th>Telefon<sup>*</sup></th>
		<td><?php echo $form->input('User.phone', array('label' => false))?></td>
	</tr>
	<tr>
		<th>Email<sup>*</sup></th>
		<td><?php echo $form->input('User.email', array('label' => false))?></td>
	</tr>
	<tr>
		<th>Login<sup>*</sup></th>
		<td><?php echo $form->input('User.login', array('label' => false))?></td>
	</tr>
	<tr>
		<th>Heslo<sup>*</sup></th>
		<td><?php echo $form->input('User.password', array('label' => false))?></td>
	</tr>
	<tr>
		<th>Typ uživatele<sup>*</sup></th>
		<td><?php echo $form->input('User.user_type_id', array('options' => $user_types, 'empty' => false, 'label' => false))?></td>
	</tr>
</table>
<ul>
	<li><small><sup>*</sup> - Pole musí být neprázdné</small></li>
</ul>	

<?php echo $form->submit('Uložit')?>
<?php echo $form->end()?>