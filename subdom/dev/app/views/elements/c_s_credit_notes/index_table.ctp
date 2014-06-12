<table class="top_heading">
	<tr>
		<th><?php echo $this->Paginator->sort('Číslo', 'CSCreditNote.code')?></th>
		<th><?php echo $this->Paginator->sort('Odběratel', 'BusinessPartner.name')?></th>
		<th><?php echo $this->Paginator->sort('Datum vystavení', 'CSCreditNote.date_of_issue')?></th>
		<th><?php echo $this->Paginator->sort('Datum splatnosti', 'CSCreditNote.due_date')?></th>
		<th><?php echo $this->Paginator->sort('Celková částka', 'CSCreditNote.amount')?></th>
		<th><?php echo $this->Paginator->sort('Název zboží', 'CSTransactionItem.product_name')?></th>
		<th><?php echo $this->Paginator->sort('Mn.', 'CSTransactionItem.quantity')?></th>
		<th><?php echo $this->Paginator->sort('MJ', 'Unit.shortcut')?></th>
		<th><?php echo $this->Paginator->sort('Cena za jednotku', 'CSTransactionItem.price')?></th>
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
	foreach ($c_s_credit_notes as $credit_note) {
		$odd = ( $odd == ' class="odd"' ? '' : ' class="odd"' );
	?>
	<tr<?php echo $odd?>>
		<td><?php echo $this->Html->link($credit_note['CSCreditNote']['code'], array('user' => false, 'action' => 'view_pdf', $credit_note['CSCreditNote']['id']), array('target' => '_blank'))?></td>
		<td><?php echo $this->Html->link($credit_note['BusinessPartner']['name'], array('controller' => 'business_partners', 'action' => 'view', $credit_note['BusinessPartner']['id'], 'tab' => 14))?></td>
		<td><?php echo $credit_note['CSCreditNote']['date_of_issue']?></td>
		<td><?php echo czech_date($credit_note['CSCreditNote']['due_date'])?></td>
		<td><?php echo $credit_note['CSCreditNote']['amount']?></td>
		<td><?php echo $credit_note['CSTransactionItem']['product_name']?></td>
		<td><?php echo $credit_note['CSTransactionItem']['quantity']?></td>
		<td><?php echo $credit_note['Unit']['shortcut']?></td>
		<td><?php echo $credit_note['CSTransactionItem']['price']?></td>
		<td><?php echo $credit_note['Currency']['shortcut']?></td>
		<td><?php echo $credit_note['Product']['vzp_code']?></td>
		<td><?php echo $credit_note['Product']['group_code']?></td>
		<td><?php echo $credit_note['Product']['referential_number']?></td>
		<td><?php echo $credit_note['ProductVariant']['lot']?></td>
		<td><?php echo $credit_note['ProductVariant']['exp']?></td>
		<td><?php echo $credit_note['User']['last_name']?></td>
		<td><?php echo $this->Html->link('Upravit', array('action' => 'edit', $credit_note['CSCreditNote']['id']))?></td>
	</tr>
	<?php } ?>
</table>
<?php 
	echo $this->Paginator->prev('« Předchozí', null, null, array('class' => 'disabled'));
	echo $this->Paginator->numbers();
	echo $this->Paginator->next('Další »', null, null, array('class' => 'disabled'));
?>