<h1>Vytvoření nové objednávky</h1>

<?=$form->create('Order', array('url' => array('users' => true, 'controller' => 'orders', 'action' => 'recap' ))) ?>
<table class="top_headed" style="width:100%">
	<tr>
		<th>Produkt</th>
		<th>Vlastnosti</th>
		<th>Cena / ks<br /><span style="font-size:10px">(bez DPH)</span></th>
		<th>mn.</th>
	</tr>
<?
$i = 0;
$bg_color = '';
foreach ($products as $product) {
	if ( isset($product['values']) ) {
		//produkt ma nejake atributy
		foreach ($product['values'] as $value_group) {
			$bg_color = empty($bg_color) ? ' style="background-color:#D5D5D5"' : '';
			// prochazim vsechny mozne varianty
			echo '<tr' . $bg_color . '>';
			echo '<td valign="top">' . $html->link($product['Product']['name'], array('users' => true, 'controller' => 'products', 'action' => 'view', $product['Product']['id'])) . "</td>\n";
			
			$output_variants = array();
			$form_fields = array();
			$j = 0;
			foreach ( $value_group as $value ){
				$output_variants[] = $value['Value']['Option']['name'] . ": " . $value['Value']['name'];
				$form_fields[] = '<input type="hidden" name="data[Order][' . $i . '][OrderedProduct][OrderedProductAttribute][' . $j . '][option_id]" value="' . $value['Value']['Option']['id'] . '" />' . 
				"\n" . '<input type="hidden" name="data[Order][' . $i . '][OrderedProduct][OrderedProductAttribute][' . $j . '][option_name]" value="' . $value['Value']['Option']['name'] . '" />' .
				"\n" . '<input type="hidden" name="data[Order][' . $i . '][OrderedProduct][OrderedProductAttribute][' . $j . '][value_id]" value="' . $value['Value']['id'] . '" />' .
				"\n" . '<input type="hidden" name="data[Order][' . $i . '][OrderedProduct][OrderedProductAttribute][' . $j . '][value_name]" value="' . $value['Value']['name'] . '" />';
				$j++; 
			}

			// vypisu si textove varianty produktu
			echo '<td valign="top">' . implode('<br />', $output_variants) . "</td>";
?>
			<td valign="top">
				<?
					$base_price = $product['Product']['price'];
					if ( $value['Value']['price'] != 0 ){
						$base_price = $base_price + $value['Value']['price'];
					}
					echo $base_price . '&nbsp;Kč';
					
				?>
			</td>
<?
			echo '<td valign="top">';
			echo $form->input('Order.' . $i . '.OrderedProduct.quantity', array('label' => false, 'size' => '2'));
			echo $form->input('Order.' . $i . '.OrderedProduct.product_id', array('type' => 'hidden', 'label' => false, 'value' => $product['Product']['id']));
			echo $form->input('Order.' . $i . '.OrderedProduct.product_name', array('type' => 'hidden', 'label' => false, 'value' => $product['Product']['name']));
			echo $form->input('Order.' . $i . '.OrderedProduct.price', array('type' => 'hidden', 'label' => false, 'value' => $base_price));
			echo "\n" . implode("\n", $form_fields);
			echo "</td>";
			echo "</tr>";
			$i++;
		}
	}  else {
		$bg_color = empty($bg_color) ? ' style="background-color:#D5D5D5"' : '';
		//produkt nema zadne atributy -->
		echo '<tr' . $bg_color . '>';
		echo '<td>' . $html->link($product['Product']['name'], array('users' => true, 'controller' => 'products', 'action' => 'view', $product['Product']['id'])) . "</td>";
		echo '<td>&nbsp;</td>';
		echo '<td>' . $product['Product']['price'] . "&nbsp;Kč</td>";
		echo '<td>';
		echo $form->input('Order.' . $i . '.OrderedProduct.quantity', array('label' => false, 'size' => '2'));
		echo $form->hidden('Order.' . $i . '.OrderedProduct.product_id', array('value' => $product['Product']['id']));
		echo $form->hidden('Order.' . $i . '.OrderedProduct.product_name', array('value' => $product['Product']['name']));
		echo $form->hidden('Order.' . $i . '.OrderedProduct.price', array('value' => $product['Product']['price']));
		echo "</td>";
		echo "</tr>";
		$i++;
	}
}
?>
</table>
<?
echo $form->submit('Odeslat');
echo $form->end();
?>
