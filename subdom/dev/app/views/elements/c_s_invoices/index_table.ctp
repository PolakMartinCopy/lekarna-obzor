<?php $this->Paginator->options(array('escape' => false))?>

<table class="top_heading">
	<tr>
		<th><?php echo $this->Paginator->sort('Číslo', 'CSInvoice.code')?></th>
		<th><?php echo $this->Paginator->sort('Odběratel', 'BusinessPartner.name')?></th>
		<th><?php echo $this->Paginator->sort('Datum vystavení', 'CSInvoice.date_of_issue')?></th>
		<th><?php echo $this->Paginator->sort('Datum splatnosti', 'CSInvoice.due_date')?></th>
		<th><?php echo $this->Paginator->sort('Číslo objednávky', 'CSInvoice.order_number')?></th>
		<th><?php echo $this->Paginator->sort('Celková částka bez daně', 'CSInvoice.amount')?></th>
		<th><?php echo $this->Paginator->sort('Název zboží', 'CSTransactionItem.product_name')?></th>
		<th><?php echo $this->Paginator->sort('Mn.', 'CSTransactionItem.quantity')?></th>
		<th><?php echo $this->Paginator->sort('MJ', 'Unit.shortcut')?></th>
		<th><?php echo $this->Paginator->sort('Cena za jednotku bez daně', 'CSTransactionItem.price')?></th>
		<th><?php echo $this->Paginator->sort('Měna', 'Currency.shortcut')?></th>
		<th><?php echo $this->Paginator->sort('Jazyk', 'Language.shortcut')?></th>
		<th><?php echo $this->Paginator->sort('VZP kód', 'Product.vzp_code')?></th>
		<th><?php echo $this->Paginator->sort('Kód skupiny', 'Product.group_code')?></th>
		<th><?php echo $this->Paginator->sort('Referenční číslo', 'Product.referential_number')?></th>
		<th><?php echo $this->Paginator->sort('LOT', 'ProductVariant.lot')?></th>
		<th><?php echo $this->Paginator->sort('EXP', 'ProductVariant.exp')?></th>
		<th><?php echo $this->Paginator->sort('Vystavil', 'User.last_name')?></th>
		<th>&nbsp;</th>
	</tr>
	<?php 
	$odd = '';
	foreach ($c_s_invoices as $invoice) {
		$odd = ( $odd == ' class="odd"' ? '' : ' class="odd"' );
	?>
	<tr<?php echo $odd?>>
		<td><?php echo $this->Html->link($invoice['CSInvoice']['code'], array('user' => false, 'action' => 'view_pdf', $invoice['CSInvoice']['id']), array('target' => '_blank'))?></td>
		<td><?php echo $this->Html->link($invoice['BusinessPartner']['name'], array('controller' => 'business_partners', 'action' => 'view', $invoice['BusinessPartner']['id'], 'tab' => 14))?></td>
		<td><?php echo $invoice['CSInvoice']['date_of_issue']?></td>
		<td><?php echo czech_date($invoice['CSInvoice']['due_date'])?></td>
		<td><?php echo $invoice['CSInvoice']['order_number']?></td>
		<td><?php echo $invoice['CSInvoice']['amount']?></td>
		<td><?php echo $invoice['CSTransactionItem']['product_name']?></td>
		<td><?php echo $invoice['CSTransactionItem']['quantity']?></td>
		<td><?php echo $invoice['Unit']['shortcut']?></td>
		<td><?php echo $invoice['CSTransactionItem']['price']?></td>
		<td><?php echo $invoice['Currency']['shortcut']?></td>
		<td><?php echo $invoice['Language']['shortcut']?></td>
		<td><?php echo $invoice['Product']['vzp_code']?></td>
		<td><?php echo $invoice['Product']['group_code']?></td>
		<td><?php echo $invoice['Product']['referential_number']?></td>
		<td><?php echo $invoice['ProductVariant']['lot']?></td>
		<td><?php echo $invoice['ProductVariant']['exp']?></td>
		<td><?php echo $invoice['User']['last_name']?></td>
		<td><?php
			echo $this->Html->link('Upravit', array('controller' => 'c_s_invoices', 'action' => 'edit', $invoice['CSInvoice']['id']));
			echo '<br/>';
			echo $this->Html->link('Dodací list', array('user' => false, 'controller' => 'c_s_invoices', 'action' => 'view_pdf_delivery_note', $invoice['CSInvoice']['id']), array('target' => '_blank'))
		?></td>
	</tr>
	<?php } ?>
</table>
<?php 
	echo $this->Paginator->prev('« Předchozí', null, null, array('class' => 'disabled'));
	echo $this->Paginator->numbers();
	echo $this->Paginator->next('Další »', null, null, array('class' => 'disabled'));
?>