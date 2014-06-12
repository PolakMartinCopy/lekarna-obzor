<script>
	$(document).ready(function(){
		// autocomplete na jmeno a cislo zakaznika
		var data = <?php echo json_encode($customers)?>;
		$('#SaleCustomerName').each(function() {
			$(this).autocomplete({
				source: data, 
				select: function(event, ui) {
					var selectedObj = ui.item;
					$(this).val(selectedObj.label);
					$('#SaleCustomerId').val(selectedObj.value);

					$('#SaleCustomerBonus').val('');
					$('#SaleRecommendingCustomerBonus').val('');
					return false;
				}
			});
		});
		$('#SalePrice').change(function() {
			$('#SaleCustomerBonus').val('');
			$('#SaleRecommendingCustomerBonus').val('');
		});
	});

	// kalendar na datum u vlozeni prodeje
	$(function() {
		$( "#SaleDate" ).datepicker( $.datepicker.regional[ "cs" ] );
	});
</script>

<h1>Upravit prodej</h1>
<?php echo $this->Form->create('Sale', array('action' => 'edit', 'url' => $this->passedArgs))?>
<table class="left_heading">
	<tr>
		<th>Datum</th>
		<td><?php echo $this->Form->input('Sale.date', array('label' => false, 'type' => 'text'))?></td>
	</tr>
	<tr>
		<th>Cena</th>
		<td><?php echo $this->Form->input('Sale.price', array('label' => false))?></td>
	</tr>
	<tr>
		<th>Zákazník</th>
		<td>
			<?php echo $this->Form->input('Sale.customer_name', array('label' => false, 'type' => 'text'))?>
			<?php echo $this->Form->hidden('Sale.customer_id')?>
			<?php echo $this->Form->error('Sale.customer_id')?>
		</td>
	</tr>
	<tr>
		<th>Výše bonusu pro zákazníka</th>
		<td><?php echo $this->Form->input('Sale.customer_bonus', array('label' => false))?></td>
	</tr>
	<tr>
		<th>Výše bonusu pro doporučující osobu</th>
		<td><?php echo $this->Form->input('Sale.recommending_customer_bonus', array('label' => false))?></td>
	</tr>
	<tr>
		<th>Popis</th>
		<td><?php echo $this->Form->input('Sale.description', array('label' => false, 'cols' => 30, 'rows' => 3))?></td>
	</tr>
</table>
<?php echo $this->Form->hidden('Sale.id')?>
<?php echo $this->Form->hidden('Sale.action', array('value' => 'edit'))?>
<?php echo $this->Form->submit('Uložit prodej')?>
<?php echo $this->Form->end()?>