<h1>Přidat stav řešení</h1>

<?php echo $form->create('SolutionState', array('url' => array('controller' => 'solution_states', 'action' => 'add')))?>
<table class="left_heading">
	<tr>
		<th>Název</th>
		<td><?php echo $form->input('SolutionState.name', array('label' => false))?></td>
	</tr>
</table>
<?php echo $form->submit('Uložit')?>
<?php echo $form->end() ?>