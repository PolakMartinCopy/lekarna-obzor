<h1>Typy výročí</h1>
<ul>
	<li><?php echo $html->link('Přidat typ výročí', array('controller' => 'anniversary_types', 'action' => 'add'))?></li>
</ul>
<?php if (empty($anniversary_types)) { ?>
<p><em>V databázi nejsou žádné typy výročí.</em></p>
<?php } else { ?>
<table class="top_heading">
	<tr>
		<th>ID</th>
		<th>Název</th>
		<th>&nbsp;</th>
	</tr>
<?php 
	$odd = '';
	foreach ($anniversary_types as $anniversary_type) {
		$odd = ( $odd == ' class="odd"' ? '' : ' class="odd"' );
?>
	<tr<?php echo $odd?>>
		<td><?php echo $anniversary_type['AnniversaryType']['id']?></td>
		<td><?php echo $anniversary_type['AnniversaryType']['name']?></td>
		<td class="actions">
			<?php echo $html->link('Upravit', array('controller' => 'anniversary_types', 'action' => 'edit', $anniversary_type['AnniversaryType']['id']))?>
			<?php echo $html->link('Smazat', array('controller' => 'anniversary_types', 'action' => 'delete', $anniversary_type['AnniversaryType']['id']), null, 'Opravdu si přejete typ výročí ' . $anniversary_type['AnniversaryType']['name'] . ' smazat?')?>
		</td>
	</tr>
<?php } // end foreach?>
</table>
<?php } // end if?>
<ul>
	<li><?php echo $html->link('Přidat typ výročí', array('controller' => 'anniversary_types', 'action' => 'add'))?></li>
</ul>