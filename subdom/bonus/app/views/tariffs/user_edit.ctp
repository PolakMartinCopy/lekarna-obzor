<h1>Upravit tarif</h1>

<?php echo $this->Form->create('Tariff', array('action' => 'edit'))?>
<table class="left_heading">
	<tr>
		<th>Jméno bonusu<sup>*</sup></th>
		<td><?php echo $this->Form->input('Tariff.name', array('label' => false))?></td>
	</tr>
	<tr>
		<th>Výše pro zákazníka<sup>*</sup></th>
		<td><?php echo $this->Form->input('Tariff.owner_amount', array('label' => false, 'size' => 2, 'after' => '%'))?></td>
	</tr>
	<tr>
		<th>Výše při doporučení<sup>*</sup></th>
		<td><?php echo $this->Form->input('Tariff.recommending_amount', array('label' => false, 'size' => 2, 'after' => '%'))?></td>
	</tr>
</table>
<?php echo $this->Form->hidden('Tariff.id')?>
<?php echo $this->Form->submit('Upravit tarif')?>
<?php echo $this->Form->end()?>