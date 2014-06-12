<h2>Způsoby dopravy</h2>
<table class="topHeading" cellpadding="5" cellspacing="3">
	<tr>
		<th>ID</th>
		<th>Název</th>
		<th>Dopravne</th>
		<th>Zdarma od</th>
		<th>Závislá pole</th>
		<th>URL prefix</th>
		<th>URL postfix</th>
	</tr>
<?
	foreach ( $shippings as $shipping ){
		echo '
			<tr>
				<td>
					' . $shipping['Shipping']['id'] . '
				</td>
				<td>
					' . $shipping['Shipping']['name'] . '
				</td>
				<td>
					' . $shipping['Shipping']['price'] . '
				</td>
				<td>
					' . $shipping['Shipping']['free'] . '
				</td>
				<td>
					' . $shipping['Shipping']['tracker_prefix'] . '
				</td>
				<td>
					' . $shipping['Shipping']['tracker_postfix'] . '
				</td>
				<td>
					' . $html->link('upravit', array('action' => 'edit', $shipping['Shipping']['id'])) . '<br />' . $html->link('smazat', array('action' => 'delete', $shipping['Shipping']['id']), null, 'Opravdu chcete tento způsob dopravy smazat?') . '
				</td>
			</tr>
		';
	}
?>
</table>
<div class="actions">
	<ul>
		<li><?=$html->link('Vytvořit nový způsob dopravy', array('action' => 'add'))?></li>
	</ul>
</div>