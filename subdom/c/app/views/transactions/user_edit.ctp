<script type="text/javascript">
	$(function() {
		var rowCount = $('.product_row').length;
		console.log(rowCount);
		var model = '<?php echo $model?>'; 
		
		$("#" + model + "Date").datepicker({
			changeMonth: false,
			numberOfMonths: 1,
		});

		$('#' + model + 'BusinessPartnerName').autocomplete({
			delay: 500,
			minLength: 2,
			source: '/user/business_partners/autocomplete_list',
			select: function(event, ui) {
				$('#' + model + 'BusinessPartnerName').val(ui.item.label);
				$('#' + model + 'BusinessPartnerId').val(ui.item.value);
				return false;
			}
		});

		$('table').delegate('.ProductsTransactionProductName', 'focusin', function() {
			if ($(this).is(':data(autocomplete)')) return;
			$(this).autocomplete({
				delay: 500,
				minLength: 2,
				source: '/user/products/autocomplete_list',
				select: function(event, ui) {
					var tableRow = $(this).closest('tr');
					var count = tableRow.attr('rel');
					$(this).val(ui.item.label);
					$('#ProductsTransaction' + count + 'ProductId').val(ui.item.value);
					return false;
				}
			});
		});
		
		$('table').delegate('.addRowButton', 'click', function(e) {
			e.preventDefault();
			// pridat radek s odpovidajicim indexem na konec tabulky s addRowButton
			var tableRow = $(this).closest('tr');
			tableRow.after(productRow(rowCount));
			// zvysim pocitadlo radku
			rowCount++;
		});

		$('table').delegate('.removeRowButton', 'click', function(e) {
			e.preventDefault();
			var tableRow = $(this).closest('tr');
			tableRow.remove();
		});
	});

	function productRow(count) {
		count++;
		var rowData = '<tr rel="' + count + '">';
		rowData += '<th>Zboží</th>';
		rowData += '<td>';
		rowData += '<input name="data[ProductsTransaction][' + count + '][product_name]" type="text" class="ProductsTransactionProductName" size="50" id="ProductsTransaction' + count + 'ProductName" />';
		rowData += '<input type="hidden" name="data[ProductsTransaction][' + count + '][product_id]" id="ProductsTransaction' + count + 'ProductId" />';
		rowData += '<input type="hidden" name="data[ProductsTransaction][' + count + '][subtract]" id="ProductsTransaction' + count + 'Subtract" value="<?php echo $transaction['TransactionType']['subtract']?>" />';
		rowData += '</td>';
		rowData += '<th>Množství</th>';
		rowData += '<td><input name="data[ProductsTransaction][' + count + '][quantity]" type="text" size="3" maxlength="10" id="ProductsTransaction' + count + 'Quantity" />';
		rowData += '</td>';
		rowData += '<td><a href="#" class="addRowButton">+</a>&nbsp;<a href="#" class="removeRowButton">-</a></td>';
		rowData += '</tr>';
		return rowData;
	}
</script>

<h1>Upravit transakci</h1>
<?php
$form_options = array();
if (isset($this->params['named']['business_partner_id'])) {
?>
<ul>
	<li><?php echo $this->Html->link('Zpět na detail obchodního partnera', array('controller' => 'business_partners', 'action' => 'view', $this->params['named']['business_partner_id']))?></li>
</ul>
<?php
	$form_options = array('url' => array('business_partner_id' => $this->params['named']['business_partner_id']));
}
echo $this->Form->create($model, $form_options)?>
<table class="left_heading">
	<tr>
		<th>Odběratel</th>
		<td colspan="4">
			<?php echo $this->Form->input($model . '.business_partner_name', array('label' => false, 'size' => 50))?>
			<?php echo $this->Form->error($model . '.business_partner_id')?>
			<?php echo $this->Form->hidden($model . '.business_partner_id')?>
		</td>
	</tr>
	<tr>
		<th>Datum</th>
		<td colspan="4">
			<?php echo $this->Form->input($model . '.date', array('label' => false, 'type' => 'text', 'div' => false))?>
			<?php echo $this->Form->input($model . '.time', array('label' => false, 'timeFormat' => '24', 'div' => false))?>
		</td>
	</tr>
	<?php if (empty($this->data['ProductsTransaction'])) { ?>
	<tr rel="0" class="product_row">
		<th>Zboží</th>
		<td>
			<?php echo $this->Form->input('ProductsTransaction.0.product_name', array('label' => false, 'class' => 'ProductsTransactionProductName', 'size' => 50))?>
			<?php echo $this->Form->error('ProductsTransaction.0.product_id')?>
			<?php echo $this->Form->hidden('ProductsTransaction.0.product_id')?>
			<?php echo $this->Form->hidden('ProductsTransaction.0.subtract')?>
		</td>
		<th>Množství</th>
		<td><?php echo $this->Form->input('ProductsTransaction.0.quantity', array('label' => false, 'size' => 3))?></td>
		<td><a href="#" class="addRowButton">+</a>&nbsp;<a href="#" class="removeRowButton">-</a></td>
	</tr>
	<?php } else { ?>
	<?php 	foreach ($this->data['ProductsTransaction'] as $index => $data) { ?>
	<tr rel="<?php echo $index?>" class="product_row">
		<th>Zboží</th>
		<td>
			<?php echo $this->Form->input('ProductsTransaction.' . $index . '.product_name', array('label' => false, 'class' => 'ProductsTransactionProductName', 'size' => 50))?>
			<?php echo $this->Form->error('ProductsTransaction.' . $index . '.product_id')?>
			<?php echo $this->Form->hidden('ProductsTransaction.' . $index . '.product_id')?>
			<?php echo $this->Form->hidden('ProductsTransaction.' . $index . '.subtract')?>
		</td>
		<th>Množství</th>
		<td><?php echo $this->Form->input('ProductsTransaction.' . $index . '.quantity', array('label' => false, 'size' => 3))?></td>
		<td><a href="#" class="addRowButton">+</a>&nbsp;<a href="#" class="removeRowButton">-</a></td>
	</tr>
	<?php } ?>
	<?php } ?>
</table>
<?php 
	echo $this->Form->hidden($model . '.transaction_type_id');
	echo $this->Form->hidden($model . '.id');
	echo $this->Form->submit('Uložit');
	echo $this->Form->end()
?>

<?php if (isset($this->params['named']['business_partner_id'])) { ?>
<ul>
	<li><?php echo $this->Html->link('Zpět na detail obchodního partnera', array('controller' => 'business_partners', 'action' => 'view', $this->params['named']['business_partner_id']))?></li>
</ul>
<?php }?>