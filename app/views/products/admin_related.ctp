<h2><?=$product['Product']['name'] ?> - související produkty</h2>

<?
	echo $form->Create('Product', array('url' => array('controller' => 'products', 'action' => 'related', $id)));
	echo $form->text('Product.query', array('label' => false));
	echo $form->submit('vyhledat');
	echo $form->end();
	
	echo '<p style="font-size:12px">Napište jakoukoliv část názvu produktu. ("metr" vyhledá např. gluko<strong>metr</strong> i tono<strong>metr</strong> ).</p>';

	if ( !empty($query_products) ){
?>
		<table class="topHeading" cellpadding="5" cellspacing="3">
			<tr>
				<th style="width:105px">Obrázek</th>
				<th>Název produktu</th>
				<th>&nbsp;</th>
			</tr>
<?
	foreach ( $query_products as $query_product ){
		echo '<tr>';
		if (!empty($query_product['Image'])) {
			$im = getImageSize('product-images/small/' . $query_product['Image'][0]['name']);
			echo '
					<td>
						<img src="/product-images/small/' . $query_product['Image'][0]['name'] . '" width="' . $im[0] . 'px" height="' . $im[1] . 'px" alt="' . $query_product['Product']['name'] . '" />
					</td>
				';
		}
		
		echo '
				<td>
					<a href="http://www.e-kola.cz/' . $query_product['Product']['url'] . '">' . $query_product['Product']['name'] . '</a>
				</td>
				<td>
					přidat jako související:<br />
					' . $form->create('Product', array('url' => array('controller' => 'products', 'action' => 'related_add', $id))) . '
					' . $form->hidden('Product.related_product_id', array('value' => $query_product['Product']['id'])) . '
					' . $form->end('jednostranně') . '
					
					' . $form->create('Product', array('url' => array('controller' => 'products', 'action' => 'related_add', $id))) . '
					' . $form->hidden('Product.both', array('value' => 1)) . '
					' . $form->hidden('Product.related_product_id', array('value' => $query_product['Product']['id'])) . '
					' . $form->end('oboustranně') . '
				</td>
			</tr>
		';
	}
?>
		</table>
<?	
	}
	
	if ( empty($related_products) ){
		echo '<p>Produkt nemá žádné související produkty.</p>';
	} else {
?>
	<table class="topHeading" cellpadding="5" cellspacing="3">
		<tr>
			<th style="width:105px">Obrázek</th>
			<th>Název souvisejícího produktu</th>
			<th>&nbsp;</th>
		</tr>
<?
		foreach ( $related_products as $related_product ){
			echo '<tr>';
			if (!empty($related_product['Image'])) {
				$im = getImageSize('product-images/small/' . $related_product['Image'][0]['name']);
				echo '
					<td>
						<img src="/product-images/small/' . $related_product['Image'][0]['name'] . '" width="' . $im[0] . 'px" height="' . $im[1] . 'px" alt="' . $related_product['Product']['name'] . '" />
					</td>
					';
			}
			echo '
				<td>
					<a href="http://www.e-kola.cz/' . $related_product['Product']['url'] . '">' . $related_product['Product']['name'] . '</a>
				</td>
				<td>
					' . $form->create('Product', array('url' => array('controller' => 'products', 'action' => 'related_delete', $id))) . '
					' . $form->hidden('Product.related_product_id', array('value' => $related_product['Product']['id'])) . '
					' . $form->hidden('Product.product_id', array('value' => $id)) . '
					' . $form->end('zrušit závislost') . '
				</td>
			</tr>';
		}
	}
?>
	</table>