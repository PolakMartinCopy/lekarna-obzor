<h2>Obrázky k produktu <?=$product['Product']['name']?></h2>
<a href="#imagesform">vložit nový obrázek</a> | <a href="/admin/dirimages/list/<?=$product['Product']['id']?>">vložit z FTP</a>
<?
	if ( count($images) > 0 ){
?>
	<table class="topHeading" cellpadding="5" cellspacing="3">
		<tr>
			<th>ID</th>
			<th>Náhled</th>
			<th>Název</th>
			<th>&nbsp;</th>
		</tr>
<?
	foreach( $images as $image ){
		$imageSize = @getimagesize('product-images/small/' . $image['Image']['name']);
?>
		<tr>
			<td><?=$image['Image']['id']?></td>
			<td valign="middle" style="height:100px;width:100px;"><img style="border:1px solid black;" src="/product-images/small/<?=$image['Image']['name']?>" width="<?=$imageSize[0]?>px" height="<?=$imageSize[1]?>" alt="" /></td>
			<td><?=$image['Image']['name']?></td>
			<td>
				<a href="/admin/images/delete/<?=$image['Image']['id']?>">odstranit</a> |
				<?
					if ( $image['Image']['is_main'] ){
						echo '<strong>hlavní obrázek</strong>';
					} else {
						echo '<a href="/admin/images/set_as_main/' . $image['Image']['id'] . '">nastavit jako hlavní obrázek</a>';
					}
				?>
			</td>
		</tr>
<?
	}
?>
	</table>
<?
	} else {
		echo '<p>Produkt zatím nemá žádné obrázky</p>';
	}
?>
	<a name="imagesform"></a>
	<h3>Vložit nový obrázek</h3>
<?
	echo $form->create('Product', array('url' => '/admin/products/images_list/' . $product['Product']['id'] . '/') );
	echo $form->submit('Zobrazit');
	echo $form->text('Product.image_fields', array('size' => '1')) . ' polí';
	echo $form->end();

	echo $form->Create('Image', array('url' => '/admin/images/add', 'type' => 'file')); ?>
	<fieldset>
		<legend>Nový obrázek</legend>
		<table class="leftHeading" cellpadding="5" cellspacing="3">
			<tr>
				<th></th>
				<td>
					<?
						if ( !isset($this->data['Product']['image_fields']) OR $this->data['Product']['image_fields'] > 10 OR $this->data['Product']['image_fields'] < 1 ) {
							$this->data['Product']['image_fields'] = 1;
						}
						for ( $i = 0; $i < $this->data['Product']['image_fields']; $i++ ){
							echo '<input type="file" name="data[Image][image' . $i . ']" /><br />';
						}
					?>
				</td>
			</tr>
		</table>
<?
	echo $form->hidden('Image.image_fields', array('value' => $this->data['Product']['image_fields']));
	echo $form->hidden('Image.product_id', array('value' => $product['Product']['id']));
?>
	</fieldset>
<?
	echo $form->submit('Vložit obrázek');
	echo $form->end();
?>
<div class="actions">
	<ul>
		<li><?php echo $html->link('Zpět na seznam produktů', array('controller' => 'categories', 'action' => 'list_products', $category[0]['CategoriesProduct']['category_id'])); ?> </li>
	</ul>
</div>