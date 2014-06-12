<h1>Přidat nový status</h1>
<?php echo $form->Create('Status')?>
<table class="left_headed" cellpadding="5" cellspacing="3">
	<tr>
		<th>
			Název statusu
		</th>
		<td>
			<?=$form->text('Status.name')?>
			<?=$form->error('Status.name')?>
		</td>
	</tr>
	<tr>

		<th>
			Barva statusu (RGB)
		</th>
		<td>
			<?=$form->text('Status.color')?>
		</td>
	</tr>
	<tr>
		<th>
			Uzavřenost
		</th>
		<td>
			<?=$form->input('Status.closed', array('type' => 'select', 'label' => false, 'options' => array('0' => 'NE', '1' => 'ANO')))?>
		</td>
	</tr>
</table>
<?
	echo $form->end('upravit')
?>