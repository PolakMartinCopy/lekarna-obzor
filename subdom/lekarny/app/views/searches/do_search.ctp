<h1>Výsledek vyhledávání</h1>

<?php
	if ( !empty($this->data) ){
		echo $form->Create('Search', array('url' => array('controller' => 'searches', 'action' => 'do_search')));
		echo $form->text('q', array('onClick' => 'return this.select();'));
		echo $form->submit('hledej');
		echo $form->end();
	
		if ( empty($products) ){
			echo '<p>Nebyl nalezen žádný produkt odpovídající Vašemu dotazu.</p>';
		} else {
?>
			<table class="product_listing">
<?php

			foreach ( $products as $product ){
?>
				<tr>
					<td class="image">
<?php
						if ( !empty($product['Image']) ){
?>
							<a href="/users/products/view/<?=$product['Product']['id']?>">
								<img src="/product-images/small/<?php echo $product['Image'][0]['name']?>" />
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
<?php
			}
?>
			</table>
<?php
		}
	}
?>