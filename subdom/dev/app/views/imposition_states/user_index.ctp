<h1>Stavy úkolů</h1>
<ul>
	<li><?php echo $html->link('Přidat stav úkolu', array('controller' => 'imposition_states', 'action' => 'add'))?></li>
</ul>

<?php if (empty($imposition_states)) { ?>
<p><em>V databázi nejsou žádné stavy úkolů</em></p>
<?php } else { ?>
<table class="top_heading">
	<tr>
		<th>ID</th>
		<th>Název</th>
		<th>&nbsp;</th>
	</tr>
<?php
	$odd = '';
	foreach ($imposition_states as $imposition_state) {
		$odd = ( $odd == ' class="odd"' ? '' : ' class="odd"' );	
?>
	<tr<?php echo $odd?>>
		<td><?php echo $imposition_state['ImpositionState']['id']?></td>
		<td><?php echo $imposition_state['ImpositionState']['name']?></td>
		<td class="actions">
			<?php echo $html->link('Upravit', array('controller' => 'imposition_states', 'action' => 'edit', $imposition_state['ImpositionState']['id']))?>
			<?php echo $html->link('Smazat', array('controller' => 'imposition_states', 'action' => 'delete', $imposition_state['ImpositionState']['id']), null, 'Opravdu chcete stav úkolu odstranit?')?>
		</td>
	</tr>
<?php } // end foreach?>
</table>

<?php } // end if?>

<ul>
	<li><?php echo $html->link('Přidat stav úkolu', array('controller' => 'imposition_states', 'action' => 'add'))?></li>
</ul>