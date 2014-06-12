<h1>Seznam způsobů platby</h1>

<table class="top_headed">
	<tr>
		<th>Název</th>
		<th>&nbsp;</th>
	</tr>
<?
	$odd = ' class="odd"'; 
	foreach ($payments as $payment) {
?>
	<tr<?php echo $odd?>>
		<td><?=$payment['Payment']['name'] ?></td>
		<td>
			<?=$html->link('upravit', array('controller' => 'payments', 'action' => 'edit', 'id' => $payment['Payment']['id'])) ?>
			<?=$html->link('smazat', array('controller' => 'payments', 'action' => 'delete', 'id' => $payment['Payment']['id']), array(), 'Opravdu chcete způsob platby odstranit?') ?>
		</td>
	</tr>
<? 
		$odd = $odd == ' class="odd"' ? '' : ' class="odd"';
	}
?>
</table>