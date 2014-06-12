<h1>Stavy řešení</h1>

<?php if (empty($solution_states)) { ?>
<p><em>V databázi nejsou žádné stavy řešení</em></p>
<?php } else { ?>
<table class="top_heading">
	<tr>
		<th>ID</th>
		<th>Název</th>
		<th>&nbsp;</th>
	</tr>
<?php
	$odd = '';
	foreach ($solution_states as $solution_state) {
		$odd = ( $odd == ' class="odd"' ? '' : ' class="odd"' );	
?>
	<tr<?php echo $odd?>>
		<td><?php echo $solution_state['SolutionState']['id']?></td>
		<td><?php echo $solution_state['SolutionState']['name']?></td>
		<td class="actions">
			<?php echo $html->link('Upravit', array('controller' => 'solution_states', 'action' => 'edit', $solution_state['SolutionState']['id']))?>
			<?php echo $html->link('Smazat', array('controller' => 'solution_states', 'action' => 'delete', $solution_state['SolutionState']['id']), null, 'Opravdu chcete stav řešení odstranit?')?>
		</td>
	</tr>
<?php } // end foreach?>
</table>

<?php } // end if?>