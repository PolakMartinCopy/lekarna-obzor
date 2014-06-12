<h1>Editace stavu</h1>
<?=$form->Create('Status')?>
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
			<?=$form->input('Status.closed', array('type' => 'select', 'label' => false, 'options' => array('0' => 'NE', '1' => 'ANO - úspěšně', 'ANO - neúspěšne')))?>
		</td>
	</tr>
	<tr>
		<th>
			Emailová šablona
		</th>
		<td>
			<?=$form->input('Status.mail_template_id', array('label' => false, 'empty' => ''))?>
		</td>
	</tr>
	<tr>
		<th>
			Závislá pole
		</th>
		<td>
			<?=$form->input('Status.requested_fields', array('label' => false, 'type' => 'textarea', 'cols' => 45))?>
		</td>
	</tr>
</table>
<?
	echo $form->hidden('Status.id');
	echo $form->end('upravit')
?>