<h1>Upravit jednotku zboží</h1>
<?php echo $this->Form->create('Unit')?>
<table class="left_heading">
	<tr>
		<th>Název</th>
		<td><?php echo $this->Form->input('Unit.name', array('label' => false))?></td>
	</tr>
	<tr>
		<th>Zkratka</th>
		<td><?php echo $this->Form->input('Unit.shortcut', array('label' => false))?></td>
	</tr>
</table>
<?php echo $this->Form->hidden('Unit.id')?>
<?php echo $this->Form->submit('Uložit')?>
<?php echo $this->Form->end()?>