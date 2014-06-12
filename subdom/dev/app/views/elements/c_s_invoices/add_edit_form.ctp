<table class="left_heading">
	<tr>
		<th>Komu:</th>
		<td colspan="4"><?php 
			if (isset($business_partner)) {
				echo $this->Form->input('CSInvoice.business_partner_name', array('label' => false, 'size' => 50, 'disabled' => true));
			} else {
				echo $this->Form->input('CSInvoice.business_partner_name', array('label' => false, 'size' => 50));
				echo $this->Form->error('CSInvoice.business_partner_id');
			}
			echo $this->Form->hidden('CSInvoice.business_partner_id')
		?></td>
	</tr>
	<tr>
		<th>Datum splatnosti</th>
		<td colspan="4">
			<?php echo $this->Form->input('CSInvoice.due_date', array('label' => false, 'type' => 'text', 'div' => false))?>
		</td>
	</tr>
	<tr>
		<th>Číslo objednávky</th>
		<td><?php echo $this->Form->input('CSInvoice.order_number', array('label' => false))?></td>
	</tr>
	<tr>
		<th>Jazyk</th>
		<td><?php echo $this->Form->input('CSInvoice.language_id', array('label' => false))?></td>
	</tr>
	<tr>
		<th>Měna</th>
		<td><?php echo $this->Form->input('CSInvoice.currency_id', array('label' => false))?></td>
	</tr>
	<tr>
		<th>Poznámka</th>
		<td><?php echo $this->Form->input('CSInvoice.note', array('label' => false, 'cols' => 60, 'rows' => 5))?></td>
	</tr>
</table>
<h2>Položky</h2>
<table class="top_heading">
	<tr>
		<th>Zboží</th>
		<th>Popis</th>
		<th>Množství</th>
		<th>Cena<br/><small>bez DPH</small></th>
		<th>Cena<br/><small>s DPH</small></th>
		<th>&nbsp;</th>
	</tr>
<?php if (empty($this->data['CSTransactionItem'])) { ?>
	<tr rel="1" class="product_row">
		<td><?php
			echo $this->Form->input('CSTransactionItem.1.product_name', array('label' => false, 'size' => 50, 'class' => 'CSTransactionItemProductName'));
			echo $this->Form->error('CSTransactionItem.1.product_variant_id');
			echo $this->Form->hidden('CSTransactionItem.1.product_variant_id')
		?></td>
		<td><?php echo $this->Form->input('CSTransactionItem.1.description', array('label' => false, 'size' => 50))?></td>
		<td><?php echo $this->Form->input('CSTransactionItem.1.quantity', array('label' => false, 'size' => 2))?></td>
		<td><?php echo $this->Form->input('CSTransactionItem.1.price', array('label' => false, 'size' => 5, 'class' => 'price'))?></td>
		<td><?php echo $this->Form->input('CSTransactionItem.1.price_vat', array('label' => false, 'size' => 5))?></td>
		<td>
			<a class="addRowButton" href="#">+</a>&nbsp;<a class="removeRowButton" href="#">-</a>
		</td>
	</tr>
<?php } else { ?>
<?php 	foreach ($this->data['CSTransactionItem'] as $index => $data) { ?>
	<tr rel="<?php echo $index?>" class="product_row">
		<td>
			<?php echo $this->Form->input('CSTransactionItem.' . $index . '.product_name', array('label' => false, 'size' => 50, 'class' => 'CSTransactionItemProductName'))?>
			<?php echo $this->Form->error('CSTransactionItem.' . $index . '.product_variant_id')?>
			<?php echo $this->Form->hidden('CSTransactionItem.' . $index . '.product_variant_id')?>
		</td>
		<td><?php echo $this->Form->input('CSTransactionItem.' . $index . '.description', array('label' => false, 'size' => 50))?></td>
		<td><?php echo $this->Form->input('CSTransactionItem.' . $index . '.quantity', array('label' => false, 'size' => 2))?></td>
		<td><?php echo $this->Form->input('CSTransactionItem.' . $index . '.price', array('label' => false, 'size' => 5, 'class' => 'price'))?></td>
		<td><?php echo $this->Form->input('CSTransactionItem.' . $index . '.price_vat', array('label' => false, 'size' => 5))?></td>
		<td>
			<a class="addRowButton" href="#">+</a>&nbsp;<a class="removeRowButton" href="#">-</a>
		</td>
	</tr>
<?php 	}?>
<?php }?>
</table>