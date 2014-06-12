<h2>Seznam zákazníků</h2>
<?
	$count = count($alphabet);
	setlocale( LC_ALL, 'cs_CZ' );
	for ( $i = 0; $i < $count; $i++){
		echo $html->link(strtoupper($alphabet[$i]), array('controller' => 'customers', 'action' => 'list', $alphabet[$i]))  . ' ';
	}

	if ( count($customers) > 0 ){
?>
		<table class="topHeading">
			<tr>
				<th>Příjmení a Jméno</th>
				<th>Kontakty</th>
				<th>Objednávky</th>
				<th>Adresy</th>
				<th>
					Potvrzen / Zdroj
				</th>
				<th>&nbsp;</th>
			</tr>
<?
	foreach ( $customers as $customer ){
?>
			<tr>
				<td><?php echo $html->link($customer['Customer']['last_name'] . '&nbsp;' . $customer['Customer']['first_name'], array('controller' => 'customers', 'action' => 'view', $customer['Customer']['id']), array('escape' => false), false, false); ?></td>
				<td><?php echo $customer['Customer']['phone']?><br /><?php echo $customer['Customer']['email']?></td>
				<td>
					<?
						foreach ( $customer['Customer']['orders'] as $order ){
							echo $html->link($order['Order']['id'], array('controller' => 'orders', 'action' => 'view', $order['Order']['id'])) . '<br />';
						}
					?>
				</td>
				<td>
					<?
						foreach ( $customer['Customer']['addresses'] as $address ){
							echo $html->link($address['Address']['name'], array('controller' => 'orders', 'action' => 'view', $address['Address']['id'])) . '<br />';
						}
					?>
				</td>
				<td>
					<?php echo $customer['Customer']['confirmed'] ?><br />
					<?php echo $customer['Customer']['registration_source'] ?>
				</td>
				<td>
					<?php echo $html->link('zobrazit', array('controller' => 'customers', 'action' => 'view', $customer['Customer']['id']), array(), false, false); ?><br />
					<?php echo $html->link('smazat zákazníka z&nbsp;databáze', array('controller' => 'customers', 'action' => 'delete', $customer['Customer']['id']), array('style' => 'color:red;', 'escape' => false), 'Opravdu si přejete zákazníka odstranit z databáze?')?>
				</td>
			</tr>
<?
	}
?>
		</table>
<?
	} else {
		echo '<p>Žádný zákazník s počátečním písmenem <strong>' . strtoupper($id) . '</strong> nebyl nalezen.</p>';
	}
?>