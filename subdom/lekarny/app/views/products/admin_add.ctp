<h1>Vytvoření nového produktu</h1>
<?
	$target = array();
	if ( isset($category_id) ){
		$target = array('url' => array('category_id' => $category_id));
	} 
	
	echo $form->create('Product', $target)
?>
<table class="left_headed" cellpadding="5" cellspacing="3">
<?
			if ( isset($categories) ){
?>
	<tr>
		<th>
			Kategorie
		</th>
		<td>
			<?=$form->input('Category.id', array('label' => false, 'type' => 'select', 'options' => $categories))?>
		</td>
	</tr>
<?
			}
?>
	<tr>
		<th>
			Název produktu
		</th>
		<td>
			<?=$form->input('name', array('label' => false))?>
		</td>
	</tr>
	<tr>
		<th>
			Kód VZP
		</th>
		<td>
			<?=$form->input('vzp_code', array('label' => false))?>
		</td>
	</tr>
	<tr>
		<th>
			Výrobce
		</th>
		<td>
			<?=$form->input('manufacturer_id', array('label' => false))?>
		</td>
	</tr>
	<tr>
		<th>
			Dostupnost
		</th>
		<td>
			<?=$form->input('availability_id', array('label' => false))?>
		</td>
	</tr>
	<tr>
		<th valign="top">
			Popis produktu
		</th>
		<td>
			<?=$form->input('description', array('label' => false, 'type' => 'textarea', 'cols' => 60, 'rows' => 12))?>
		</td>
	</tr>
	<tr>
		<th valign="top">
			Cena bez DPH
		</th>
		<td>
			<?=$form->input('price', array('label' => false))?>
		</td>
	</tr>
	<tr>
		<th valign="top">
			Hladina DPH
		</th>
		<td>
			<?=$form->input('tax_class_id', array('label' => false))?>
		</td>
	</tr>
	<tr>
		<th valign="top">
			Cena s DPH
		</th>
		<td>
			<?=$form->input('price_tax', array('label' => false)) ?>
		</td>
	</tr>
	<tr>
		<th valign="top">
			Nákupní cena
		</th>
		<td>
			<?=$form->input('price_buy', array('label' => false)) ?>
		</td>
	</tr>
</table>
<?=$form->submit('uložit') ?>
<?
	if ( isset($category_id) ){
		echo $form->hidden('Category.id', array('value' => $category_id));
	}
	echo $form->end();
?>