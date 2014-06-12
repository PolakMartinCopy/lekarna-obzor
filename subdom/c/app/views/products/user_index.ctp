<h1>Číselník zboží</h1>

<button id="search_form_show_products">vyhledávací formulář</button>
<?php
	$hide = ' style="display:none"';
	if ( isset($this->data['ProductSearch2']) ){
		$hide = '';
	}
?>
<div id="search_form_products"<?php echo $hide?>>
	<?php echo $form->create('Products', array('url' => array('controller' => 'products', 'action' => 'index'))); ?>
	<table class="left_heading">
		<tr>
			<th>Název</th>
			<td><?php echo $form->input('ProductSearch2.Product.name', array('label' => false))?></td>
			<th>Kód VZP</th>
			<td><?php echo $form->input('ProductSearch2.Product.vzp_code', array('label' => false))?></td>
			<th>Kód skupiny</th>
			<td><?php echo $form->input('ProductSearch2.Product.group_code', array('label' => false))?></td>
		</tr>
		<tr>
			<td colspan="6">
				<?php echo $html->link('reset filtru', array('controller' => 'products', 'action' => 'index', 'reset' => 'products')) ?>
			</td>
		</tr>
	</table>
	<?php
		echo $form->hidden('ProductSearch2.Product.search_form', array('value' => 1));
		echo $form->submit('Vyhledávat');
		echo $form->end();
	?>
</div>

<script>
	$("#search_form_show_products").click(function () {
		if ($('#search_form_products').css('display') == "none"){
			$("#search_form_products").show("slow");
		} else {
			$("#search_form_products").hide("slow");
		}
	});
</script>

<?php
echo $form->create('CSV', array('url' => array('controller' => 'products', 'action' => 'xls_export')));
echo $form->hidden('data', array('value' => serialize($find)));
echo $form->hidden('fields', array('value' => serialize($export_fields)));
echo $form->submit('CSV');
echo $form->end();

if (empty($products)) { ?>
<p><em>Číselník zboží je prázdný.</em></p>
<?php } else { ?>
<table class="top_heading">
	<tr>
		<th><?php echo $this->Paginator->sort('Kód VZP', 'Product.vzp_code')?></th>
		<th><?php echo $this->Paginator->sort('Kód skupiny', 'Product.group_code')?></th>
		<th><?php echo $this->Paginator->sort('Název', 'Product.name')?></th>
		<th><?php echo $this->Paginator->sort('Jednotka', 'Unit.name')?></th>
		<th><?php echo $this->Paginator->sort('Cena', 'Product.price')?></th>
		<th><?php echo $this->Paginator->sort('Marže', 'Product.margin')?></th>
		<th>&nbsp;</th>
	</tr>
	<?php foreach ($products as $product) { ?>
	<tr>
		<td><?php echo $product['Product']['vzp_code']?></td>
		<td><?php echo $product['Product']['group_code']?></td>
		<td><?php echo $product['Product']['name']?></td>
		<td><?php echo $product['Unit']['name']?></td>
		<td><?php echo $product['Product']['price']?></td>
		<td><?php echo $product['Product']['margin']?></td>
		<td><?php
			echo $this->Html->link('Upravit', array('action' => 'edit', $product['Product']['id'])) . ' | ';
			echo $this->Html->link('Smazat', array('action' => 'delete', $product['Product']['id']));
		?></td>
	</tr>
	<?php } ?>
</table>
<?php echo $this->Paginator->prev('« Předchozí', null, null, array('class' => 'disabled')); ?>
<?php echo $this->Paginator->numbers(); ?>
<?php echo $this->Paginator->next('Další »', null, null, array('class' => 'disabled')); ?>
<?php } ?>