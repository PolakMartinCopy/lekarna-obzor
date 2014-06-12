<div id="tabs">
	<ul>
		<li><a href="#tabs-1">Editace</a></li>
		<li><a href="#tabs-2">Atributy</a></li>
		<li><a href="#tabs-3">Obrázky</a></li>
		<li><a href="#tabs-4">Dokumenty</a>
		<li><a href="#tabs-5">Další operace</a>
	</ul>
	
	<div id="tabs-1">
<h1>Editace produktu</h1>
<?=$form->create('Product')?>
	<fieldset>
 		<legend>Produkt</legend>
		<table class="left_headed" cellpadding="5" cellspacing="3">
			<tr>
				<th>
					Název produktu
				</th>
				<td>
					<?=$form->input('name', array('label' => false, 'size' => 60))?>
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
	</fieldset>
<?=$form->end();?>
</div>
<div id="tabs-2"></div>
<div id="tabs-3"></div>
<div id="tabs-4"></div>
<div id="tabs-5"></div>
</div>