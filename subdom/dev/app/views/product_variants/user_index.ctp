<h1>Číselník zboží</h1>

<button id="search_form_show_product_variants">vyhledávací formulář</button>
<?php
	$hide = ' style="display:none"';
	if ( isset($this->data['ProductVariantSearch2']) ){
		$hide = '';
	}
?>
<div id="search_form_product_variants"<?php echo $hide?>>
	<?php echo $form->create('ProductVariant', array('url' => array('controller' => 'product_variants', 'action' => 'index'))); ?>
	<table class="left_heading">
		<tr>
			<th>Název</th>
			<td><?php echo $form->input('ProductVariantSearch2.Product.name', array('label' => false))?></td>
			<th>Kód VZP</th>
			<td><?php echo $form->input('ProductVariantSearch2.Product.vzp_code', array('label' => false))?></td>
			<th>Kód skupiny</th>
			<td><?php echo $form->input('ProductVariantSearch2.Product.group_code', array('label' => false))?></td>
		</tr>
		<tr>
			<th>Referenční číslo</th>
			<td><?php echo $this->Form->input('ProductVariantSearch2.Product.referential_number', array('label' => false))?></td>
			<th>LOT</th>
			<td><?php echo $this->Form->input('ProductVariantSearch2.ProductVariant.lot', array('label' => false))?></td>
			<th>EXP</th>
			<td><?php echo $this->Form->input('ProductVariantSearch2.ProductVariant.exp', array('label' => false))?></td>
		</tr>
		<tr>
			<td colspan="6">
				<?php echo $html->link('reset filtru', array('controller' => 'product_variants', 'action' => 'index', 'reset' => 'product_variants')) ?>
			</td>
		</tr>
	</table>
	<?php
		echo $form->hidden('ProductVariantSearch2.ProductVariant.search_form', array('value' => 1));
		echo $form->submit('Vyhledávat');
		echo $form->end();
	?>
</div>

<script>
	$("#search_form_show_product_variants").click(function () {
		if ($('#search_form_product_variants').css('display') == "none"){
			$("#search_form_product_variants").show("slow");
		} else {
			$("#search_form_product_variants").hide("slow");
		}
	});
</script>

<?php
echo $form->create('CSV', array('url' => array('controller' => 'product_variants', 'action' => 'xls_export')));
echo $form->hidden('data', array('value' => serialize($find)));
echo $form->hidden('fields', array('value' => serialize($export_fields)));
echo $form->submit('CSV');
echo $form->end();

if (empty($product_variants)) { ?>
<p><em>Číselník zboží je prázdný.</em></p>
<?php } else { ?>
<table class="top_heading">
	<tr>
		<th><?php echo $this->Paginator->sort('Kód VZP', 'Product.vzp_code')?></th>
		<th><?php echo $this->Paginator->sort('Kód skupiny', 'Product.group_code')?></th>
		<th><?php echo $this->Paginator->sort('Referenční číslo', 'Product.referential_number')?></th>
		<th><?php echo $this->Paginator->sort('Název', 'Product.name')?></th>
		<th><?php echo $this->Paginator->sort('Název anglicky', 'Product.en_name')?></th>
		<th><?php echo $this->Paginator->sort('Jednotka', 'Unit.name')?></th>
		<th><?php echo $this->Paginator->sort('Cena', 'ProductVariant.meavita_price')?></th>
		<th><?php echo $this->Paginator->sort('Mn.', 'ProductVariant.meavita_quantity')?></th>
		<th><?php echo $this->Paginator->sort('Marže', 'ProductVariant.meavita_margin')?></th>
		<th><?php echo $this->Paginator->sort('LOT', 'ProductVariant.lot')?></th>
		<th><?php echo $this->Paginator->sort('EXP', 'ProductVariant.exp')?></th>
		<th>&nbsp;</th>
	</tr>
	<?php foreach ($product_variants as $product_variant) { ?>
	<tr>
		<td><?php echo $product_variant['Product']['vzp_code']?></td>
		<td><?php echo $product_variant['Product']['group_code']?></td>
		<td><?php echo $product_variant['Product']['referential_number']?></td>
		<td><?php echo $product_variant['Product']['name']?></td>
		<td><?php echo $product_variant['Product']['en_name']?></td>
		<td><?php echo $product_variant['Unit']['name']?></td>
		<td><?php echo $product_variant['ProductVariant']['meavita_price']?></td>
		<td><?php echo $product_variant['ProductVariant']['meavita_quantity']?></td>
		<td><?php echo $product_variant['ProductVariant']['meavita_margin']?></td>
		<td><?php echo $product_variant['ProductVariant']['lot']?></td>
		<td><?php echo $product_variant['ProductVariant']['exp']?></td>
		<td><?php
			echo $this->Html->link('Upravit', array('action' => 'edit', $product_variant['ProductVariant']['id'])) . ' | ';
			echo $this->Html->link('Smazat', array('action' => 'delete', $product_variant['ProductVariant']['id']), null, 'Opravdu chcete produkt odstranit?');
		?></td>
	</tr>
	<?php } ?>
</table>
<?php echo $this->Paginator->prev('« Předchozí', null, null, array('class' => 'disabled')); ?>
<?php echo $this->Paginator->numbers(); ?>
<?php echo $this->Paginator->next('Další »', null, null, array('class' => 'disabled')); ?>
<?php } ?>