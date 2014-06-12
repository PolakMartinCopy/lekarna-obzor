<h2>Kontrola doručených objednávek</h2>
<p>Nedoručené objednávky byly překontrolovány.</p>
<?
	if ( !empty($bad_orders) ){
?>
		<ul>
		<?
			foreach ( $bad_orders as $bad_order_id ){
		?>
			<li><?=$html->link($bad_order_id, array('controller' => 'orders', 'action' => 'view', $bad_order_id) ) ?></li>
		<?
			}
		?>
		</ul>
<?
	}
?>