<table class="left_heading">
	<tr>
		<th>Název</th>
		<td><?php echo $this->Form->input('Product.name', array('label' => false, 'size' => 70))?></td>
	</tr>
	<tr>
		<th>Název anglicky</th>
		<td><?php echo $this->Form->input('Product.en_name', array('label' => false, 'size' => 70))?></td>
	</tr>
	<tr>
		<th>Kód VZP</th>
		<td><?php echo $this->Form->input('Product.vzp_code', array('label' => false))?></td>
	</tr>
	<tr>
		<th>Kód skupiny</th>
		<td><?php echo $this->Form->input('Product.group_code', array('label' => false))?></td>
	</tr>
	<tr>
		<th>Referenční číslo</th>
		<td><?php echo $this->Form->input('Product.referential_number', array('label' => false))?></td>
	</tr>
	<tr>
		<th>Jednotka</th>
		<td><?php echo $this->Form->input('Product.unit_id', array('label' => false))?></td>
	</tr>
	<tr>
		<th>Daňová třída</th>
		<td><?php echo $this->Form->input('Product.tax_class_id', array('label' => false, 'options' => $tax_classes))?></td>
	</tr>
	<tr>
		<th>Marže</th>
		<td><?php echo $this->Form->input('ProductVariant.meavita_margin', array('label' => false, 'size' => 3, 'after' => '%'))?></td>
	</tr>
	<tr>
		<th>EXP</th>
		<td><?php echo $this->Form->input('ProductVariant.exp', array('label' => false))?></td>
	</tr>
	<tr>
		<th>LOT</th>
		<td><?php echo $this->Form->input('ProductVariant.lot', array('label' => false))?></td>
	</tr>
</table>