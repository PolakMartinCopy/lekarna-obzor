<div>
	<h2>Vyhledávání</h2>
	<?
		echo $form->create('Search', array('action' => 'do'));
		echo $form->text('query');
		echo $form->end('Hledej');
	
		if ( !empty($products) ){
	?>
			<table class="topHeading" cellpadding="5" cellspacing="3">
				<tr>
					<th>Id</th>
					<th>Název</th>
					<th>Cena</th>
					<th>&nbsp;</th>
				</tr>
	<?
		foreach ( $products as $product ){
			// oznacim si vyhledavane vyrazy v query
			$split_query = explode(" ", $this->data['Search']['query']);
			for ( $i = 0; $i < count($split_query); $i++ ){
				$product['Product']['name'] = preg_replace('/' . $split_query[$i] . '/', '<strong style="color:red;">' . $split_query[$i] . '</strong>', $product['Product']['name']);
			}
	?>
				<tr>
					<td><a href="/products/view/<?=$product['Product']['id']?>"><?=$product['Product']['id']?></a></td>
					<td style="font-size:10px;"><?=$product['Product']['name']?></td>
					<td style="font-size:9px;">
						<? 
							foreach ( $product['CategoriesProduct'] as $category){
								echo '<a href="/admin/categories/list_products/' . $category['category_id'] . '">';
								foreach ( $category['path'] as $item ){
									echo $item['Category']['name'] . '&nbsp;&raquo;&nbsp;';
								}
								echo '</a> - <a href="/admin/categories_products/edit/' . $category['id'] . '">přesunout</a><br/>';
							}
						?>
					</td>
					<td style="font-size:10px;">
						<a href="/admin/products/edit/<?=$product['Product']['id']?>">Editovat</a> |
						<a href="/admin/products/attributes_list/<?=$product['Product']['id']?>">Varianty</a> |
						<a href="/admin/products/images_list/<?=$product['Product']['id']?>">Obrázky</a>
						<br />
						<?=$html->link('Kopírovat', array('controller' => 'categories_products', 'action' => 'add', $product['Product']['id'])) ?> |
						<?=$html->link('Smazat', array('controller' => 'products', 'action' => 'delete', $product['Product']['id']), array(), 'Opravdu chcete tento produkt smazat?')?>
					</td>
				</tr>
	<?
		}
	?>
			</table>
	<?
		}
	?>
	
</div>