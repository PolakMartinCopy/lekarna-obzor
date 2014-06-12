<h1>Potvrzení doporučující osoby u prvního prodeje</h1>
<p>Zákazník provádí <strong>první nákup</strong>. Potvrďte prosím správnost přiřazené doporučující osoby.</p>
<?php echo $this->Form->create('Sale', array('action' => 'add', 'url' => $this->passedArgs))?>
<table class="left_heading">
	<tr>
		<th>Doporučující osoba</th>
		<td><?php echo $this->Form->input('Customer.recommending_customer_name', array('label' => false, 'value' => $customer['RecommendingCustomer']['name'], 'disabled' => true))?></td>
	</tr>
</table>

<?php echo $this->Form->hidden('Sale.date')?>
<?php echo $this->Form->hidden('Sale.customer_id')?>
<?php echo $this->Form->hidden('Sale.price')?>
<?php echo $this->Form->hidden('Sale.customer_bonus')?>
<?php echo $this->Form->hidden('Sale.recommending_customer_bonus')?>
<?php echo $this->Form->button('Vložit prodej a potvrdit doporučující osobu', array('name' => 'data[Customer][confirm]', 'value' => 1, 'div' => false))?>&nbsp;
<?php echo $this->Form->button('Vložit prodej a zamítnout doporučující osobu', array('name' => 'data[Customer][confirm]', 'value' => 0, 'div' => false))?>
<?php echo $this->Form->end()?> 