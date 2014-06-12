<h1>Úprava osobních údajů</h1>

<?=$form->create('Rep', array('url' => '/rep/reps/edit/')) ?>
<table class="left_headed">
	<tr>
		<th>Jméno</th>
		<td><?=$form->input('Rep.first_name', array('label' => false)) ?></td>
	</tr>
	<tr>
		<th>Příjmení</th>
		<td><?=$form->input('Rep.last_name', array('label' => false)) ?></td>
	</tr>
	<tr>
		<th>Email</th>
		<td><?=$form->input('Rep.email', array('label' => false)) ?></td>
	</tr>
	<tr>
		<th>Login</th>
		<td><?=$form->input('Rep.login', array('label' => false)) ?></td>
	</tr>
	<tr>
		<th>Heslo</th>
		<td><?=$form->input('Rep.password', array('label' => false)) ?></td>
	</tr>
</table>

<?
	echo $form->hidden('Rep.id');
	echo $form->submit('Odeslat');
	echo $form->end();
?>