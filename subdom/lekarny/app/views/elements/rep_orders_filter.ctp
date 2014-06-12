<h2>Filtrovat objednávky dle stavu</h2>
<?
$url_arr = array('controller' => 'orders', 'action' => 'index', 'rep' => true);
foreach ($this->passedArgs as $arg_name => $arg_value) {
	if ($arg_name != 'status_id') {
		$url_arr = array_merge(array($arg_name => $arg_value), $url_arr);
	}
}
echo $html->link('<span style="font-size:11px">všechny (' . $orders_count . ')</span>', $url_arr, array(), false, false) . ' ';

foreach ( $statuses as $status ){
	$url_arr = array('controller' => 'orders', 'action' => 'index', 'status_id' => $status['Status']['id'], 'rep' => true);
	foreach ($this->passedArgs as $arg_name => $arg_value) {
		$url_arr = array_merge(array($arg_name => $arg_value), $url_arr);
	}
	echo $html->link(
			'<span style="font-size:11px;color:#' . $status['Status']['color'] . '">' . $status['Status']['name'] . ' (' . $status['Status']['count'] . ')</span>',
			$url_arr, 
			array(),
			false,
			false
	) . " ";
}
?>