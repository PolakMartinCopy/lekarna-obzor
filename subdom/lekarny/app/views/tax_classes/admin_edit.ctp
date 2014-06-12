<h2>Editace daňové třídy</h2>
<?php echo $form->create('TaxClass');?>
<table class="left_headed" cellpadding="5" cellspacing="3">
	<tr>
		<th>
			Název daňové třídy
		</th>
		<td>
			<?=$form->text('name')?>
			<?=$form->error('name')?>
		</td>
	</tr>
	<tr>
		<th>
			Hodnota daně
		</th>
		<td>
			<?=$form->text('value')?>
			<?=$form->error('value')?>
		</td>
	</tr>
</table>
<?=$form->hidden('id')?>
<?=$form->end('Upravit');?>