	<h2>Varianty produktu <?=$product['Product']['name'] ?></h2>
	<?
	if (empty($subproducts)) {
		echo '<p>Produkt nemá žádné varianty</p>';
	} else {
		echo $form->create('Attribute', array('url' => '/admin/products/attributes_list/' . $product['Product']['id']));
		echo '<table>';		
		foreach ($filter_options as $option) {
			if (!empty($option['Attribute'])) {
				echo '<tr>';
				echo '<th>' . $option['Option']['name'] . '</th>';
				echo '<td>' . $form->select('Option. ' . trim($option['Option']['id']) . '.id', $option['Attribute'], null, array('label' => false, 'empty' => true)) . '</td>';
				echo '</tr>';
			}
		}
		echo '</table>';
		echo $form->submit('Filtrovat');
		echo $form->end();
		
		echo $form->create('Product', array('url' => array('controller' => 'products', 'action' => 'add_subproducts', $product['Product']['id'])));
	?>
	
	<table>
		<tr>
			<th>Atributy</th>
			<th>Dostupnost</th>
			<th>Kusů</th>
			<th>Přírůstková cena s&nbsp;DPH</th>
			<th>Povoleno</th>
			
		</tr>
		<? foreach ($subproducts as $subproduct) { ?>
		<tr>
			<td>
				<? foreach ($subproduct['AttributesSubproduct'] as $attributes_subproduct) { ?>
					-&nbsp;<?=$attributes_subproduct['Attribute']['Option']['name'] ?>: <?=$attributes_subproduct['Attribute']['value'] ?><br/>
				<? } ?>
			</td>
			<td><?=$form->select('Product.' . $subproduct['Subproduct']['id'] . '.availability_id', $availabilities, null, array('label' => false, 'value' => $data['Product'][$subproduct['Subproduct']['id']]['availability_id'], 'empty' => false)) ?></td>
			<td><?=$form->input('Product.' . $subproduct['Subproduct']['id'] . '.pieces', array('label' => false, 'size' => 3, 'value' => $data['Product'][$subproduct['Subproduct']['id']]['pieces'])) ?></td>
			<td><?=$form->input('Product.' . $subproduct['Subproduct']['id'] . '.price_with_dph', array('label' => false, 'size' => 10, 'value' => $data['Product'][$subproduct['Subproduct']['id']]['price_with_dph'])) ?></td>
			<td>
				<?=$form->checkbox('Product.' . $subproduct['Subproduct']['id'] . '.active', array('label' => false, 'checked' => ($data['Product'][$subproduct['Subproduct']['id']]['active'] == 1))); ?>
				<?=$form->hidden('Product.' . $subproduct['Subproduct']['id'] . '.product_id', array('value' => $product['Product']['id'])); ?>
			</td>
		</tr>
		<? } ?>
	</table>
	<?
	echo $form->submit('Odeslat');
	echo $form->end();
	}
	?>