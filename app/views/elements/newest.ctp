<div class="header nejnovejsi">
	Nejnovější produkty
</div>
<div class="product_listing_narrow">
	<?php if (empty($newest)) { ?>
	<p><em>Nejsou dostupné.</em></p>
	<?php } else {
		foreach ($newest as $product) { 
			if (!empty($product['Image'])) {
	?>
	<img src="/product-images/medium/<?php echo $product['Image'][0]['name']?>" width="80px" height="80px" alt="obrazek produktu <?php echo $product['Product']['name']?>" />
	<?php 
			}
	?>
	<p class="product_name"><a href="/<?php echo $product['Product']['url']?>"><?php echo $product['Product']['name']?></a></p>
	<p class="product_link nejnovejsi"><a href="/<?php echo $product['Product']['url']?>">Zobrazit</a></p>
	<div class="menu_spacer"></div>
	<?	}
	}?>
</div>