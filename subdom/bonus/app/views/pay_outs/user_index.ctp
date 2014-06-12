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

		$('#PayOutCustomerName').select();
	});

	// kalendar na datum u vlozeni prodeje
	$(function() {
		$( "#PayOutDate" ).datepicker( $.datepicker.regional[ "cs" ] );
	});
</script>

<h1>Výplaty</h1>
<?php echo $this->Form->create('PayOut', array('action' => 'add', 'url' => $this->passedArgs))?>
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
<?php echo $this->Form->submit('Vložit novou výplatu')?>
<?php echo $this->Form->end()?>

<?php echo $this->element('search_forms/pay_outs')?>

<?php if (empty($pay_outs)) { ?>
<p><em>V systému nejsou odpovídající výplaty.</em></p>
<?php } else { ?>
<table class="top_heading">
	<tr>
		<th><?php echo $this->Paginator->sort('Datum', 'PayOut.date')?></th>
		<th><?php echo $this->Paginator->sort('<abbr title="Zákaznické číslo">ZČ</abbr>', 'Customer.number', array('escape' => false))?></th>
		<th><?php echo $this->Paginator->sort('Příjmení', 'Customer.last_name')?></th>
		<th><?php echo $this->Paginator->sort('Jméno', 'Customer.first_name')?></th>
		<th><?php echo $this->Paginator->sort('Ulice + č.p.', 'Customer.street')?></th>
		<th><?php echo $this->Paginator->sort('PSČ', 'Customer.zip')?></th>
		<th><?php echo $this->Paginator->sort('Město', 'Customer.city')?></th>
		<th><?php echo $this->Paginator->sort('Doporučující', 'RecommendingCustomer.name')?></th>
		<th><?php echo $this->Paginator->sort('Výše slevy', 'Tariff.name')?></th>
		<th><?php echo $this->Paginator->sort('<abbr title="Výše vyplaceného bonusu">Výše výplaty</abbr>', 'PayOut.amount', array('escape' => false))?></th>
		<th><?php echo $this->Paginator->sort('<abbr title="Výše bonusu zákazníka">Stav účtu</abbr>', 'Customer.account', array('escape' => false))?></th>
		<th><?php echo $this->Paginator->sort('Vložil', 'User.last_name')?></th>
		<th colspan="5">&nbsp;</th>
	</tr>
	<?php foreach ($pay_outs as $pay_out) { ?>
	<tr>
		<td><?php echo cz_date($pay_out['PayOut']['date'])?></td>
		<td><?php echo $pay_out['Customer']['number']?></td>
		<td><?php echo $this->Html->link($pay_out['Customer']['last_name'], array('controller' => 'customers', 'action' => 'index', 'customer_number' => $pay_out['Customer']['number']))?></td>
		<td><?php echo $pay_out['Customer']['first_name']?></td>
		<td><?php echo $pay_out['Customer']['street']?></td>
		<td><?php echo $pay_out['Customer']['zip']?></td>
		<td><?php echo $pay_out['Customer']['city']?></td>
		<td><?php echo $this->Html->link($pay_out['RecommendingCustomer']['name'], array('controller' => 'customers', 'action' => 'index', 'customer_number' => $pay_out['RecommendingCustomer']['number']))?></td>
		<td><?php echo $pay_out['Tariff']['name']?></td>
		<td><?php echo $pay_out['PayOut']['amount']?></td>
		<td><?php echo $pay_out['Customer']['account']?></td>
		<td><?php echo $pay_out['User']['last_name']?></td>
		<td><?php echo $this->Html->link('<img src="/images/icons/acrobat-reader-32.png" alt="Dárkový poukaz"', array('controller' => 'pay_outs', 'action' => 'view_pdf', $pay_out['PayOut']['id']) + $this->passedArgs, array('escape' => false, 'title' => 'Zobrazit dárkový poukaz', 'target' => '_blank'))?></td>
		<td><?php echo $this->Html->link('<img src="/images/icons/shopping-cart-32.png" alt="Zadat prodej" />', array('controller' => 'sales', 'action' => 'index', 'customer_id' => $pay_out['Customer']['id']), array('escape' => false, 'title' => 'Zadat prodej'))?></td>
		<td><?php echo $this->Html->link('<img src="/images/icons/money-bag-32.png" alt="Vyplatit bonus" />', array('controller' => 'pay_outs', 'action' => 'index', 'customer_id' => $pay_out['Customer']['id']), array('escape' => false, 'title' => 'Vyplatit bonus'))?></td>
		<td><?php echo $this->Html->link('<img src="/images/icons/edit-32.png" alt="Upravit" />', array('controller' => 'pay_outs', 'action' => 'edit', $pay_out['PayOut']['id']) + $this->passedArgs, array('escape' => false, 'title' => 'Upravit'))?></td>
		<td><?php echo $this->Html->link('<img src="/images/icons/trash-32.png" alt="Smazat" />', array('controller' => 'pay_outs', 'action' => 'delete', $pay_out['PayOut']['id']) + $this->passedArgs, array('escape' => false, 'title' => 'Smazat'), 'Opravdu chcete záznam o výplatě odstranit?')?></td>
	</tr>
	<?php } ?>
</table>
<?php echo $this->Paginator->prev('« Předchozí', null, null, array('class' => 'disabled')); ?>
<!-- Shows the page numbers -->
<?php echo $this->Paginator->numbers(); ?>
<!-- Shows the next and previous links -->
<?php echo $this->Paginator->next('Další »', null, null, array('class' => 'disabled')); ?>
<p><?php echo $this->Html->link('Export výsledků do csv', array('controller' => 'pay_outs', 'action' => 'csv') + $this->passedArgs)?></p>
<?php } ?>