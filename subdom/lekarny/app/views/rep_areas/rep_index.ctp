<h1>Moje oblasti</h1>

<? if ( !empty($rep_areas) ) { ?>
<table class="top_headed">
	<tr>
		<th>Počáteční PSČ</th>
		<th>Koncové PSČ</th>
		<th>Oblast</th>
	</tr>
<?
	$odd = ' class="odd"';
	foreach ( $rep_areas as $area ) {
?>
	<tr<?php echo $odd?>>
		<td><?=$area['RepArea']['start_zip'] ?></td>
		<td><?=$area['RepArea']['end_zip'] ?></td>
		<td><?=$area['RepArea']['area'] ?></td>
	</tr>
<?
		$odd = $odd == ' class="odd"' ? '' : ' class="odd"';
	}
?>
</table>
<? } else { ?>
<p style="font-style:italic">Nemáte přiřazeny žádné oblasti.</p>
<? } ?>