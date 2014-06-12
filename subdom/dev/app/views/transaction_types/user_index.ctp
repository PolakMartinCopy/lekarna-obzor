<h1>Typy transakcí</h1>
<ul>
	<li><?php echo $this->Html->link('Přidat typ transakce', array('action' => 'add'))?></li>
</ul>

<?php if (empty($transaction_types)) { ?>
<p><em>V systému nejsou žádné typy transakcí.</em></p>
<?php } else { ?>
<table class="top_heading">
	<tr>
		<th>ID</th>
		<th>Název</th>
		<th>Přičítam do skladu</th>
		<th>&nbsp;</th>
	</tr>
	<?php foreach ($transaction_types as $transaction_type) { ?>
	<tr>	
		<td><?php echo $transaction_type['TransactionType']['id']?></td>
		<td><?php echo $transaction_type['TransactionType']['name']?></td>
		<td><?php echo ($transaction_type['TransactionType']['sign'] ? 'ano' : 'ne')?></td>
		<td><?php
			echo $this->Html->link('Upravit', array('action' => 'edit', $transaction_type['TransactionType']['id'])) . ' | ';
			echo $this->Html->link('Smazat', array('action' => 'delete', $transaction_type['TransactionType']['id']), array(), 'Opravdu chcete typ transakce ' . $transaction_type['TransactionType']['name'] . ' odstranit?');
		?></td>
	</tr>
	<?php } ?>
</table>
<?php } ?>

<ul>
	<li><?php echo $this->Html->link('Přidat typ transakce', array('action' => 'add'))?></li>
</ul>