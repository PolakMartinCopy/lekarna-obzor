<h1>Typy obchodních jednání</h1>
<ul>
	<li><?php echo $html->link('Přidat typ obchodního jednání', array('controller' => 'business_session_types', 'action' => 'add'))?></li>
</ul>
<?php if (empty($business_session_types)) { ?>
<p><em>V databázi nejsou žádné typy obchodních jednání.</em></p>
<?php } else { ?>
<table class="top_heading">
	<tr>
		<th>ID</th>
		<th>Název</th>
		<th>&nbsp;</th>
	</tr>
<?php
	$odd = '';
	foreach ($business_session_types as $business_session_type) {
		$odd = ( $odd == ' class="odd"' ? '' : ' class="odd"' );
?>
	<tr<?php echo $odd?>>
		<td><?php echo $business_session_type['BusinessSessionType']['id']?></td>
		<td><?php echo $business_session_type['BusinessSessionType']['name']?></td>
		<td class="actions">
			<?php echo $html->link('Upravit', array('controller' => 'business_session_types', 'action' => 'edit', $business_session_type['BusinessSessionType']['id']))?>
			<?php echo $html->link('Smazat', array('controller' => 'business_session_types', 'action' => 'delete', $business_session_type['BusinessSessionType']['id']), null, 'Opravdu si přejete typ obchodního jednání ' . $business_session_type['BusinessSessionType']['name'] . ' smazat?')?>
		</td>
	</tr>
<?php } // end foreach?>
</table>
<?php } // end if?>
<ul>
	<li><?php echo $html->link('Přidat typ obchodního jednání', array('controller' => 'business_session_types', 'action' => 'add'))?></li>
</ul>