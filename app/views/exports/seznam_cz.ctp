<?
	foreach ( $products as $product ){
?>
	<SHOPITEM>
<?php 
$zbozi_name = $product['Product']['zbozi_name'];
if (empty($zbozi_name)) {
	$zbozi_name = $product['Product']['name'];
}
?>
		<PRODUCT><?=iconv('utf-8', 'windows-1250', $zbozi_name)?></PRODUCT>
		<DESCRIPTION><?=iconv('utf-8', 'windows-1250', $product['Product']['short_description'])?></DESCRIPTION>
		<URL>http://www.<?php echo CUST_ROOT ?>/<?=$product['Product']['url']?></URL>
<?php // vychozi dostupnost produktu je ihned
	$availability = 0;
	// dostupnost do tydne
	if ($product['Availability']['id'] == 4) {
		$availability = 5;
	}
?>
		<DELIVERY_DATE><?php echo $availability?></DELIVERY_DATE>
<?php if (!empty($product['Image']) && file_exists('product-images/' . $product['Image'][0]['name'])) { ?>
		<IMGURL>http://www.<?php echo CUST_ROOT ?>/product-images/<?=(empty($product['Image'][0]['name']) ? '' : str_replace(" ", "%20", $product['Image'][0]['name']))?></IMGURL>
<?php } ?>
		<PRICE_VAT><?=$product['Product']['retail_price_with_dph']?></PRICE_VAT>
<?php if (!empty($product['Product']['ean']) && strlen($product['Product']['ean']) == 13) { ?>
		<EAN><?php echo $product['Product']['ean']?></EAN>
<?php } ?>
<?php if (!empty($product['Product']['zbozi_cpc'])) { ?>
		<MAX_CPC><?php echo $product['Product']['zbozi_cpc']?></MAX_CPC>
<?php } ?>
	</SHOPITEM>
<?
	} ?>