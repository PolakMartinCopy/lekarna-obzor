<script>
	$(document).ready(function(){
		// autocomplete na jmeno a cislo zakaznika
		var data = <?php echo json_encode($customers)?>;
		$('#SaleCustomerName').each(function() {
			var autoCompleteElement = this;
			var hiddenElementID  = 'SaleCustomerId';
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

		var href = location.href;
		if (href.match(/#search/)) {
			$('#SaleFormCustomerNumber').select();
		} else {
			$('#SalePrice').select();
		}
	});

	// kalendar na datum u vlozeni prodeje
	$(function() {
		$( "#SaleDate" ).datepicker( $.datepicker.regional[ "cs" ] );
	});
</script>

<h1>Prodeje</h1>
<?php echo $this->Form->create('Sale', array('action' => 'add', 'url' => $this->passedArgs))?>
<table class="left_heading">
	<tr>
		<th>Datum</th>
		<td><?php echo $this->Form->input('Sale.date', array('label' => false, 'type' => 'text'))?></td>
	</tr>
	<tr>
		<th>Zákazník</th>
		<td>
			<?php echo $this->Form->input('Sale.customer_name', array('label' => false, 'type' => 'text', 'error' => false, 'size' => 40))?>
			<?php echo $this->Form->hidden('Sale.customer_id')?>
			<?php echo $this->Form->error('Sale.customer_id')?>
		</td>
	</tr>
	<tr>
		<th>Cena nákupu</th>
		<td><?php echo $this->Form->input('Sale.price', array('label' => false))?></td>
	</tr>
	<tr>
		<th>Bonus pro zákazníka</th>
		<td>
			<?php echo $this->Form->input('Sale.customer_bonus', array('label' => false, 'after' => '%', 'size' => 5, 'div' => false))?>
			<?php echo $this->Form->input('Sale.customer_bonus_amount', array('label' => false, 'after' => 'Kč', 'size' => 5, 'div' => false))?>
		</td>
	</tr>
	<tr>
		<th>Bonus doporučující osoby</th>
		<td>
			<?php echo $this->Form->input('Sale.recommending_customer_bonus', array('label' => false, 'after' => '%', 'size' => 5, 'div' => false))?>
			<?php echo $this->Form->input('Sale.recommending_customer_bonus_amount', array('label' => false, 'after' => 'Kč', 'size' => 5, 'div' => false))?>
		</td>
	</tr>
	<tr>
		<th>Popis</th>
		<td><?php echo $this->Form->input('Sale.description', array('label' => false, 'cols' => 30, 'rows' => 3))?></td>
	</tr>
</table>
<?php echo $this->Form->submit('Vložit nový prodej')?>
<?php echo $this->Form->end()?>

<?php echo $this->element('search_forms/sales')?>

<?php if (empty($sales)) { ?>
<p><em>V systému nejsou odpovídající prodeje.</em></p>
<?php } else { ?>
<table class="top_heading">
	<tr>
		<th><?php echo $this->Paginator->sort('Datum', 'Sale.date')?></th>
		<th><?php echo $this->Paginator->sort('<abbr title="Zákaznické číslo">ZČ</abbr>', 'Customer.number', array('escape' => false))?></th>
		<th><?php echo $this->Paginator->sort('Příjmení', 'Customer.last_name')?></th>
		<th><?php echo $this->Paginator->sort('Jméno', 'Customer.first_name')?></th>
		<th><?php echo $this->Paginator->sort('Ulice + č.p.', 'Customer.street')?></th>
		<th><?php echo $this->Paginator->sort('PSČ', 'Customer.zip')?></th>
		<th><?php echo $this->Paginator->sort('Město', 'Customer.city')?></th>
		<th><?php echo $this->Paginator->sort('Doporučující', 'RecommendingCustomer.name')?></th>
		<th><?php echo $this->Paginator->sort('Výše slevy', 'Tariff.name')?></th>
		<th><?php echo $this->Paginator->sort('Cena nákupu', 'Sale.price')?></th>
		<th><?php echo $this->Paginator->sort('<abbr title="Bonus zákazníka z prodeje">Bonus zák.</abbr>', 'Sale.customer_bonus', array('escape' => false))?></th>
		<th><?php echo $this->Paginator->sort('<abbr title="Bonus doporučujícího">Bonus dop.</abbr>', 'Sale.recommending_customer_bonus', array('escape' => false))?></th>
		<th><?php echo $this->Paginator->sort('<abbr title="Výše bonusu zákazníka">Stav účtu</abbr>', 'Customer.account', array('escape' => false))?></th>
		<th><?php echo $this->Paginator->sort('Vložil', 'User.last_name')?></th>
		<th colspan="4">&nbsp;</th>
	</tr>
	<?php foreach ($sales as $sale) { ?>
	<tr>
		<td><?php echo cz_date($sale['Sale']['date'])?></td>
		<td><?php echo $sale['Customer']['number']?></td>
		<td><?php echo $this->Html->link($sale['Customer']['last_name'], array('controller' => 'customers', 'action' => 'index', 'customer_number' => $sale['Customer']['number']))?></td>
		<td><?php echo $sale['Customer']['first_name']?></td>
		<td><?php echo $sale['Customer']['street']?></td>
		<td><?php echo $sale['Customer']['zip']?></td>
		<td><?php echo $sale['Customer']['city']?></td>
		<td><?php echo $this->Html->link($sale['RecommendingCustomer']['name'], array('controller' => 'customers', 'action' => 'index', 'customer_number' => $sale['RecommendingCustomer']['number']))?></td>
		<td><?php echo $sale['Tariff']['name']?></td>
		<td><?php echo $sale['Sale']['price']?></td>
		<td><?php echo $sale['Sale']['customer_bonus']?></td>
		<td><?php echo $sale['Sale']['recommending_customer_bonus']?></td>
		<td><?php echo $sale['Customer']['account']?></td>
		<td><?php echo $sale['User']['last_name']?></td>
		<td><?php echo $this->Html->link('<img src="/images/icons/shopping-cart-32.png" alt="Zadat prodej" />', array('controller' => 'sales', 'action' => 'index', 'customer_id' => $sale['Customer']['id']), array('escape' => false, 'title' => 'Zadat prodej'))?></td>
		<td><?php echo $this->Html->link('<img src="/images/icons/money-bag-32.png" alt="Vyplatit bonus" />', array('controller' => 'pay_outs', 'action' => 'index', 'customer_id' => $sale['Customer']['id']), array('escape' => false, 'title' => 'Vyplatit bonus'))?></td>
		<td><?php echo $this->Html->link('<img src="/images/icons/edit-32.png" alt="Upravit" />', array('controller' => 'sales', 'action' => 'edit', $sale['Sale']['id']) + $this->passedArgs, array('escape' => false, 'title' => 'Upravit'))?></td>
		<td><?php echo $this->Html->link('<img src="/images/icons/trash-32.png" alt="Smazat" />', array('controller' => 'sales', 'action' => 'delete', $sale['Sale']['id']) + $this->passedArgs, array('escape' => false, 'title' => 'Smazat'), 'Opravdu chcete záznam o prodeji odstranit?')?></td>
	</tr>
	<?php } ?>
</table>

<?php echo $this->Paginator->prev('« Předchozí', null, null, array('class' => 'disabled')); ?>
<!-- Shows the page numbers -->
<?php echo $this->Paginator->numbers(); ?>
<!-- Shows the next and previous links -->
<?php echo $this->Paginator->next('Další »', null, null, array('class' => 'disabled')); ?>
<p><?php echo $this->Html->link('Export výsledků do csv', array('controller' => 'sales', 'action' => 'csv') + $this->passedArgs)?></p>
<?php } ?>