<h1>Historie objednávek</h1>
<? if ( empty($orders) ){ ?>
<p><em>V systému nejsou objednávky. Pokračujte <a href="/users/carts/add">vytvořením nové objednávky</a>.</em></p>
<?php } else { ?>
	<table class="top_headed" cellpadding="5" cellspacing="3">
		<tr>
			<th>
				č. obj.
			</th>
			<th>
				datum
			</th>
			<th>
				cena bez DPH <br/>( s DPH )
			</th>
			<th>
				stav
			</th>
			<th>
				&nbsp;
			</th>
		</tr>
<?
	foreach ( $orders as $order ){
?>
		<tr>
			<td>
				<?=$order['Order']['id'] ?>
			</td>
			<td>
				<?=text_date($order['Order']['created']) ?>
			</td>
			<td>
				<?=$order['Order']['subtotal'] . '&nbsp;Kč' ?> (<?=$order['Order']['subtotal_tax'] . '&nbsp;Kč' ?>)
			</td>
			<td>
				<?=$order['Status']['name'] ?>
			</td>
			<td>
				<?=$html->link('zobrazit detaily', array('users' => true, 'controller' => 'orders', 'action' => 'view', $order['Order']['id'])) ?> |
				<?=$html->link('opakovat objednávku', array('users' => true, 'controller' => 'orders', 'action' => 'duplicate', $order['Order']['id'])) ?>
			</td>
		</tr>
<?
	}
}
?>
	</table>