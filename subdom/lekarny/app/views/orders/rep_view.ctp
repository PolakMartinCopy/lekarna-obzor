<h1>Objednávka č. <?=$order['Order']['id']?> (<?=strftime("%d.%m.%Y %H:%M", strtotime($order['Order']['created']))?>)</h1>
<?
	$color = '';
	if ( !empty($order['Status']['color']) ){
		$color = ' style="color:#' . $order['Status']['color'] . '"';
	}
?>
	<h2>Stav objednávky - <?='<span' . $color . '>' . $order['Status']['name'] . '</span>'?></h2>

<?
	if ( !empty( $order['Order']['comments'] ) ){
?>
	<h2>Komentář od zákazníka</h2>
		<p style="font-size:11px;color:blue;"><?=$order['Order']['comments']?></p>
<?
	}
?>

	<h3>Objednané produkty</h3>
	<table class="top_headed" cellpadding="5" cellspacing="3">
		<tr>
			<th>
				Název produktu
			</th>
			<th>
				Množství
			</th>
			<th>
				Cena<br />
				za kus
			</th>
			<th>
				Cena<br />
				celkem
			</th>
		</tr>
	<?
		foreach ( $order['OrderedProduct'] as $product ){
			// celkova cena za pocet kusu krat jednotkova cena
			$total_products_price = $product['product_quantity'] * $product['product_price'];
			echo '	<tr>
						<td>
							' . $product['Product']['name'];

			// musim vyhodit atributy, pokud nejake produkt ma
			if ( !empty( $product['OrderedProductAttribute'] ) ){
				echo '<div class="orderedProductAttributes">';
				foreach( $product['OrderedProductAttribute'] as $attribute ){
					echo '<span>- <strong>' . $attribute['option_name'] . '</strong>: ' . $attribute['value_name'] . '</span><br />';
				}
				echo '</div>';
			}


			echo		'</td>
						<td>
							' . $product['product_quantity'] . ' ks
						</td>
						<td>
							' . $product['product_price'] . ' Kč
						</td>
						<td>
							' . $total_products_price . ' Kč
						</td>
					</tr>';
			
		}
	?>
		<tr>
			<th colspan="2" align="right">
				cena za zboží celkem:
			</th>
			<td colspan="2" align="right">
				<?=$order['Order']['subtotal']?> Kč
			</td>
		</tr>
		<tr>
			<td colspan="2" align="right">
				způsob doručení: <?=$order['Shipping']['name']?>
			</td>
			<td colspan="2" align="right">
				<?=$order['Order']['shipping_cost']?> Kč
			</td>
		</tr>
		<tr>
			<th colspan="2" align="right">
				celková cena objednávky:
			</th>
			<td colspan="2" align="right">
				<?=( $order['Order']['subtotal'] + $order['Order']['shipping_cost'])?> Kč
			</td>
		</tr>
	</table>


	<table>
		<tr>
			<td style="width:33%" valign="top">
				<h3>Kontaktní údaje</h3>
				<?
					echo 'název společnosti:&nbsp;' . $order['Order']['company_name'] . '<br />';
					echo 'ičo:&nbsp;' . $order['Order']['company_ico'] . '<br />';
					echo 'dič:&nbsp;' . $order['Order']['company_dic'] . '<br />';
					echo '---<br />';
					echo 'kontaktní osoba:&nbsp;' . $order['Order']['person_first_name'] . ' ' . $order['Order']['person_last_name'] . '<br />';
					echo 'telefon:&nbsp;' . $order['Order']['person_phone'] . '<br />';
					echo 'email:&nbsp;' . $order['Order']['person_email'];
				?>
			</td>
			<td style="width:33%" valign="top">
				<h3>Fakturační adresa</h3>
				<?
					echo $order['Order']['payment_name'] . '<br />'
					. $order['Order']['payment_street'] . '<br />'
					. $order['Order']['payment_postal_code'] . ' ' . $order['Order']['payment_city'] . '<br />
					platba: <strong>' . $order['Payment']['name'] . '</strong>';
				?>			
			</td>
			<td style="width:33%" valign="top">
				<h3>Doručovací adresa</h3>
				<?
					echo $order['Order']['delivery_name'] . '<br />'
					. $order['Order']['delivery_street'] . '<br />'
					. $order['Order']['delivery_postal_code'] . ' ' . $order['Order']['delivery_city'] . '<br />
					doručení: <strong>' . $order['Shipping']['name'] . '</strong>';
					echo '<br />číslo balíku: ' . $html->link($order['Order']['shipping_number'], $order['Shipping']['tracker_prefix'] . trim($order['Order']['shipping_number']) . $order['Shipping']['tracker_postfix']);
					echo '<br />variabilní symbol: ' . $order['Order']['variable_symbol'];
				?>
			</td>
		</tr>
	</table>


<?
	if ( !empty ( $order['Ordernote'] ) ){
?>
		<h3>Poznámky</h3>
		<table class="top_headed" style="width:80%">
			<tr>
				<th>datum</th>
				<th>status</th>
				<th>kdo</th>
				<th>poznámka</th>
			</tr>
<?
		foreach ( $order['Ordernote'] as $note ){
			echo '
				<tr>
					<td>' . $note['created'] . '</td>
					<td>' . $note['Status']['name'] . '</td>
					<td>' . $note['Administrator']['first_name'] . ' ' . $note['Administrator']['last_name'] . '</td>
					<td>' . $note['note'] . '</td>
				</tr>
			';
		}
?>
		</table>
<?
	}
?>