<div id="suggestedProducts">
<?
	foreach ( $products as $product ){
?>
	<div class="wrapper">
		<div class="imageWrapper"><img src="/product-images/small/<?=$product['Image'][0]['name']?>" alt="" /></div>
		<h3><a href="/<?=$product['Product']['url']?>"><?=$product['Product']['name']?></a></h3>
		<p><?=$product['Product']['short_description']?>&nbsp;<a href="/<?=$product['Product']['url']?>">více info</a></p>
		<br />
		<span>cena: od <?=$product['Product']['price']?> Kč</span>
		<div class="clearer"></div>
	</div>
<?
	}
?>
</div>