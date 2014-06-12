<h2>Přesunout produkt</h2>

<?=$form->create('CategoriesProduct', array('url' => '/admin/categories_products/edit/' . $category_product['CategoriesProduct']['id'])) ?>
<?=$form->hidden('CategoriesProduct.id', array('value' => $category_product['CategoriesProduct']['id'])) ?>
<table>
	<tr>
		<th>přesunout do</th>
		<td><?=$form->select('CategoriesProduct.category_id', $categories, $category_product['CategoriesProduct']['category_id'], array('label' => false), false) ?></td>
	</tr>
</table>
<?=$form->submit('Odeslat') ?>
<?=$form->end() ?>