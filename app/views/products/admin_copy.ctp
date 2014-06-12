<h2>Duplikace produktu</h2>
	<?=$form->create('Product', array('url' => array('action' => 'copy', $this->data['Product']['id'])));?>
	<fieldset>
		<legend>Kopírovat produkt</legend>
		<table class="leftHeading" cellpadding="5" cellspacing="3">
			<tr>
				<th>
					kopírovat do
				</th>
				<td>
					<?=$form->select('Product.categories_id', $categories, null, array('empty' => false))?>
				</td>
			</tr>
<!--			<tr>
				<th>
					obrázky
				</th>
				<td>
					<?=$form->checkbox('Product.images')?>
				</td>
			</tr>
			<tr>
				<th>
					varianty
				</th>
				<td>
					<?=$form->checkbox('Product.subproducts')?>
				</td>
			</tr>-->
		</table>
	</fieldset>
<?
	echo $form->submit('Duplikovat');
?>