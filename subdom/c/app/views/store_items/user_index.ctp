<h1>Sklady odběratelů</h1>
<?php
	echo $this->element('search_forms/stores');

	echo $form->create('CSV', array('url' => array('controller' => 'store_items', 'action' => 'xls_export')));
	echo $form->hidden('data', array('value' => serialize($find)));
	echo $form->hidden('fields', array('value' => serialize($export_fields)));
	echo $form->submit('CSV');
	echo $form->end();
?>

<?php if (empty($stores)) { ?>
<p><em>Sklady všech odběratelů jsou prázdné</em></p>
<?php } else { ?>
<table class="top_heading">
	<tr>
		<th><?php echo $this->Paginator->sort('Odběratel', 'BusinessPartner.name')?></th>
		<th><?php echo $this->Paginator->sort('Okres', 'Address.region')?></th>
		<th><?php echo $this->Paginator->sort('Město', 'Address.city')?></th>
		<th><?php echo $this->Paginator->sort('Název produktu', 'Product.name')?></th>
		<th><?php echo $this->Paginator->sort('Kód VZP', 'Product.vzp_code')?></th>
		<th><?php echo $this->Paginator->sort('Kód skupiny', 'Product.group_code')?></th>
		<th><?php echo $this->Paginator->sort('Množství', 'StoreItem.quantity')?></th>
		<th><?php echo $this->Paginator->sort('MJ', 'Unit.shortcut')?></th>
		<th><?php echo $this->Paginator->sort('Kč/J', 'Product.price')?></th>
		<th><?php echo $this->Paginator->sort('Kč', 'StoreItem.item_total_price')?></th>
	</tr>
	<?php
	$odd = '';
	foreach ($stores as $store) {
		$odd = ( $odd == ' class="odd"' ? '' : ' class="odd"' );
	?>
	<tr<?php echo $odd?>>
		<td><?php echo $this->Html->link($store['BusinessPartner']['name'], array('controller' => 'business_partners', 'action' => 'view', $store['BusinessPartner']['id'], 'tab' => 9))?></td>
		<td><?php echo $store['Address']['region']?></td>
		<td><?php echo $store['Address']['city']?></td>
		<td><?php echo $store['Product']['name']?></td>
		<td><?php echo $store['Product']['vzp_code']?></td>
		<td><?php echo $store['Product']['group_code']?></td>
		<td><?php echo $store['StoreItem']['quantity']?></td>
		<td><?php echo $store['Unit']['shortcut']?></td>
		<td><?php echo $store['Product']['price']?></td>
		<td><?php echo $store['StoreItem']['item_total_price']?></td>
	</tr>
	<?php } ?>
</table>
<?php echo $this->Paginator->prev('« Předchozí', null, null, array('class' => 'disabled')); ?>
<?php echo $this->Paginator->numbers(); ?>
<?php echo $this->Paginator->next('Další »', null, null, array('class' => 'disabled')); ?>
<?php } ?>