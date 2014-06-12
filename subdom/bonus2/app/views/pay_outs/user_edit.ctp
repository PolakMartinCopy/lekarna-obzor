<script>
	$(document).ready(function(){
		// autocomplete na jmeno a cislo zakaznika
		var data = <?php echo json_encode($customers)?>;
		$('#PayOutCustomerName').each(function() {
			var autoCompleteElement = this;
			var hiddenElementID  = 'PayOutCustomerId';
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

	// kalendar na datum u vlozeni prodeje
	$(function() {
		$( "#PayOutDate" ).datepicker( $.datepicker.regional[ "cs" ] );
	});
</script>

<?php echo $this->Form->create('PayOut', array('action' => 'edit', 'url' => $this->passedArgs))?>
<table class="left_heading">
	<tr>
		<th>Datum</th>
		<td><?php echo $this->Form->input('PayOut.date', array('label' => false, 'type' => 'text'))?></td>
	</tr>
	<tr>
		<th>Zákazník</th>
		<td>
			<?php echo $this->Form->input('PayOut.customer_name', array('label' => false, 'type' => 'text', 'error' => false))?>
			<?php echo $this->Form->hidden('PayOut.customer_id')?>
			<?php echo $this->Form->error('PayOut.customer_id')?>
		</td>
	</tr>
	<tr>
		<th>Výše vyplacené částky</th>
		<td><?php echo $this->Form->input('PayOut.amount', array('label' => false))?></td>
	</tr>
</table>
<?php echo $this->Form->hidden('PayOut.id')?>
<?php echo $this->Form->submit('Uložit výplatu')?>
<?php echo $this->Form->end()?>