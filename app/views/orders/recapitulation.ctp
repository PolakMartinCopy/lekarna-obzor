<div class="mainContentWrapper">
	<table id="recapWrapper" border="0" cellpadding="5" cellspacing="0">
		<tr>
			<td valign="top">
				<h3>Adresa doručení</h3>
				<? echo $address['name']?><br />
				<? echo $address['street'] . ' ' . $address['street_no']?><br />
				<? echo $address['zip'] . ' ' . $address['city'];?><br />
				<? echo $address['state']?><br />
				<?=$html->link('upravit', array('controller' => 'orders', 'action' => 'address_edit', 'type' => 'd'), array('class' => 'smallLinkEdit')) ?>
				<h3>Fakturační adresa</h3>
				<? echo $address_payment['name']?><br />
				<? echo $address_payment['street'] . ' ' . $address_payment['street_no']?><br />
				<? echo $address_payment['zip'] . ' ' . $address_payment['city']?><br />
				<? echo $address_payment['state']?><br />
				<?=$html->link('upravit', array('controller' => 'orders', 'action' => 'address_edit', 'type' => 'f'), array('class' => 'smallLinkEdit')) ?>
			</td>
			<td class="leftBorder" valign="top">
				<h3>Objednané zboží</h3>
				<a class="smallLinkEdit" href="/kosik">upravit</a>
				<table id="recapProducts" cellpadding="5" cellspacing="0">
					<tr>
						<th style="width:50%">Název produktu</th>
						<th style="width:30%">Množství</th>
						<th>Cena za kus</th>
						<th>Cena celkem</th>
					</tr>
					<?
						$final_price = 0;
						$final_price_wout = 0;
						$first = true;
						$border = '';
						foreach ( $cart_products as $cart_product ){
							$tax_class_coef = 1 + $cart_product['Product']['TaxClass']['value'] / 100;
							$final_price_wout = $final_price_wout + (($cart_product['CartsProduct']['price_with_dph'] * $cart_product['CartsProduct']['quantity']) / $tax_class_coef );
							$final_price = $final_price + $cart_product['CartsProduct']['price_with_dph'] * $cart_product['CartsProduct']['quantity'];
							if ( $first ){
								$border = ' style="border-top:1px solid #EDF9FF"';
								$first = false;
							}
							echo '
								<tr' . $border . '>
									<td>
										<strong>' . $cart_product['Product']['name'] . '</strong>';

										if ( !empty($cart_product['CartsProduct']['product_attributes']) ){
											echo '<br /><div style="font-size:11px;padding-left:20px;">';
											foreach ( $cart_product['CartsProduct']['product_attributes'] as $option => $value ){
												echo '- <strong>' . $option . '</strong>: ' . $value . '<br />';
											}
											echo '</div>';
										}

							echo '
									</td>
									<td>
										' . $cart_product['CartsProduct']['quantity'] . '&nbsp;ks
									</td>
									<td align="right">
										' . intval($cart_product['CartsProduct']['price_with_dph'])  . '&nbsp;Kč
									</td>
									<td align="right">
										' . intval( $cart_product['CartsProduct']['price_with_dph'] * $cart_product['CartsProduct']['quantity'] ) . '&nbsp;Kč
									</td>
								</tr>
							';
						}
					
						echo '<tr>
							<th colspan="2" align="right">cena za zboží celkem:</td>
							<td colspan="2" align="right"><strong>' . intval($final_price) . ' Kč</strong><br /><span style="font-size:10px">(' . round($final_price_wout, 2) . ' Kč<br />bez DPH)</span></td>
						</tr>';
						
						// pokud cena objednavky neprekroci dopravu zdarma
						$shipping_price = 0;
						if ( $final_price < $shipping['Shipping']['free'] AND !$free_shipping ){
							$shipping_price = $shipping['Shipping']['price'];
						}
					?>
					<tr>
						<td>
							způsob doručení: <strong><?=$shipping['Shipping']['name']?></strong><br />
							<?=$html->link('upravit', array('controller' => 'orders', 'action' => 'shipping_edit'), array('class' => 'smallLinkEdit')) ?>
						</td>
						<td>&nbsp;</td>
						<td align="right"><?=$shipping_price?>&nbsp;Kč</td>
						<td align="right"><?=$shipping_price?>&nbsp;Kč</td>
					</tr>
					<?echo '<tr>
							<th colspan="2" class="totalPrice">celková cena objednávky:</td>
							<td colspan="2" class="totalPrice">' . intval($final_price + $shipping_price) . ' Kč</td>
						</tr>';?>
						<tr>
							<td colspan="4" align="right">
								<a id="finalLink" href="/orders/finalize">dokončit objednávku &raquo;</a>
							</td>
						</tr>
				</table>
			</td>
		</tr>
	</table>
</div>