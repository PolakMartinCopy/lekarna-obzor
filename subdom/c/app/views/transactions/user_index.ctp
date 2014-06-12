<h1><?php echo ucfirst($header)?></h1>
<?php
	echo $this->element('search_forms/transactions');

	echo $form->create('CSV', array('url' => array('controller' => $this->params['controller'], 'action' => 'xls_export')));
	echo $form->hidden('data', array('value' => serialize($find)));
	echo $form->hidden('fields', array('value' => serialize($export_fields)));
	echo $form->submit('CSV');
	echo $form->end();
?>

<?php if (empty($transactions)) { ?>
<p><em>V systému nejsou žádné <?php echo $header?>.</em></p>
<?php } else {
	$quantity_field = 'abs_quantity';
	$total_price_field = 'abs_total_price';
	$margin_field = 'abs_margin';
	if ($model == 'Transaction') {
		$quantity_field = 'quantity';
		$total_price_field = 'total_price';
		$margin_field = 'margin';
	}	
?>
<table class="top_heading">
	<tr>
		<th><?php echo $this->Paginator->sort('Datum vys.', $model . '.date')?></th>
		<th><?php echo $this->Paginator->sort('Číslo dokladu', $model . '.code')?></th>
		<th><?php echo $this->Paginator->sort('Odběratel', 'BusinessPartner.name')?></th>
		<th><?php echo $this->Paginator->sort('Název zboží', 'Product.name')?></th>
		<th><?php echo $this->Paginator->sort('Mn.', $model . '.' . $quantity_field)?></th>
		<th><?php echo $this->Paginator->sort('MJ', 'Unit.shortcut')?></th>
		<th><?php echo $this->Paginator->sort('Kč/J', 'ProductsTransaction.unit_price')?></th>
		<th><?php echo $this->Paginator->sort('Marže produktu', 'ProductsTransaction.product_margin')?></th>
		<th><?php echo $this->Paginator->sort('Celkem', $model . '.' . $total_price_field)?></th>
		<th><?php echo $this->Paginator->sort('Marže', $model . '.' . $margin_field)?></th>
		<th><?php echo $this->Paginator->sort('VZP kód', 'Product.vzp_code')?></th>
		<th><?php echo $this->Paginator->sort('Kód skupiny', 'Product.group_code')?></th>
		<th>&nbsp;</th>
	</tr>
	<?php 
	$odd = '';
	foreach ($transactions as $transaction) {
		$odd = ( $odd == ' class="odd"' ? '' : ' class="odd"' );
	?>
	<tr<?php echo $odd?>>
		<td><?php echo czech_date($transaction[$model]['date'])?></td>
		<td><?php
		if ($transaction['TransactionType']['id'] == 1) {
			echo $this->Html->link($transaction[$model]['code'], '/' . DL_FOLDER . $transaction[$model]['id'] . '.pdf', array('target' => '_blank'));
		} else {
			echo $transaction[$model]['code'];
		} ?></td>
		<td><?php
			switch ($model) {
				case 'DeliveryNote': $active_tab = 10; break;
				case 'Sale': $active_tab = 11; break;
				default: $active_tab = 12; break;
			} 
			echo $this->Html->link($transaction['BusinessPartner']['name'], array('controller' => 'business_partners', 'action' => 'view', $transaction['BusinessPartner']['id'], 'tab' => $active_tab))?></td>
		<td><?php echo $transaction['Product']['name']?></td>
<?php ?>
		<td><?php echo $transaction[$model][$quantity_field]?></td>
		<td><?php echo $transaction['Unit']['shortcut']?></td>
		<td><?php echo $transaction['ProductsTransaction']['unit_price']?></td>
		<td><?php echo $transaction['ProductsTransaction']['product_margin']?></td>
		<td><?php echo $transaction[$model][$total_price_field]?></td>
		<td><?php echo $transaction[$model][$margin_field]?></td>
		<td><?php echo $transaction['Product']['vzp_code']?></td>
		<td><?php echo $transaction['Product']['group_code']?></td>
		<td><?php 
			echo $this->Html->link('Upravit', array('action' => 'edit', $transaction[$model]['id'])) . ' | ';
			echo $this->Html->link('Smazat', array('action' => 'delete', $transaction[$model]['id']), array(), 'Opravdu chcete transakci smazat?') . ' | ';
			echo $this->Html->link('Smazat položku', array('controller' => 'products_transactions', 'action' => 'delete', $transaction['ProductsTransaction']['id']));
		?></td>
	</tr>
	<?php } ?>
</table>
<?php echo $this->Paginator->prev('« Předchozí', null, null, array('class' => 'disabled')); ?>
<?php echo $this->Paginator->numbers(); ?>
<?php echo $this->Paginator->next('Další »', null, null, array('class' => 'disabled')); ?>

<?php } ?>