<h1>Oblasti - <?=$rep['Rep']['first_name'] . ' ' . $rep['Rep']['last_name'] ?></h1>
<ul>
	<li><?=$html->link('Přidat oblast', array('controller' => 'rep_areas', 'action' => 'add', 'rep_id' => $rep['Rep']['id'])) ?></li>
</ul>

<? if ( !empty($rep_areas) ) { ?>
<table class="top_headed">
	<tr>
		<th>Počáteční PSČ</th>
		<th>Koncové PSČ</th>
		<th>Oblast</th>
		<th>&nbsp;</th>
	</tr>
<?php 
	$odd = ' class="odd"';
	foreach ( $rep_areas as $area ) {
?>
	<tr<?php echo $odd?>>
		<td><?=$area['RepArea']['start_zip'] ?></td>
		<td><?=$area['RepArea']['end_zip'] ?></td>
		<td><?=$area['RepArea']['area'] ?></td>
		<td>
			<?
				echo $html->link('Upravit', array('controller' => 'rep_areas', 'action' => 'edit', 'id' => $area['RepArea']['id'])) . ' | ';
				echo $html->link('Smazat', array('controller' => 'rep_areas', 'action' => 'delete', 'id' => $area['RepArea']['id'])) . ' | ';
				echo $form->create('RepArea', array('url' => array('controller' => 'rep_areas', 'action' => 'move', $area['RepArea']['id']), 'style' => 'display:inline'));
				echo $form->hidden('RepArea.id', array('value' => $area['RepArea']['id']));
				echo $form->submit('Přesunout PSČ k', array('div' => false));
				echo $form->select('RepArea.rep_id', $reps, null, array('div' => false), false);
				echo $form->end();
			?>
		</td>
	</tr>
<?
		$odd = $odd == ' class="odd"' ? '' : ' class="odd"'; 
	}
?>
</table>
<? } else { ?>
<p style="font-style:italic">Tento rep nemá přiřazeny žádné oblasti.</p>
<? } ?>