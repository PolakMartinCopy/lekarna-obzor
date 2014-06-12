<h1>Detail objednávky č. <?=$order['Order']['id'] ?></h1>
<h2>Objednané produkty</h2>
<table class="top_headed" cellpadding="5" cellspacing="3">
	<tr>
		<th>název produktu</th>
		<th>cena bez DPH / ks<br/>( s DPH / ks )</th>
		<th>ks.</th>
		<th>celk. cena bez DPH</br>( s DPH )</th>
	</tr>
<?
	foreach ( $order['OrderedProduct'] as $op ){
		// pripravim si atributy produktu pokud nejake ma
		$attributes = array();
		if ( !empty($op['OrderedProductsAttribute']) ){
			foreach ( $op['OrderedProductsAttribute'] as $opa ){
				$attributes[] = $opa['Attribute']['Option']['name'] . ': ' . $opa['Attribute']['value'];
			}
		}
?>
	<tr>
		<td>
			<?
				echo $op['Product']['name'];
				if ( !empty($attributes) ){
					echo '<br />';
					echo '<p style="font-size:9px;">' . implode('<br />', $attributes) . '</p>';
				}
			?>
			
		</td>
		<td valign="top">
			<?=$op['product_price'] . '&nbsp;Kč' ?> (<?=$op['product_price_tax'] . '&nbsp;Kč' ?>)
		</td>
		<td>
			<?=$op['product_quantity'] ?>
		</td>
		<td>
			<?=$op['product_price'] * $op['product_quantity'] . '&nbsp;Kč' ?> (<?=$op['product_price_tax'] * $op['product_quantity'] . '&nbsp;Kč'  ?>)
		</td>
	</tr>
<?
	}
?>
	<tr>
		<td colspan="3">
			doprava(<?=$order['Shipping']['name'] ?>)
		</td>
		<td>
			<?=$order['Order']['shipping_cost'] . '&nbsp;Kč' ?>
		</td>
	</tr>
	<tr>
		<td colspan="3">
			způsob platby(<?=$order['Payment']['name'] ?>)
		</td>
		<td>
			&nbsp;
		</td>
	</tr>
	<tr>
		<td>
			&nbsp;
		</td>
		<td colspan="2">
			celková cena bez DPH<br/>(celková cena s DPH)
		</td>
		<td>
			<?=$order['Order']['subtotal'] + $order['Order']['shipping_cost'] . '&nbsp;Kč'?> (<?=$order['Order']['subtotal_tax'] + $order['Order']['shipping_cost'] . '&nbsp;Kč' ?>)
		</td>
	</tr>
</table>