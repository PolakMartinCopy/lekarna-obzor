<h1>Stavy objednávek</h1>
<?
	if ( !empty($statuses) ){
?>
<table class="top_headed" cellpadding="5" cellspacing="3">
	<tr>
		<th>název</th>
		<th>barva</th>
		<th>uzavřený</th>
		<th>šablona</th>
		<th>závislá pole</th>
		<th>&nbsp;</th>
	</tr>
<?
	$odd = ' class="odd"';
	foreach ( $statuses as $status ){
		echo '
			<tr' . $odd . '>
				<td>
					' . $status['Status']['name'] . '
				</td>
				<td>
					<span style="color:' . $status['Status']['color'] . '">' . $status['Status']['color'] . '</span>
				</td>
				<td>
					' . ( $status['Status']['closed'] == 0 ? 'NE' : 'ANO' )   . '
				</td>
				<td>
					' . ( isset($status['MailTemplate']['subject']) ? $status['MailTemplate']['subject'] : 'email se neposílá' )   . '
				</td>
				<td>
					' . ( isset($status['Status']['requested_fields']) ? str_replace("\n", "<br />", $status['Status']['requested_fields']) : '&nbsp;' )   . '
				</td>
				<td>
					' . $html->link('upravit', array('action' => 'edit', 'id' => $status['Status']['id'])) . ' | ' . $html->link('smazat', array('action' => 'delete', 'id' => $status['Status']['id']), null, 'Opravdu chcete tento stav smazat?') . '
				</td>
			</tr>
		';
		$odd = $odd == ' class="odd"' ? '' : ' class="odd"';
	}
?>
</table>
<? } ?>