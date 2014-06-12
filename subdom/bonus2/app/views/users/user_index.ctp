<h1>Seznam uživatelů</h1>
<?php echo $this->Form->create('User', array('action' => 'add'))?>
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
		<th>Heslo<sup>*</sup></th>
		<td><?php echo $this->Form->input('User.password', array('label' => false))?></td>
	</tr>
	<tr>
		<th>Admin?</th>
		<td><?php echo $this->Form->input('User.is_admin', array('label' => false))?></td>
	</tr>
</table>
<?php echo $this->Form->submit('Vložit nového uživatele')?>
<?php echo $this->Form->end()?>

<?php if (empty($users)) { ?>
<p><em>V systému nejsou žádní uživatelé.</em></p>
<?php } else { ?>
<table class="top_heading">
	<tr>
		<th>Jméno</th>
		<th>Login</th>
		<th>Admin?</th>
		<th>&nbsp;</th>
	</tr>
<?php 	foreach ($users as $user) { ?>
	<tr>
		<td><?php echo $user['User']['name']?></td>
		<td><?php echo $user['User']['login']?></td>
		<td><?php echo ($user['User']['is_admin'] ? 'Ano' : 'Ne')?></td>
		<td><?php echo $this->Html->link('Edit', array('controller' => 'users', 'action' => 'edit', $user['User']['id']))?></td>
	</tr>
<?php } ?>
</table>
<?php } ?>

<h1>Bonusové tarify</h1>
<?php echo $this->Form->create('Tariff', array('action' => 'add'))?>
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
<?php echo $this->Form->submit('Vložit nový bonus')?>
<?php echo $this->Form->end()?>

<?php if (empty($tariffs)) { ?>
<p><em>V systému nejsou  žádné bonusové tarify.</em></p>
<?php } else { ?>
<table class="top_heading">
	<tr>
		<th>Jméno tarifu</th>
		<th>Výše pro zákazníka</th>
		<th>Výše při doporučení</th>
		<th>&nbsp;</th>
	</tr>
	<?php foreach ($tariffs as $tariff) { ?>
	<tr>
		<td><?php echo $tariff['Tariff']['name']?></td>
		<td><?php echo $tariff['Tariff']['owner_amount']?> %</td>
		<td><?php echo $tariff['Tariff']['recommending_amount']?> %</td>
		<td><?php echo $this->Html->link('Edit', array('controller' => 'tariffs', 'action' => 'edit', $tariff['Tariff']['id']))?></td>
	</tr>
	<?php } ?>
</table>
<?php } ?>