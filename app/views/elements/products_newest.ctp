<?
	echo $html->link($newest_product['Product']['name'], '/' . $newest_product['Product']['url']);
	echo '<br />';
	$done = false;
	
	foreach ( $newest_product['Image'] as $image ){
		if ( $image['is_main'] == 1 AND !$done ){
			if (file_exists('product-images/small/' . $image['name'])) {
				$imagedim = getimagesize('product-images/small/' . $image['name']);
				echo '<img src="/product-images/small/' . $image['name'] . '" width="' . $imagedim[0] . 'px" height="' . $imagedim[1] . '" alt="" /><br />';
				$done = true;
			}
		}
	}

	if ( $newest_product['Product']['discount_price'] < $newest_product['Product']['retail_price_with_dph'] ){
?>
		<span class="old_price"><?=intval($newest_product['Product']['retail_price_with_dph'])?> Kč</span><br />
		<strong><?=intval($newest_product['Product']['discount_price'])?> Kč</strong>
<?
	} else {
?>
		<strong><?=intval($newest_product['Product']['retail_price_with_dph'])?> Kč</strong>
<?
	}
?>