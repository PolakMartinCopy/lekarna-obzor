<h1>Nastavení</h1>

<?php echo $this->Form->create('Setting', array('action' => 'edit'))?>
<table class="left_heading">
	<tr>
		<th>Kurz pro odeslání upozornění</th>
		<td><?php echo $this->Form->input('Setting.rate', array('label' => false))?></td>
	</tr>
	<tr>
		<th>Telefonní číslo (O2)</th>
		<td><?php echo $this->Form->input('Setting.phone', array('label' => false))?></td>
	</tr>
</table>
<?php echo $this->Form->hidden('Setting.id')?>
<?php echo $this->Form->submit('upravit')?>
<?php echo $this->Form->end()?>