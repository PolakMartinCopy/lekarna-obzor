<?php
	if ( !empty($similar_products) ){
?>
		<h2 id="details"><?php echo $product['Product']['name']?> - podobné produkty</h2>
<?php
		foreach ( $similar_products as $product ){
			$related_name = null;
			if (isset($product['Product']['related_name'])) {
				$related_name = $product['Product']['related_name'];
			}
			if (!$related_name) {
				$related_name = $product['Product']['name'];
			}
?>
	<div class="similarProductBox">

		<h3><a href="/<?=$product['Product']['url']?>"><?=$related_name?></a></h3>
		<table class="related_image_holder" cellpadding="0" cellspacing="0">
			<tr>
				<td align="center" valign="middle">
<?php			if ( !empty($product['Image']) ){ ?>
					<a href="/<?php echo $product['Product']['url']?>">
						<img src="/product-images/small/<?php echo $product['Image'][0]['name']?>" alt="<?php echo $product['Product']['name']?>"/>
					</a>
<?php			} ?>
				</td>
			</tr>
		</table>
		<div class="related_product_info">
			<span class="nova_cena_rp">cena: <?=intval($product['Product']['retail_price_with_dph'])?> Kč</span>
			<p><?=$product['Product']['short_description']?></p>
		</div>
	</div>
<?php	} ?>
	<div class="clearer"></div>
<?php } ?>