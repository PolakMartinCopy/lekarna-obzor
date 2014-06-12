<?
foreach ( $statuses as $status ){
	echo $html->link(
			'<span style="font-size:11px;color:#' . $status['Status']['color'] . '">' . $status['Status']['name'] . ' (' . $status['Status']['count'] . ')</span>',
			array('controller' => 'orders', 'action' => 'index', 'status_id' => $status['Status']['id']),
			array('escape' => false),
			false
	) . " ";
}
?>