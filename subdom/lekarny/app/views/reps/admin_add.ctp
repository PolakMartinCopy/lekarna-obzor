<h1>Vytvoření nového repa</h1>

<?=$form->create('Rep', array('action' => 'add')) ?>
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
</table>

<?=$form->submit('Odeslat') ?>
<?=$form->end() ?>