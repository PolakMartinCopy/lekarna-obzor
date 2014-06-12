<h1>Editace atributu</h1>
<? echo $form->create('Attribute', array('url' => '/admin/attributes/edit/' . $id));?>
	<table class="left_headed" cellpadding="5" cellspacing="3">
		<tr>
			<th>Název atributu</th>
			<td>
				<?=$form->select('Attribute.option_id', $options_options, null, array('disabled' => 1))?>
				<?=$form->hidden('Attribute.option_id')?>
			</td>
		</tr>
		<tr>
			<th>Hodnota atributu</th>
			<td>
				<?=$form->text('Value.name')?>
				<?=$form->error('value_id');?>
				<?=$form->error('Value.name') ?>	
			</td>
		</tr>
	</table>
	<?
		echo $form->input('Attribute.id');
		echo $form->hidden('Value.id');
		echo $form->end('Upravit');
	?>