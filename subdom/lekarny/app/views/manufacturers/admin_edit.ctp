<h1>Editace výrobce</h1>
<?php echo $form->create('Manufacturer');?>
<table class="left_headed" cellpadding="5" cellspacing="3">
	<tr>
		<th>
			Název výrobce
		</th>
		<td>
			<?=$form->text('name')?>
			<?=$form->error('name')?>
		</td>
	</tr>
</table>
<?=$form->hidden('id')?>
<?=$form->end('Upravit');?>