<h2>Filtr výplat</h2>
<div id="search_form">
	<?php echo $this->Form->create('PayOut', array('url' => array('controller' => 'pay_outs', 'action' => 'index') + $this->passedArgs)); ?>
	<table class="left_heading">
		<tr>
			<th>Zákaznické číslo</th>
			<td><?php echo $this->Form->input('PayOutForm.Customer.number', array('label' => false))?></td>
			<th>Ulice + č. p.</th>
			<td><?php echo $this->Form->input('PayOutForm.Customer.street', array('label' => false))?></td>
		</tr>
		<tr>
			<th>Příjmení</th>
			<td><?php echo $this->Form->input('PayOutForm.Customer.last_name', array('label' => false))?></td>
			<th>PSČ</th>
			<td><?php echo $this->Form->input('PayOutForm.Customer.zip', array('label' => false))?></td>
		</tr>
		<tr>
			<th>Jméno</th>
			<td><?php echo $this->Form->input('PayOutForm.Customer.first_name', array('label' => false))?></td>
			<th>Město</th>
			<td><?php echo $this->Form->input('PayOutForm.Customer.city', array('label' => false))?></td>
		</tr>
		<tr>
			<th>Doporučující osoba</th>
			<td><?php echo $this->Form->input('PayOutForm.RecommendingCustomer.name', array('label' => false))?></td>
			<td colspan="2">&nbsp;</td>
		</tr>
		<tr>
			<td colspan="4"><?php echo $html->link('reset filtru', array('controller' => 'pay_outs', 'action' => 'index', 'reset' => 'pay_outs') + $this->passedArgs) ?></td>
		</tr>
	</table>
	<?php
		echo $form->hidden('PayOutForm.PayOut.search_form', array('value' => 1));
		echo $form->submit('Hledat');
		echo $form->end();
	?>
</div>