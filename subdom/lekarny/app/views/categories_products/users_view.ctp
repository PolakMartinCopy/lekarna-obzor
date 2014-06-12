<h1><?=$category['Category']['name'] ?></h1>

	<table class="product_listing">
<?
		foreach ( $products as $product ){
?>

				<tr>
					<td class="image">
<?php
						if ( !empty($product['Product']['Image']) ){
?>
							<a href="/users/products/view/<?=$product['Product']['id']?>">
								<img src="/product-images/small/<?php echo $product['Product']['Image'][0]['name']?>" />
							</a>
<?php
						} else {
							echo '&nbsp;';
						}
?>
					</td>
					<th valign="top">
						<a href="/users/products/view/<?=$product['Product']['id']?>"><?=$product['Product']['name']?></a>
						<br />
						<a class="cart_button" href="/users/products/view/<?=$product['Product']['id']?>">objednat</a>
					</th>
					<td><p><?=$product['Product']['description']?></p></td>
				</tr>
				<tr>
					<td colspan="2">&nbsp;</td>
				</tr>
<?
		}
?>
	</table>