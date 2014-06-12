<div class="mainContentWrapper">
	<p>Na této stránce můžete zkontrolovat obsah Vašeho nákupního košíku.<br />
	Chcete-li dokončit objednávku a <a href="/orders/add">zaplatit</a>, klikněte <a href="/orders/add">zde</a>.</p>

	<h2>Seznam produktů v nákupním košíku</h2>
	<?
		if ( empty($cart_products) ){
	?>
		<p>V košíku nemáte žádné zboží.</p>
	<?
		} else {
	?>
		<table id="cartContents" cellpadding="0" cellspacing="0">
			<tr>
				<th style="width:50%">Název produktu</th>
				<th style="width:30%">Množství</th>
				<th>Cena za kus</th>
				<th>Cena celkem</th>
				<th>&nbsp;</th>
			</tr>
		<?
			$final_price = 0;
			foreach ( $cart_products as $cart_product ){
				$final_price = $final_price + $cart_product['CartsProduct']['price_with_dph'] * $cart_product['CartsProduct']['quantity'];
				echo '
					<tr>
						<td>
							<a href="/' . $cart_product['Product']['url'] . '">' . $cart_product['Product']['name'] . '</a>';
		
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
							' . $form->Create('CartsProduct', array('url' => array('action' => 'edit', $cart_product['CartsProduct']['id']))) . '
							' . $form->hidden('CartsProduct.id', array('value' => $cart_product['CartsProduct']['id'])) . '
							' . $form->text('CartsProduct.quantity', array('size' => 1, 'value' => $cart_product['CartsProduct']['quantity'])) . '&nbsp;ks
							' . $form->Submit('Upravit', array('class' => 'changeAmount')) . '
							' . $form->end() . '
						</td>
						<td>
							' . intval($cart_product['CartsProduct']['price_with_dph'])  . '&nbsp;Kč
						</td>
						<td>
							' . intval( $cart_product['CartsProduct']['price_with_dph'] * $cart_product['CartsProduct']['quantity'] ) . '&nbsp;Kč
						</td>
						<td>
							<a title="odstranit z košíku" href="/carts_products/delete/' . $cart_product['CartsProduct']['id'] . '">smazat</a>
						</td>
					</tr>
				';
			}
		
			echo '<tr>
				<th colspan="2" align="right">cena za zboží celkem:</td>
				<td colspan="3" align="center"><strong>' . intval($final_price) . ' Kč</strong></td>
			</tr>';
		?>
			<tr>
				<td colspan="5" align="right">
					<a id="orderAndPay" href="/orders/add">přejít k pokladně</a>
				</td>
			</tr>
		</table>
	<?
		}
	?>
</div>