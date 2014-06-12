<h1>Upravit uživatele</h1>

<?php echo $this->Form->create('User', array('action' => 'edit'))?>
<table class="left_heading">
	<tr>
		<th>Křestní jméno</th>
		<td><?php echo $this->Form->input('User.first_name', array('label' => false))?></td>
	</tr>
	<tr>
		<th>Příjmení</th>
		<td><?php echo $this->Form->input('User.last_name', array('label' => false))?></td>
	</tr>
	<tr>
		<th>Login<sup>*</sup></th>
		<td><?php echo $this->Form->input('User.login', array('label' => false))?></td>
	</tr>
	<tr>
		<th>Heslo</th>
		<td><?php echo $this->Form->input('User.password', array('label' => false))?></td>
	</tr>
	<tr>
		<th>Admin?</th>
		<td><?php echo $this->Form->input('User.is_admin', array('label' => false))?></td>
	</tr>
</table>
<?php echo $this->Form->hidden('User.id')?>
<?php echo $this->Form->submit('Upravit uživatele')?>
<?php echo $this->Form->end()?>