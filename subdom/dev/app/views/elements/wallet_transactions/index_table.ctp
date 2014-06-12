<table class="top_heading">
	<tr>
		<th><?php echo $this->Paginator->sort('Datum', 'WalletTransaction.created')?></th>
		<th><?php echo $this->Paginator->sort('Jméno', 'Rep.first_name')?></th>
		<th><?php echo $this->Paginator->sort('Příjmení', 'Rep.last_name')?></th>
		<th><?php echo $this->Paginator->sort('Stav peněženky', 'Rep.wallet')?></th>
		<th><?php echo $this->Paginator->sort('Částka', 'WalletTransaction.amount')?></th>
		<th><?php echo $this->Paginator->sort('Stav po transakci', 'WalletTransaction.amount_after')?></th>
		<th><?php echo $this->Paginator->sort('Vložil', 'User.last_name')?></th>
		<th>&nbsp;</th>
	</tr>
	<?php foreach ($wallet_transactions as $wallet_transaction) { ?>
	<tr>
		<td><?php echo czech_date($wallet_transaction['WalletTransaction']['created'])?></td>
		<td><?php echo $wallet_transaction['Rep']['first_name']?></td>
		<td><?php echo $this->Html->link($wallet_transaction['Rep']['last_name'], array('controller' => 'reps', 'action' => 'view', $wallet_transaction['Rep']['id']))?></td>
		<td><?php echo $wallet_transaction['Rep']['wallet']?></td>
		<td><?php echo $wallet_transaction['WalletTransaction']['amount']?></td>
		<td><?php echo $wallet_transaction['WalletTransaction']['amount_after']?></td>
		<td><?php echo $wallet_transaction['User']['last_name']?></td>
		<td><?php echo $this->Html->link('Příjmový doklad', array('controller' => 'wallet_transactions', 'action' => 'cash_receipt', $wallet_transaction['WalletTransaction']['id']))?>
	<?php } ?>
</table>
<?php
	echo $this->Paginator->prev('« Předchozí', null, null, array('class' => 'disabled'));
	echo $this->Paginator->numbers();
	echo $this->Paginator->next('Další »', null, null, array('class' => 'disabled'));
?>