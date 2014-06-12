<table class="left_heading">
	<tr>
		<th>Datum</th>
		<td>
			<?php echo $this->Form->input('CSStoring.date', array('label' => false, 'type' => 'text', 'div' => false))?>
			<?php echo $this->Form->input('CSStoring.time', array('label' => false, 'timeFormat' => '24', 'div' => false))?>
		</td>
	</tr>
	<tr>
		<th>Poznámka</th>
		<td><?php echo $this->Form->input('CSStoring.note', array('label' => false, 'cols' => 60, 'rows' => 5))?></td>
	</tr>
</table>
<h2>Položky</h2>
<table class="top_heading">
	<tr>
		<th>Zboží</th>
		<th>Popis</th>
		<th>Dodavatel</th>
		<th>Množství</th>
		<th>Nákupní cena<br/><small>(bez DPH)</small></th>
		<th>Měna</th>
		<th>Kurz</th>
		<th>&nbsp;</th>
	</tr>
<?php
	$img = '<img src="/images/icons/add.png" alt="Novy" />';
	if (empty($this->data['CSTransactionItem'])) {
?>
	<tr rel="1" class="product_row">
		<td nowrap><?php
			echo $this->Form->input('CSTransactionItem.1.product_name', array('label' => false, 'size' => 30, 'class' => 'CSTransactionItemProductName', 'div' => false));
			echo $this->Html->link($img, '#new_product_form', array('class' => 'new_product_link', 'escape' => false));
			echo $this->Form->error('CSTransactionItem.1.product_variant_id', array('div' => false));
			echo $this->Form->hidden('CSTransactionItem.1.product_variant_id');
		?></td>
		<td><?php echo $this->Form->input('CSTransactionItem.1.description', array('label' => false, 'size' => 25))?></td>
		<td><?php
			echo $this->Form->input('CSTransactionItem.1.business_partner_name', array('label' => false, 'size' => 30, 'class' => 'CSTransactionItemBusinessPartnerName'));
			echo $this->Form->error('CSTransactionItem.1.business_partner_id');
			echo $this->Form->hidden('CSTransactionItem.1.business_partner_id');
		?></td>
		<td><?php echo $this->Form->input('CSTransactionItem.1.quantity', array('label' => false, 'size' => 2))?></td>
		<td><?php echo $this->Form->input('CSTransactionItem.1.price', array('label' => false, 'size' => 5))?></td>
		<td><?php echo $this->Form->input('CSTransactionItem.1.currency_id', array('label' => false, 'options' => $currencies))?></td>
		<td><?php echo $this->Form->input('CSTransactionItem.1.exchange_rate', array('label' => false, 'size' => 3, 'value' => 1))?></td>
		<td>
			<a class="addRowButton" href="#">+</a>&nbsp;<a class="removeRowButton" href="#">-</a>
		</td>
	</tr>
<?php } else { ?>
<?php 	foreach ($this->data['CSTransactionItem'] as $index => $data) { ?>
	<tr rel="<?php echo $index?>" class="product_row">
		<td nowrap><?php
			echo $this->Form->input('CSTransactionItem.' . $index . '.product_name', array('label' => false, 'size' => 30, 'class' => 'CSTransactionItemProductName', 'div' => false));
			echo $this->Html->link($img, '#new_product_form', array('class' => 'new_product_link', 'escape' => false));
			echo $this->Form->error('CSTransactionItem.' . $index . '.product_variant_id', array('div' => false));
			echo $this->Form->hidden('CSTransactionItem.' . $index . '.product_variant_id');
		?></td>
		<td><?php echo $this->Form->input('CSTransactionItem.' . $index . '.description', array('label' => false, 'size' => 25))?></td>
		<td><?php
			echo $this->Form->input('CSTransactionItem.' . $index . '.business_partner_name', array('label' => false, 'size' => 30, 'class' => 'CSTransactionItemBusinessPartnerName'));
			echo $this->Form->error('CSTransactionItem.' . $index . '.business_partner_id');
			echo $this->Form->hidden('CSTransactionItem.' . $index . '.business_partner_id');
		?></td>
		<td><?php echo $this->Form->input('CSTransactionItem.' . $index . '.quantity', array('label' => false, 'size' => 2, 'after' => 'ks'))?></td>
		<td><?php echo $this->Form->input('CSTransactionItem.' . $index . '.price', array('label' => false, 'size' => 5, 'after' => 'Kč'))?></td>
		<td><?php echo $this->Form->input('CSTransactionItem.' . $index . '.currency_id', array('label' => false, 'options' => $currencies))?></td>
		<td><?php echo $this->Form->input('CSTransactionItem.' . $index . '.exchange_rate', array('label' => false, 'size' => 3))?></td>
		<td>
			<a class="addRowButton" href="#">+</a>&nbsp;<a class="removeRowButton" href="#">-</a>
		</td>
	</tr>
<?php 	}?>
<?php }?>
</table>