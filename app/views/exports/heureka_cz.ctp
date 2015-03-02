<? foreach ( $products as $product ){ ?>
	<SHOPITEM>
		<ITEM_ID><?php echo $product['Product']['id']?></ITEM_ID>
		<PRODUCT><?=$product['Product']['heureka_name']?></PRODUCT>
		<DESCRIPTION><?=$product['Product']['short_description']?></DESCRIPTION>
		<URL>http://www.<?php echo CUST_ROOT?>/<?=$product['Product']['url']?></URL>
<?php if (!empty($product['Image'][0]['name'])) { ?>
		<IMGURL>http://www.<?php echo CUST_ROOT?>/product-images/<?=str_replace(" ", "%20", $product['Image'][0]['name'])?></IMGURL>
<?php  }?>
		<PRICE><?php echo ceil($product['Product']['retail_price_with_dph'] * 100 / ($product['TaxClass']['value'] + 100)) ?></PRICE>
		<PRICE_VAT><?=ceil($product['Product']['retail_price_with_dph'])?></PRICE_VAT>
		<VAT><?php echo str_replace('.', ',', $product['TaxClass']['value'] / 100) ?></VAT>
		<MANUFACTURER><?php echo $product['Manufacturer']['name']?></MANUFACTURER>
		<ITEM_TYPE>new</ITEM_TYPE>
		<CATEGORYTEXT><?php echo $product['CATEGORYTEXT'] ?></CATEGORYTEXT>
		<DELIVERY_DATE>0</DELIVERY_DATE>
<?php foreach ($shippings as $shipping) { ?>
		<DELIVERY>
			<DELIVERY_ID><?php echo $shipping['Shipping']['heureka_id']?></DELIVERY_ID>
			<?php // pokud je cena produktu vyssi, nez cena objednavky, od ktere je tato doprava zdarma, cena je 0, jinak zadam cenu dopravy
			$shipping_price = 0;
			if ($shipping['Shipping']['free'] && $product['Product']['retail_price_with_dph'] < $shipping['Shipping']['free']) {
				$shipping_price = ceil($shipping['Shipping']['price']);
			}
			?>
			<DELIVERY_PRICE><?php echo $shipping_price?></DELIVERY_PRICE>	
		</DELIVERY>
<?php } ?>
<?php if (!empty($product['Product']['ean']) && strlen($product['Product']['ean']) == 13) { ?>
		<EAN><?php echo $product['Product']['ean']?></EAN>
<?php } ?>
<?php if (!empty($product['Product']['heureka_cpc'])) { ?>
		<HEUREKA_CPC><?php echo $product['Product']['heureka_cpc']?></HEUREKA_CPC>
<?php } ?>
	</SHOPITEM>
<?
	}
?>