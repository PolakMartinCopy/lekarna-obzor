<h1>Seznam způsobů dopravy</h1>

<table class="top_headed">
	<tr>
		<th>Název</th>
		<th>Cena</th>
		<th>Doprava zdarma od</th>
		<th>Tracker pre</th>
		<th>Tracker post</th>
		<th>&nbsp;</th>
	</tr>
<?
	$odd = ' class="odd"'; 
	foreach ($shippings as $shipping) {
?>
	<tr<?php echo $odd?>>
		<td><?=$shipping['Shipping']['name'] ?></td>
		<td><?=$shipping['Shipping']['price'] ?></td>
		<td><?=$shipping['Shipping']['free'] ?></td>
		<td><?=$shipping['Shipping']['tracker_prefix'] ?></td>
		<td><?=$shipping['Shipping']['tracker_postfix'] ?></td>
		<td>
			<?=$html->link('upravit', array('controller' => 'shippings', 'action' => 'edit', 'id' => $shipping['Shipping']['id'])) ?>
			<?=$html->link('smazat', array('controller' => 'shippings', 'action' => 'delete', 'id' => $shipping['Shipping']['id']), array(), 'Opravdu chcete způsob dopravy odstranit?') ?>
		</td>
	</tr>
<? 
		$odd = $odd == ' class="odd"' ? '' : ' class="odd"';	
	}
?>
</table>