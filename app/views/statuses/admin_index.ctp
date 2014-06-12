<h2>Stavy objednávek</h2>
<table class="topHeading" cellpadding="5" cellspacing="3">
	<tr>
		<th>ID</th>
		<th>Název</th>
		<th>Barva</th>
		<th>Šablona</th>
		<th>Závislá pole</th>
		<th>&nbsp;</th>
	</tr>
<?
	foreach ( $statuses as $status ){
		echo '
			<tr>
				<td>
					' . $status['Status']['id'] . '
				</td>
				<td>
					' . $status['Status']['name'] . '
				</td>
				<td>
					' . $status['Status']['color'] . '
				</td>
				<td>
					' . $status['MailTemplate']['subject'] . '
				</td>
				<td>
					' . str_replace("\n", "<br />", $status['Status']['requested_fields']) . '
				</td>
				<td>
					' . $html->link('upravit', array('action' => 'edit', $status['Status']['id'])) . '<br />' . $html->link('smazat', array('action' => 'delete', $status['Status']['id']), null, 'Opravdu chcete tento stav smazat?') . '
				</td>
			</tr>
		';
	}
?>
</table>
<div class="actions">
	<ul>
		<li><?=$html->link('Vytvořit nový status', array('action' => 'add'))?></li>
	</ul>
</div>