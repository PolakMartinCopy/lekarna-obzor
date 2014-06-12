<div class="mainContentWrapper">
<table id="customerLayout">
	<tr>
		<th>
			Seznam objednávek
		</th>
	</tr>
	<tr>
		<td>
			<table class="topHeading" width="100%">
				<tr>
					<th>číslo</th>
					<th>vytvořena</th>
					<th>cena</th>
					<th>stav</th>
					<th>&nbsp;</th>
				</tr>
		<?
			foreach ( $orders as $order ){
		?>
				<tr>
					<td><?=$order['Order']['id']?></td>
					<td><?=$order['Order']['created']?></td>
					<td><?=($order['Order']['subtotal_with_dph'] + $order['Order']['shipping_cost']) . '&nbsp;Kč' ?></td>
					<td><?
							$color = '';
							if ( !empty($order['Status']['color']) ){
								$color = ' style="color:#' . $order['Status']['color'] . '"';
							}
							echo '<span' . $color . '>' . $order['Status']['name'] . '</span>';
						?>
					</td>
					<td>
						<?=$html->link('detaily', array('controller' => 'customers', 'action' => 'order_detail', $order['Order']['id']));?>
					</td>
				</tr>
		<?
			}
		?>
			</table>
		</td>
	</tr>

</table>
</div>