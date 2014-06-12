<div class="mainContentWrapper">
	<table id="customerLayout">
		<tr>
			<th colspan="2">
				Objednané položky
			</th>
		</tr>
		<tr>
			<td colspan="2">
				<table class="topHeading" width="100%">
					<tr>
						<th>název</th>
						<th>jedn. cena</th>
						<th>cena celkem</th>
					</tr>
					<?
						foreach ( $order['OrderedProduct'] as $product ){
							echo '
								<tr>
									<td>
										' . $product['product_quantity'] . ' &times; ' . $product['Product']['name'] . '
									</td>
									<td>
										' . $product['product_price_with_dph'] . ' Kč
									</td>
									<td>
										' . ($product['product_price_with_dph'] * $product['product_quantity']) . ' Kč
									</td>
								</tr>
							';
						}
					?>
					<tr>
						<th colspan="2">
							objednané zboží celkem:
						</th>
						<td>
							<?=$order['Order']['subtotal_with_dph']?> Kč
						</td>
					</tr>
					<tr>
						<th colspan="2">
							způsob dopravy:
						</th>
						<td>
							<?=$order['Shipping']['name']?> (<?=$order['Order']['shipping_cost']?> Kč)
						</td>
					</tr>
					<tr>
						<th colspan="2">
							celková cena objednávky:
						</th>
						<td>
							<?=($order['Order']['subtotal_with_dph'] + $order['Order']['shipping_cost'])?> Kč
						</td>
					</tr>
				</table>
			</td>
		</tr>
		<tr>
			<th>
				Fakturační adresa
			</th>
			<th>
				Doručovací adresa
			</th>
		</tr>
		<tr>
			<td>
				<?=$order['Order']['customer_name']?><br />
				<?=$order['Order']['customer_street']?><br />
				<?=$order['Order']['customer_zip'] . ' ' . $order['Order']['customer_city']?><br />
				<?=$order['Order']['customer_state']?><br />
			</td>
			<td>
				<?=$order['Order']['delivery_name']?><br />
				<?=$order['Order']['delivery_street']?><br />
				<?=$order['Order']['delivery_zip'] . ' ' . $order['Order']['delivery_city']?><br />
				<?=$order['Order']['delivery_state']?><br />
			</td>
		</tr>
	</table>
</div>
