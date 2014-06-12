<h1>Vložit nový atribut</h1>
<?php echo $form->create('Attribute');?>
<table class="left_headed" cellpadding="5" cellspacing="3">
	<tr>
		<th>Název atributu</th>
		<td>
			<?=$form->select('option_id', $options_options, array(), null, array(), false);?>
			<?=$form->error('option_id');?>
		</td>
	</tr>
	<tr>
		<th>Hodnota atributu</th>
		<td>
			<?=$form->text('Value.name', array());?>
			<?=$form->error('value_id');?>
			<?=$form->error('Value.name') ?>
		</td>
	</tr>
</table>
<?php echo $form->end('Vložit');?>