<h2 class="filter">Filtrovat seznam objednávek:</h2>
<?
foreach ( $statuses as $status ){
	echo $html->link(
			'<span style="font-size:12px;color:#' . $status['Status']['color'] . '">' . $status['Status']['name'] . ' (' . $status['Status']['count'] . ')</span>',
			array('controller' => 'orders', 'action' => 'index', 'status_id' => $status['Status']['id'], 'rep' => true), 
			array(),
			false,
			false
	) . " ";
}
?>