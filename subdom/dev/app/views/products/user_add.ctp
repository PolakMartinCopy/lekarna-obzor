<h1>Nové zboží</h1>
<?php echo $this->Form->create('Product')?>
<table class="left_heading">
	<tr>
		<th>Název</th>
		<td><?php echo $this->Form->input('Product.name', array('label' => false, 'size' => 70))?></td>
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
		<th>Jednotka</th>
		<td><?php echo $this->Form->input('Product.unit_id', array('label' => false))?></td>
	</tr>
	<tr>
		<th>Cena</th>
		<td><?php echo $this->Form->input('Product.price', array('label' => false, 'size' => 7, 'after' => '&nbsp;Kč'))?></td>
	</tr>
	<tr>
		<th>Marže</th>
		<td><?php echo $this->Form->input('Product.margin', array('label' => false, 'size' => 3, 'after' => '%'))?></td>
	</tr>
</table>
<?php echo $this->Form->hidden('Product.active', array('value' => true))?>
<?php echo $this->Form->submit('Uložit')?>
<?php echo $this->Form->end()?>