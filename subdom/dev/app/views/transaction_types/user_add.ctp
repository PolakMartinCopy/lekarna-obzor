<h1>Přidat typ transakce</h1>
<?php echo $this->Form->create('TransactionType')?>
<table class="left_heading">
	<tr>
		<th>Název</th>
		<td><?php echo $this->Form->input('TransactionType.name', array('label' => false))?></td>
	</tr>
	<tr>
		<th>Přičítam do skladu</th>
		<td><?php echo $this->Form->input('TransactionType.sign', array('label' => false))?></td>
	</tr>
</table>
<?php echo $this->Form->submit('Uložit')?>
<?php echo $this->Form->end()?>