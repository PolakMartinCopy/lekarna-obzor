<h1>Editace názvu</h1>
<?php echo $form->create('Option');?>
<table class="left_headed" cellpadding="5" cellspacing="3">
	<tr>
		<th>
			Název atributu
		</th>
		<td>
			<?=$form->text('name')?>
		</td>
	</tr>
</table>
<?=$form->hidden('id')?>
<?=$form->end('Upravit');?>