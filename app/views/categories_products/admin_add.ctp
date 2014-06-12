<h2>Přesunout produkt</h2>
<?
	if ( !empty($contained) ){
?>
	<p>Produkt je již zařazen do těchto kategorií:</p>
	<ul>
<?
		foreach ( $contained as $category ){
			echo '<li>' . $html->link($category['Category']['name'], array('controller' => 'categories', 'action' => 'list_products', $category['CategoriesProduct']['category_id'])) . '</li>';
		}
?>
	</ul>
<?
	}
?>

<p>Zvolte kategorii, do které chcete produkt <strong><?=$this->data['Product']['name']?></strong> kopírovat.</p>
<?
	echo $form->Create('CategoriesProduct');
	echo $form->hidden('CategoriesProduct.product_id');
	echo $form->select('CategoriesProduct.category_id', $categories, null, array('empty' => false));
	echo $form->Submit('zkopírovat');
	echo $form->end();
?>