<h2>Detail zákazníka <?php echo $customer['Customer']['last_name']. ' ' . $customer['Customer']['first_name'] ?></h2>
<table>
	<tr>
		<td rowspan="2">
			<table class="leftHeading">
				<tr>
					<th>
						jméno
					</th>
					<td>
						<?php echo $customer['Customer']['last_name'] ?>
					</td>
				</tr>
				<tr>
					<th>
						příjmení
					</th>
					<td>
						<?php echo $customer['Customer']['first_name'] ?>
					</td>
				</tr>
				<tr>
					<th>
						registrace
					</th>
					<td>
						<?php echo $customer['Customer']['created'] ?>
						<br />
						zdroj: <?php echo $customer['Customer']['registration_source'] ?>
						<br />
						potvrzen: <?php echo $customer['Customer']['confirmed'] ?>
						<br />
						<?php echo $html->link('smazat zákazníka z databáze', array('controller' => 'customers', 'action' => 'delete', $customer['Customer']['id']), array(), 'Opravdu si přejete zákazníka odstranit z databáze?')?>
					</td>
				</tr>
				<tr>
					<th>
						telefon
					</th>
					<td>
						<?php echo $customer['Customer']['phone'] ?>
					</td>
				</tr>
				<tr>
					<th>
						email
					</th>
					<td>
						<?php echo $customer['Customer']['email'] ?>
					</td>
				</tr>
				<tr>
					<th>
						login
					</th>
					<td>
						<?php echo $customer['Customer']['login'] ?>
					</td>
				</tr>
			<?
				if ( !empty($customer['Customer']['company_name']) || !empty($customer['Customer']['company_ico']) || !empty($customer['Customer']['company_dic']) ){
			?>
				<tr>
					<th>
						název společnosti
					</th>
					<td>
						<?php echo $customer['Customer']['company_name'] ?>
					</td>
				</tr>
				<tr>
					<th>
						IČO
					</th>
					<td>
						<?php echo $customer['Customer']['company_ico'] ?>
					</td>
				</tr>
				<tr>
					<th>
						DIČ
					</th>
					<td>
						<?php echo $customer['Customer']['company_dic'] ?>
					</td>
				</tr>
				
			<?
				}
			?>
			</table>
		</td>
		<td>
			<strong>fakturační adresa</strong><br />
			<?
			foreach ( $customer['Address'] as $address ){
				if ( $address['type'] == 'f' ){
					echo $address['name'] . '<br />';
					echo $address['street'] . ' ' . $address['street_no'] . '<br />';
					echo $address['zip'] . ' ' . $address['city'] . '<br />';
					echo $html->link('smazat adresu', array('controller' => 'addresses', 'action' => 'delete', $address['id'])) . '<br />';
				}
			}
			?>
		</td>
	</tr>
	<tr>
		<td>
			<strong>doručovací adresa</strong><br />
			<?
			foreach ( $customer['Address'] as $address ){
				if ( $address['type'] == 'd' ){
					echo $address['name'] . '<br />';
					echo $address['street'] . ' ' . $address['street_no'] . '<br />';
					echo $address['zip'] . ' ' . $address['city'] . '<br />';
					echo $html->link('smazat adresu', array('controller' => 'addresses', 'action' => 'delete', $address['id'])) . '<br />';
				}
			}
			?>
		</td>
	</tr>
	<tr>
		<th>
			Objednávky zákazníka:
		</th>
		<td>&nbsp;</td>
	</tr>
	<?php 
		if ( !empty($customer['Order']) ){
	?>
		<tr>
			<th>ID obj.</th>
			<td>hodnota</td>
		</tr>
	<?php	
			foreach ( $customer['Order'] as $o ){
	?>
		<tr>
			<th><?php echo $html->link($o['id'], array('controller' => 'orders', 'action' => 'view', $o['id']))?></th>
			<td><?php echo $o['subtotal_with_dph']?>&nbsp;Kč</td>
		</tr>
	<?php
				
			}
		} else {
	?>
		<tr>
			<th>&nbsp;</th>
			<td>Zákazník zatím neprovedl žádné objednávky.</td>
		</tr>
	<?php
		}
	?>
</table>