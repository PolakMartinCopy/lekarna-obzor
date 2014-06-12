<table class="top_heading">
	<tr>
		<th><?php echo $this->Paginator->sort('Datum nas.', 'CSStoring.date')?></th>
		<th><?php echo $this->Paginator->sort('Název zboží', 'CSTransactionItem.c_s_product_name')?></th>
		<th><?php echo $this->Paginator->sort('Mn.', 'CSTransactionItem.quantity')?></th>
		<th><?php echo $this->Paginator->sort('MJ', 'Unit.shortcut')?></th>
		<th><?php echo $this->Paginator->sort('Měna', 'Currency.shortcut')?></th>
		<th><?php echo $this->Paginator->sort('Cena za jednotku', 'CSTransactionItem.price')?></th>
		<th><?php echo $this->Paginator->sort('VZP kód', 'Product.vzp_code')?></th>
		<th><?php echo $this->Paginator->sort('Kód skupiny', 'Product.group_code')?></th>
		<th><?php echo $this->Paginator->sort('Referencční číslo', 'Product.referential_number')?></th>
		<th><?php echo $this->Paginator->sort('LOT', 'ProductVariant.lot')?></th>
		<th><?php echo $this->Paginator->sort('EXP', 'ProductVariant.exp')?></th>
		<th><?php echo $this->Paginator->sort('Dodavatel', 'BusinessPartner.name')?></th>
		<th><?php echo $this->Paginator->sort('Naskladnil', 'User.last_name')?></th>
		<th>&nbsp;</th>
	</tr>
	<?php 
	$odd = '';
	foreach ($c_s_storings as $storing) {
		$odd = ( $odd == ' class="odd"' ? '' : ' class="odd"' );
	?>
	<tr<?php echo $odd?>>
		<td><?php echo czech_date($storing['CSStoring']['date'])?></td>
		<td><?php echo $storing['CSTransactionItem']['product_name']?></td>
		<td><?php echo $storing['CSTransactionItem']['quantity']?></td>
		<td><?php echo $storing['Unit']['shortcut']?></td>
		<td><?php echo $storing['Currency']['shortcut']?></td>
		<td><?php echo $storing['CSTransactionItem']['price']?></td>
		<td><?php echo $storing['Product']['vzp_code']?></td>
		<td><?php echo $storing['Product']['group_code']?></td>
		<td><?php echo $storing['Product']['referential_number']?></td>
		<td><?php echo $storing['ProductVariant']['lot']?></td>
		<td><?php echo $storing['ProductVariant']['exp']?></td>
		<td><?php echo $this->Html->link($storing['BusinessPartner']['name'], array('controller' => 'business_partners', 'action' => 'view', $storing['BusinessPartner']['id']))?></td>
		<td><?php echo $storing['User']['last_name']?></td>
		<td><?php 
			echo $this->Html->link('Upravit', array('action' => 'edit', $storing['CSStoring']['id'])) . ' | ';
			echo $this->Html->link('Smazat', array('action' => 'delete', $storing['CSStoring']['id']), array(), 'Opravdu chcete naskladnění smazat?') . ' | ';
			echo $this->Html->link('Smazat položku', array('controller' => 'c_s_transaction_items', 'action' => 'delete', $storing['CSTransactionItem']['id']));
		?></td>
	</tr>
	<?php } ?>
</table>
<?php 
	echo $this->Paginator->prev('« Předchozí', null, null, array('class' => 'disabled'));
	echo $this->Paginator->numbers();
	echo $this->Paginator->next('Další »', null, null, array('class' => 'disabled'));
?>