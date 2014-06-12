<h1>Upravit stav řešení</h1>

<?php echo $form->create('SolutionState', array('url' => array('controller' => 'solution_states', 'action' => 'edit')))?>
<table class="left_heading">
	<tr>
		<th>Název</th>
		<td><?php echo $form->input('SolutionState.name', array('label' => false))?></td>
	</tr>
</table>
<?php echo $form->hidden('SolutionState.id')?>
<?php echo $form->submit('Upravit')?>
<?php echo $form->end() ?>