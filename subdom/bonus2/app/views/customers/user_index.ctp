<h1><?php __('Hledání zákazníka')?></h1>

<?php echo $this->element('search_forms/customers')?>

<?php 
	$result_message = 'Všichni zákazníci';
	if (isset($this->data['CustomerForm'])) {
		$result_message = 'Výsledek hledání';
	}
?>
<p><?php echo $result_message?>:  <?php echo $this->Paginator->counter(array('format' => 'nalezeno %count% záznamů')) ?>

<?php if (empty($customers)) {
	$no_results_message = 'V systému nejsou žádní zákazníci.';
	if (isset($this->data['CustomerForm'])) {
		$no_results_message = 'V systému nejsou žádní zákazníci, kteří by odpovídali hodnotám filtru.';
	}
?>
<p><em><?php echo $no_results_message?></em></p>
<?php } else { ?>
<table class="top_heading">
	<tr>
		<th><?php echo $this->Paginator->sort('Zákaznické číslo', 'Customer.number')?></th>
		<th><?php echo $this->Paginator->sort('Příjmení', 'Customer.last_name')?></th>
		<th><?php echo $this->Paginator->sort('Jméno', 'Customer.first_name')?></th>
		<th><?php echo $this->Paginator->sort('Email', 'Customer.email')?></th>
		<th><?php echo $this->Paginator->sort('Titul před jménem', 'Customer.degree_before')?></th>
		<th><?php echo $this->Paginator->sort('Titul za jménem', 'Customer.degree_after')?></th>
		<th><?php echo $this->Paginator->sort('Oslovení', 'Customer.salutation')?></th>
		<th><?php echo $this->Paginator->sort('Pohlaví', 'Customer.sex')?></th>
		<th><?php echo $this->Paginator->sort('Ulice + č. p.', 'Customer.street')?></th>
		<th><?php echo $this->Paginator->sort('PSČ', 'Customer.zip')?></th>
		<th><?php echo $this->Paginator->sort('Město', 'Customer.city')?></th>
		<th><?php echo $this->Paginator->sort('RČ', 'Customer.birth_certificate_number')?></th>
		<th><?php echo $this->Paginator->sort('Doporučující osoba', 'RecommendingCustomer.name')?></th>
		<th><?php echo $this->Paginator->sort('Výše slevy', 'Tariff.name')?></th>
		<th><?php echo $this->Paginator->sort('Stav účtu', 'Customer.account')?></th>
		<th colspan="4">&nbsp;</th>
	</tr>
	<?php foreach ($customers as $customer) { ?>
	<tr>
		<td><?php echo $customer['Customer']['number']?></td>
		<td><?php echo $this->Html->link($customer['Customer']['last_name'], array('controller' => 'customers', 'action' => 'view', $customer['Customer']['id']))?></td>
		<td><?php echo $customer['Customer']['first_name']?></td>
		<td><?php echo $customer['Customer']['email']?></td>
		<td><?php echo $customer['Customer']['degree_before']?></td>
		<td><?php echo $customer['Customer']['degree_after']?></td>
		<td><?php echo $customer['Customer']['salutation']?></td>
		<td><?php echo $customer['Customer']['sex']?></td>
		<td><?php echo $customer['Customer']['street']?></td>
		<td><?php echo $customer['Customer']['zip']?></td>
		<td><?php echo $customer['Customer']['city']?></td>
		<td><?php echo $customer['Customer']['birth_certificate_number']?></td>
		<td><?php echo $customer['RecommendingCustomer']['name']?></td>
		<td><?php echo $customer['Tariff']['name']?></td>
		<td><?php echo $customer['Customer']['account']?></td>
		<td><?php echo $this->Html->link('<img src="/images/icons/shopping-cart-32.png" alt="Zadat prodej" />', array('controller' => 'sales', 'action' => 'index', 'customer_id' => $customer['Customer']['id']), array('escape' => false, 'title' => 'Zadat prodej'))?></td>
		<td><?php echo $this->Html->link('<img src="/images/icons/money-bag-32.png" alt="Vyplatit bonus" />', array('controller' => 'pay_outs', 'action' => 'index', 'customer_id' => $customer['Customer']['id']), array('escape' => false, 'title' => 'Vyplatit bonus'))?></td>
		<td><?php echo $this->Html->link('<img src="/images/icons/edit-32.png" alt="Upravit" />', array('controller' => 'customers', 'action' => 'edit', $customer['Customer']['id']), array('escape' => false, 'title' => 'Upravit'))?></td>
		<td><?php echo $this->Html->link('<img src="/images/icons/trash-32.png" alt="Smazat" />', array('controller' => 'customers', 'action' => 'delete', $customer['Customer']['id']), array('escape' => false, 'title' => 'Smazat'), 'Opravdu chcete zákazníka smazat?')?></td>
	</tr>
	<?php }?>
</table>

<?php echo $this->Paginator->prev('« Předchozí', null, null, array('class' => 'disabled')); ?>
<!-- Shows the page numbers -->
<?php echo $this->Paginator->numbers(); ?>
<!-- Shows the next and previous links -->
<?php echo $this->Paginator->next('Další »', null, null, array('class' => 'disabled')); ?>
<p><?php echo $this->Html->link('Export výsledků do csv', array('controller' => 'customers', 'action' => 'csv') + $this->passedArgs)?></p>
<?php } ?>