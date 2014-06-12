<h2>Filtr prodejů</h2>
<div id="search_form">
	<?php echo $this->Form->create('Sale', array('url' => array('controller' => 'sales', 'action' => 'index') + $this->passedArgs)); ?>
	<table class="left_heading">
		<tr>
			<th>Zákaznické číslo</th>
			<td><?php echo $this->Form->input('SaleForm.Customer.number', array('label' => false))?></td>
			<th>Ulice + č. p.</th>
			<td><?php echo $this->Form->input('SaleForm.Customer.street', array('label' => false))?></td>
		</tr>
		<tr>
			<th>Příjmení</th>
			<td><?php echo $this->Form->input('SaleForm.Customer.last_name', array('label' => false))?></td>
			<th>PSČ</th>
			<td><?php echo $this->Form->input('SaleForm.Customer.zip', array('label' => false))?></td>
		</tr>
		<tr>
			<th>Jméno</th>
			<td><?php echo $this->Form->input('SaleForm.Customer.first_name', array('label' => false))?></td>
			<th>Město</th>
			<td><?php echo $this->Form->input('SaleForm.Customer.city', array('label' => false))?></td>
		</tr>
		<tr>
			<th>Doporučující osoba</th>
			<td><?php echo $this->Form->input('SaleForm.RecommendingCustomer.name', array('label' => false))?></td>
			<td colspan="2">&nbsp;</td>
		</tr>
		<tr>
			<td colspan="4"><?php echo $html->link('reset filtru', array('controller' => 'sales', 'action' => 'index', 'reset' => 'sales') + $this->passedArgs) ?></td>
		</tr>
	</table>
	<?php
		echo $form->hidden('SaleForm.Sale.search_form', array('value' => 1));
		echo $form->submit('Hledat');
		echo $form->end();
	?>
</div>