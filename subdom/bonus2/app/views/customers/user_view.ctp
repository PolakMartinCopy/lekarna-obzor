<script>
	// kalendar na datum u vlozeni prodeje
	$(function() {
		$( "#SaleDate" ).datepicker( $.datepicker.regional[ "cs" ] );
		$( "#PayOutDate" ).datepicker( $.datepicker.regional[ "cs" ] );
	});
</script>

<h1>Transakce na účtu zákazníka <?php echo $customer['Customer']['name']?></h1>
<h2>Přidat prodej</h2>
<?php echo $this->Form->create('Sale', array('action' => 'add', 'url' => array('back_controller' => 'customers', 'back_action' => 'view') + $this->passedArgs))?>
<table class="left_heading">
	<tr>
		<th>Datum</th>
		<td><?php echo $this->Form->input('Sale.date', array('label' => false, 'type' => 'text'))?></td>
	</tr>
	<tr>
		<th>Cena nákupu</th>
		<td><?php echo $this->Form->input('Sale.price', array('label' => false))?></td>
	</tr>
	<tr>
		<th>Výše bonusu pro zákazníka</th>
		<td><?php echo $this->Form->input('Sale.customer_bonus', array('label' => false))?></td>
	</tr>
	<tr>
		<th>Výše bonusu doporučující osoby</th>
		<td><?php echo $this->Form->input('Sale.recommending_customer_bonus', array('label' => false))?></td>
	</tr>
</table>
<?php echo $this->Form->hidden('Sale.customer_id', array('value' => $customer['Customer']['id']))?>
<?php echo $this->Form->submit('Vložit nový prodej')?>
<?php echo $this->Form->end()?>

<h2>Přidat výplatu</h2>
<?php echo $this->Form->create('PayOut', array('action' => 'add', 'url' => array('back_controller' => 'customers', 'back_action' => 'view') + $this->passedArgs))?>
<table class="left_heading">
	<tr>
		<th>Datum</th>
		<td><?php echo $this->Form->input('PayOut.date', array('label' => false, 'type' => 'text'))?></td>
	</tr>
	<tr>
		<th>Výše vyplacené částky</th>
		<td><?php echo $this->Form->input('PayOut.amount', array('label' => false))?></td>
	</tr>
</table>
<?php echo $this->Form->hidden('PayOut.customer_id', array('value' => $customer['Customer']['id']))?>
<?php echo $this->Form->submit('Vložit novou výplatu')?>
<?php echo $this->Form->end()?>
<?php if (empty($transactions)) { ?>
<p><em>Zákazník nemá v systému žádné transakce.</em></p>
<?php } else { ?>
<table class="top_heading">
	<tr>
		<th><?php echo $this->Paginator->sort('Datum', 'date')?></th>
		<th><?php echo $this->Paginator->sort('Typ', 'type')?></th>
		<th><?php echo $this->Paginator->sort('Hodnota', 'price')?></th>
	</tr>
	<?php foreach ($transactions as $transaction) { ?>
	<tr>
		<td><?php echo cz_date($transaction['Transaction']['date'])?></td>
		<td><?php echo $transaction['Transaction']['type']?></td>
		<td><?php echo $transaction['Transaction']['price']?></td>
	</tr>
	<?php } ?>
</table>
<?php echo $this->Paginator->prev('« Předchozí', null, null, array('class' => 'disabled')); ?>
<!-- Shows the page numbers -->
<?php echo $this->Paginator->numbers(); ?>
<!-- Shows the next and previous links -->
<?php echo $this->Paginator->next('Další »', null, null, array('class' => 'disabled')); ?>
<p><?php echo $this->Html->link('Export výsledků do csv', array('controller' => 'transactions', 'action' => 'csv', $customer['Customer']['id']) + $this->passedArgs)?></p>

<?php }?>