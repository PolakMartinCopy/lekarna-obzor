<h1>Stavy obchodních jednání</h1>
<ul>
	<li><?php echo $html->link('Přidat stav obchodního jednání', array('controller' => 'business_session_states', 'action' => 'add'))?></li>
</ul>
<?php if (empty($business_session_states)) { ?>
<p><em>V databázi nejsou žádné stavy obchodních jednání.</em></p>
<?php } else { ?>
<table class="top_heading">
	<tr>
		<th>ID</th>
		<th>Název</th>
		<th>&nbsp;</th>
	</tr>
<?php
	$odd = '';
	foreach ($business_session_states as $business_session_state) {
		$odd = ( $odd == ' class="odd"' ? '' : ' class="odd"' );
?>
	<tr<?php echo $odd?>>
		<td><?php echo $business_session_state['BusinessSessionState']['id']?></td>
		<td><?php echo $business_session_state['BusinessSessionState']['name']?></td>
		<td class="actions">
			<?php echo $html->link('Upravit', array('controller' => 'business_session_states', 'action' => 'edit', $business_session_state['BusinessSessionState']['id']))?>
			<?php echo $html->link('Smazat', array('controller' => 'business_session_states', 'action' => 'delete', $business_session_state['BusinessSessionState']['id']), null, 'Opravdu si přejete stav obchodního jednání ' . $business_session_state['BusinessSessionState']['name'] . ' smazat?')?>
		</td>
	</tr>
<?php } // end foreach?>
</table>
<?php } // end if?>
<ul>
	<li><?php echo $html->link('Přidat stav obchodního jednání', array('controller' => 'business_session_states', 'action' => 'add'))?></li>
</ul>