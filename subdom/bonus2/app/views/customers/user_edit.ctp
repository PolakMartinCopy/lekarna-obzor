<script>
	$(document).ready(function(){
		var data = <?php echo json_encode($recommending_customers)?>;
		$('#CustomerRecommendingCustomerName').each(function() {
			var autoCompleteElement = this;
			var hiddenElementID  = 'CustomerRecommendingCustomerId';
			$(this).autocomplete({
				source: data, 
				select: function(event, ui) {
					var selectedObj = ui.item;
					$(autoCompleteElement).val(selectedObj.label);
					$('#'+hiddenElementID).val(selectedObj.value);
					return false;
				}
			});
		});
	});
</script>

<h1>Upravit zákazníka <?php echo $customer['Customer']['name']?></h1>

<?php echo $this->Form->create('Customer', array('action' => 'edit'))?>
<table class="left_heading">
	<tr>
		<th>Zákaznické číslo<sup>*</sup></th>
		<td><?php echo $this->Form->input('Customer.number', array('label' => false))?></td>
	</tr>
	<tr>
		<th>Příjmení<sup>*</sup></th>
		<td><?php echo $this->Form->input('Customer.last_name', array('label' => false))?></td>
	</tr>
	<tr>
		<th>Křestní jméno<sup>*</sup></th>
		<td><?php echo $this->Form->input('Customer.first_name', array('label' => false))?></td>
	</tr>
	<tr>
		<th>Email</th>
		<td><?php echo $this->Form->input('Customer.email', array('label' => false))?></td>
	</tr>
	<tr>
		<th>Titul před jménem</th>
		<td><?php echo $this->Form->input('Customer.degree_before', array('label' => false))?></td>
	</tr>
	<tr>
		<th>Titul za jménem</th>
		<td><?php echo $this->Form->input('Customer.degree_after', array('label' => false))?></td>
	</tr>
	<tr>
		<th>Oslovení</th>
		<td><?php echo $this->Form->input('Customer.salutation', array('label' => false))?></td>
	</tr>
	<tr>
		<th>Pohlaví<sup>*</sup></th>
		<td><?php echo $this->Form->input('Customer.sex', array('label' => false, 'type' => 'radio', 'options' => array('muž' => 'muž', 'žena' => 'žena'), 'legend' => false, 'default' => 'muž'))?></td>
	</tr>
	<tr>
		<th>Ulice + č.p.<sup>*</sup></th>
		<td><?php echo $this->Form->input('Customer.street', array('label' => false))?></td>
	</tr>
	<tr>
		<th>PSČ<sup>*</sup></th>
		<td><?php echo $this->Form->input('Customer.zip', array('label' => false))?></td>
	</tr>
	<tr>
		<th>Město<sup>*</sup></th>
		<td><?php echo $this->Form->input('Customer.city', array('label' => false))?></td>
	</tr>
	<tr>
		<th>RČ<sup>*</sup></th>
		<td><?php echo $this->Form->input('Customer.birth_certificate_number', array('label' => false))?></td>
	</tr>
	<tr>
		<th>Doporučující osoba</th>
		<td>
			<?php echo $this->Form->input('Customer.recommending_customer_name', array('label' => false, 'type' => 'text', 'disabled' => !empty($customer['Sale'])))?>
			<?php echo $this->Form->hidden('Customer.recommending_customer_id')?>
		</td>
	</tr>
	<tr>
		<th>Kategorie bonusu</th>
		<td><?php echo $this->Form->input('Customer.tariff_id', array('label' => false, 'type' => 'select', 'options' => $tariffs, 'empty' => false))?></td>
	</tr>
</table>
<?php echo $this->Form->hidden('Customer.id')?>
<?php echo $this->Form->submit('Uložit')?>
<?php echo $this->Form->end()?>