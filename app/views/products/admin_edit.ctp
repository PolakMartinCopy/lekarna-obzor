<h2>Editace produktu</h2>
<div class="product">
<?php echo $form->create('Product', array('url' => '/admin/products/edit/' . $this->data['Product']['id'] . '/' . $opened_category_id));?>
	<fieldset>
 		<legend>Produkt</legend>
		<table class="leftHeading" cellpadding="5" cellspacing="3">
			<tr>
				<th>
					Název:
				</th>
				<td>
					<?=$form->text('name', array('size' => 60))?>
					<?=$form->error('Product.name')?>
				</td>
			</tr>
			<tr>
				<th>Nadpis:</th>
				<td><?php echo $form->input('Product.heading', array('label' => false, 'size' => 60))?></td>
			</tr>
			<tr>
				<th>Breadcrumb:</th>
				<td><?php echo $form->input('Product.breadcrumb', array('label' => false, 'size' => 60))?></td>
			</tr>
			<tr>
				<th>Název v souvisejících</th>
				<td><?php echo $form->input('Product.related_name', array('label' => false, 'size' => 60))?></td>
			</tr>
			<tr>
				<th>Název - zbozi.cz</th>
				<td><?php echo $form->input('Product.zbozi_name', array('label' => false, 'size' => 60))?></td>
			</tr>
			<tr>
				<th>
					Výrobce:
				</th>
				<td>
					<?=$form->input('manufacturer_id', array('label' => ''))?>
				</td>
			</tr>
			<tr>
				<th>
					Dostupnost:
				</th>
				<td>
					<?=$form->input('availability_id', array('label' => ''))?>
				</td>
			</tr>
			<tr>
				<th>
					Poznámka
				</th>
				<td>
					<?=$form->input('note', array(
						'label' => '',
						'after' => '<br /><span style="font-size:9px">Poznámka se zobrazí při objednávacím formuláři. Použít např. když produkt není skladem.</span>',
						'style' => 'width:600px;height:40px;'
					))?>
				</td>
			</tr>
			<tr>
				<th>
					Krátký popis:
				</th>
				<td>
					<?=$form->textarea('short_description', array('style' => 'width:600px;height:40px;'))?>
					<?=$form->error('Product.short_description')?>
				</td>
			</tr>
			<tr>
				<th>
					Popis:
				</th>
				<td>
					<?=$form->textarea('description', array('style' => 'width:600px;height:350px;'))?>
					<?=$form->error('Product.description')?>
				</td>
			</tr>
			<tr>
				<th>Skupina</th>
				<td>
					<?php echo $this->Form->input('Product.show_product_type_id', array('label' => false, 'type' => 'checkbox', 'div' => false))?>
					<?php echo $this->Form->input('Product.product_type_id', array('label' => false, 'type' => 'select', 'options' => $product_types, 'div' => false))?>
				</td>
			</tr>
			<tr>
				<th>Kód zboží</th>
				<td>
					<?php echo $this->Form->input('Product.show_code', array('label' => false, 'type' => 'checkbox', 'div' => false))?>
					<?php echo $this->Form->input('Product.code', array('label' => false, 'div' => false))?>
				</td>
			</tr>
			<tr>
				<th>EAN</th>
				<td>
					<?php echo $this->Form->input('Product.show_ean', array('label' => false, 'type' => 'checkbox', 'div' => false))?>
					<?php echo $this->Form->input('Product.ean', array('label' => false, 'div' => false))?>
				</td>
			</tr>
			<tr>
				<th>Kód SÚKL</th>
				<td>
					<?php echo $this->Form->input('Product.show_sukl', array('label' => false, 'type' => 'checkbox', 'div' => false))?>
					<?php echo $this->Form->input('Product.sukl', array('label' => false, 'div' => false))?>
				</td>
			</tr>
			<tr>
				<th>Farmakoterapeutická skupina</th>
				<td>
					<?php echo $this->Form->input('Product.show_group', array('label' => false, 'type' => 'checkbox', 'div' => false))?>
					<?php echo $this->Form->input('Product.group', array('label' => false, 'size' => 70, 'div' => false))?>
				</td>
			</tr>
			<tr>
				<th>Max CPC zbozi.cz</th>
				<td><?php echo $this->Form->input('Product.zbozi_cpc', array('label' => false))?></td>
			</tr>
			<tr>
				<th>Max CPC heureka.cz</th>
				<td><?php echo $this->Form->input('Product.heureka_cpc', array('label' => false))?></td>
			</tr>
			<tr>
				<th>
					Daňová skupina:
				</th>
				<td>
					<?=$form->input('tax_class_id', array('label' => ''))?>
					<?=$form->error('Product.tax_class_id')?>
				</td>
			</tr>
			<tr>
				<th>Cena bez DPH:</th>
				<td><input type="text" name="price_without_tax" id="ProductPriceWithoutTax" onkeyup="return countPrice('with')" /></td>
			</tr>
			<tr>
				<th>
					Základní cena:
				</th>
				<td>
					<?=$form->text('retail_price_with_dph')?>
					<?=$form->error('Product.retail_price_with_dph')?>
				</td>
			</tr>
			<tr>
				<td colspan="2">
					Slevové ceny
				</td>
			</tr>
			<tr>
				<th>
					<abbr title="Běžná sleva z ceny">Běžná sleva</abbr>
				</th>
				<td>
					<?=$form->text('Product.discount_common') ?>
				</td>
			</tr>
			<tr>
				<th>
					<abbr title="Sleva z ceny pro přihlášeného zákazníka">Členská sleva</abbr>
				</th>
				<td>
					<?=$form->text('Product.discount_member') ?>
				</td>
			</tr>
			<tr>
				<td colspan="2" align="center">
					----------- níže uvedené nevyplňujte -----------
				</td>
			</tr>
			<tr>
				<th>
					Titulek
				</th>
				<td>
					<?=$form->text('title', array('size' => 60))?>
				</td>
			</tr>
			<tr>
				<th>
					URL
				</th>
				<td>
					<?=$form->text('url', array('size' => 60))?>
				</td>
			</tr>
		</table>
	<?php echo $form->input('id'); ?>
	</fieldset>
<?php echo $form->end('Uložit změny');?>
<div class="actions">
	<ul>
		<li><?php echo $html->link(__('Zpět na seznam produktů', true), array('controller'=> 'categories', 'action'=>'list_products', $opened_category_id)); ?> </li>
	</ul>
</div>
</div>