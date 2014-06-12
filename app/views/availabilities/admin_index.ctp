<h2>Dostupnosti produktů</h2>
<table class="topHeading" cellpadding="5" cellspacing="3">
	<tr>
		<th>ID</th>
		<th>Název</th>
		<th>Barva</th>
		<th>Povolit vložení<br />do košíku?</th>
		<th>&nbsp;</th>
	</tr>
<?
	foreach ( $availabilities as $availability ){
		echo '
			<tr>
				<td>
					' . $availability['Availability']['id'] . '
				</td>
				<td>
					' . $availability['Availability']['name'] . '
				</td>
				<td>
					' . $availability['Availability']['color'] . '
				</td>
				<td>
					' . ( $availability['Availability']['cart_allowed'] == 1 ? 'ano' : 'ne' ) . '
				</td>
				<td>
					' . $html->link('upravit', array('action' => 'edit', $availability['Availability']['id'])) . '<br />' . $html->link('smazat', array('action' => 'delete', $availability['Availability']['id']), null, 'Opravdu chcete tento stav smazat?') . '
				</td>
			</tr>
		';
	}
?>
</table>
<div class="actions">
	<ul>
		<li><?=$html->link('Vytvořit novou dostupnost', array('action' => 'add'))?></li>
	</ul>
</div>