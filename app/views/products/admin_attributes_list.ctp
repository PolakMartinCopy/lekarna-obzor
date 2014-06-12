<div>
<div class="actions">
	<ul>
		<li><?php echo $html->link('Zpět na seznam produktů', array('controller' => 'categories', 'action' => 'list_products', $product['CategoriesProduct'][0]['category_id'])); ?> </li>
	</ul>
</div>
	<h2>Atributy produktu <?=$product['Product']['name']?> </h2>
	<?
	echo $form->create('Product', array('url' => '/admin/products/attributes_list/' . $product['Product']['id']));
	foreach ($options as $option) {
	?>
	<h3><?=$option['Option']['name'] ?></h3>
	<?
		echo $form->textarea('Attributes.' . $option['Option']['id'], array('cols' => 50, 'rows' => 5));
	}
	echo $form->hidden('Product.id', array('value' => $product['Product']['id']));
	echo $form->submit('Odeslat');
	echo $form->end();
	
	echo $this->element('admin_subproducts_control', $this->requestAction('admin/subproducts/control/' . $product['Product']['id']));
	?>

<div class="actions">
	<ul>
		<li><?php echo $html->link('Zpět na seznam produktů', array('controller' => 'categories', 'action' => 'list_products', $product['CategoriesProduct'][0]['category_id'])); ?> </li>
	</ul>
</div>
</div>