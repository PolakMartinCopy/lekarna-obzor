<h1><?php echo $product['Product']['name']?> - operace v kategoriích</h1>

<h2>Produkt je zařazen v těchto kategoriích</h2>
<table class="topHeading">
	<tr>
		<th>Kategorie</th>
		<th>&nbsp;</th>
	</tr>
<?php foreach ($product['CategoriesProduct'] as $category) { ?>
	<tr>
		<td><?php echo $category['Category']['name']?></td>
		<td><?php
			if (count($product['CategoriesProduct']) > 1) {
				echo $html->link(
					'Odstranit z kategorie',
					array('controller' => 'categories_products', 'action' => 'delete', $category['id'], $opened_category_id),
					null,
					'Opravdu chcete produkt ' . $product['Product']['name'] . ' odstranit z kategorie ' . $category['Category']['name'] . '?'
				);
			} else { ?>
			<em>Produkt nelze odstranit z kategorie</em>	
			<?php }?>
		</td>
	</tr>
<?php } ?>
</table>

<h4>Přesunout produkt do jiné kategorie</h4>
<p>Zvolte kategorii, do které chcete produkt <strong><?=$product['Product']['name']?></strong> přesunout.</p>
<?
	echo $form->Create('CategoriesProduct', array('url' => array('controller' => 'categories_products', 'action' => 'edit', $theCategoriesProduct['CategoriesProduct']['id'])));
	echo $form->hidden('CategoriesProduct.id', array('value' => $theCategoriesProduct['CategoriesProduct']['id']));
	echo $form->hidden('CategoriesProduct.product_id', array('value' => $product['Product']['id']));
	echo $form->select('CategoriesProduct.category_id', $categories, $opened_category_id, array('empty' => false));
	echo $form->Submit('přesunout');
	echo $form->end();
?>

<h4>Kopírovat do další kategorie</h4>
<p>Zvolte kategorii, do které chcete produkt <strong><?=$product['Product']['name']?></strong> kopírovat.</p>
<?
	echo $form->Create('CategoriesProduct', array('url' => array('controller' => 'categories_products', 'action' => 'add', $product['Product']['id'])));
	echo $form->hidden('CategoriesProduct.product_id', array('value' => $product['Product']['id']));
	echo $form->select('CategoriesProduct.category_id', $categories, $opened_category_id, array('empty' => false));
	echo $form->Submit('zkopírovat');
	echo $form->end();
?>

<h4>Duplikovat do kategorie</h4>
<p>Zvolte kategorii, do které chcete produkt <strong><?=$product['Product']['name']?></strong> duplikovat.</p>
<?
	echo $form->create('Product', array('url' => array('action' => 'copy', $product['Product']['id'])));
	echo $form->select('Product.category_id', $categories, $opened_category_id, array('empty' => false));
	echo $form->hidden('Product.this_category_id', array('value' => $opened_category_id));
	echo $form->submit('Duplikovat');
	echo $form->end();
?>