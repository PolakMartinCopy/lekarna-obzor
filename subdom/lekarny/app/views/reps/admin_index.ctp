<h1>Seznam repů</h1>

<? if ( !(empty($reps)) ) { ?>
<table class="top_headed" cellpadding="5" cellspacing="3">
	<tr>
		<th>Jméno</th>
		<th>Příjmení</th>
		<th>Email</th>
		<th>Aktivní</th>
		<th>&nbsp;</th>
	</tr>
<?
	$odd = ' class="odd"';
	foreach ( $reps as $rep) {
?>
	<tr<?php echo $odd?>>
		<td><?=$rep['Rep']['first_name'] ?></td>
		<td><?=$rep['Rep']['last_name'] ?></td>
		<td><?=$rep['Rep']['email'] ?></td>
		<td><?=($rep['Rep']['active']) ? 'Ano' : 'Ne' ?></td>
		<td>
			<?
				echo $html->link('Upravit', array('controller' => 'reps', 'action' => 'edit', 'id' => $rep['Rep']['id']));
				echo ' | ';
				if ( $rep['Rep']['active'] ) {
					echo $html->link('Deaktivovat', array('controller' => 'reps', 'action' => 'deactivate', 'id' => $rep['Rep']['id']));
				} else {
					echo $html->link('Aktivovat', array('controller' => 'reps', 'action' => 'activate', 'id' => $rep['Rep']['id']));
				}
				echo ' | ';
				echo $html->link('Iniciovat', array('controller' => 'reps', 'action' => 'notify_rep', 'id' => $rep['Rep']['id']));
				echo ' | ';
				echo $html->link('Oblasti', array('controller' => 'rep_areas', 'action' => 'index', 'rep_id' => $rep['Rep']['id']));
			?>
		</td>
	</tr>
<?
		$odd = $odd == ' class="odd"' ? '' : ' class="odd"';
	}
?>
</table>
<? } else { ?>
<p style="font-style:italic">V databázi nejsou žádní repové</p>
<? } ?>
		