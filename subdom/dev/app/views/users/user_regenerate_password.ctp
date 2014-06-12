<h1>Vygenerovat heslo</h1>
<p>Zadejte emailovou adresu, pod kterou jste uložen v systému. Bude Vám vygenerováno nové heslo a zasláno na tuto emailovou adresu.</p>
<?php echo $form->create('User', array('url' => array('controller' => 'users', 'action' => 'regenerate_password')))?>
<table class="left_heading">
	<tr>
		<th>Email</th>
		<td><?php echo $form->input('User.email', array('label' => false))?></td>
	</tr>
</table>
<?php echo $form->submit('Odeslat')?>
<?php echo $form->end() ?>