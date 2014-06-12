<script>
	$(document).ready(function() {
		// kurzor se da do prvniho pole
		$('#CustomerFormCustomerNumber').select();
	});
</script>

<div id="search_form">
	<?php echo $this->Form->create('Customer', array('url' => array('controller' => 'customers', 'action' => 'index') + $this->passedArgs)); ?>
	<table class="left_heading">
		<tr>
			<th>Zákaznické číslo</th>
			<td><?php echo $this->Form->input('CustomerForm.Customer.number', array('label' => false))?></td>
			<th>Ulice + č. p.</th>
			<td><?php echo $this->Form->input('CustomerForm.Customer.street', array('label' => false))?></td>
		</tr>
		<tr>
			<th>Příjmení</th>
			<td><?php echo $this->Form->input('CustomerForm.Customer.last_name', array('label' => false))?></td>
			<th>PSČ</th>
			<td><?php echo $this->Form->input('CustomerForm.Customer.zip', array('label' => false))?></td>
		</tr>
		<tr>
			<th>Jméno</th>
			<td><?php echo $this->Form->input('CustomerForm.Customer.first_name', array('label' => false))?></td>
			<th>Město</th>
			<td><?php echo $this->Form->input('CustomerForm.Customer.city', array('label' => false))?></td>
		</tr>
		<tr>
			<th>Email</th>
			<td><?php echo $this->Form->input('CustomerForm.Customer.email', array('label' => false))?></td>
			<th>RČ</th>
			<td><?php echo $this->Form->input('CustomerForm.Customer.birth_certificate_number', array('label' => false))?></td>
		</tr>
		<tr>
			<th>Titul před jménem</th>
			<td><?php echo $this->Form->input('CustomerForm.Customer.degree_before', array('label' => false))?></td>
			<th>Doporučující osoba</th>
			<td><?php echo $this->Form->input('CustomerForm.RecommendingCustomer.name', array('label' => false))?></td>
		</tr>
		<tr>
			<th>Titul za jménem</th>
			<td><?php echo $this->Form->input('CustomerForm.Customer.degree_after', array('label' => false))?></td>
			<td colspan="2">&nbsp;</td>
		</tr>
		<tr>
			<td colspan="4"><?php echo $html->link('reset filtru', array('controller' => 'customers', 'action' => 'index', 'reset' => 'customers') + $this->passedArgs) ?></td>
		</tr>
	</table>
	<?php
		echo $form->hidden('CustomerForm.Customer.search_form', array('value' => 1));
		echo $form->submit('Hledat');
		echo $form->end();
	?>
</div>