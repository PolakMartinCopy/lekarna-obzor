<? if ($has_subproducts) { ?>
<p><span class="header">Před vložením do košíku zvolte:</span></p>
<ul id="subproductsList">
	<?php 
		$odd = false;
		$first = true;
		foreach ($subproducts as $subproduct) {
			$information = array();
			foreach ($subproduct['AttributesSubproduct'] as $attributes_subproduct) {
				$information []= $attributes_subproduct['Attribute']['Option']['name'] . ': ' . $attributes_subproduct['Attribute']['value'];
			}
			if ($subproduct['Subproduct']['price_with_dph'] != 0) {
				$information []= 'cena: ' . ($product['Product']['retail_price_with_dph'] + $subproduct['Subproduct']['price_with_dph']) . ' Kč';
			}
			if ($first) {
				$checked = 'checked';
				$first = false;
			} else {
				$checked = '';
			}
			$style = '';
			if ($odd) {
				$style = ' class="odd"';
			}
			$odd = !$odd;
		?>
	<li<?php echo $style?>><input type="radio" name="data[Subproduct][id]" value="<?=$subproduct['Subproduct']['id'] ?>" <?=$checked ?>/><?=implode(', ', $information);?></li>
	<?php } ?>
</ul>
<?php } ?>