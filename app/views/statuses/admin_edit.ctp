<h2>Editace stavu</h2>
<?php echo $form->Create('Status')?>
	<fieldset>
 		<legend>Status objednávky</legend>
		<table class="leftHeading" cellpadding="5" cellspacing="3">
			<tr>
				<th>
					Název statusu
				</th>
				<td>
					<?php echo $form->text('Status.name')?>
					<?php echo $form->error('Status.name')?>
				</td>
			</tr>
			<tr>

				<th>
					Barva statusu (RGB)
				</th>
				<td>
					<?php echo $form->text('Status.color')?>
				</td>
			</tr>
			<tr>
				<th>
					Předmět mailové šablony
				</th>
				<td>
					<?php echo $form->select('Status.mail_template_id', $mail_templates, null, array('label' => false))?>
				</td>
			</tr>
			<tr>
				<th>
					Závislá pole
				</th>
				<td>
					<?php echo $form->textarea('Status.requested_fields', array('label' => false))?>
				</td>
			</tr>
		</table>
	</fieldset>
<?
	echo $form->hidden('Status.id');
	echo $form->end('upravit')
?>
<div class="actions">
	<ul>
		<li><?php echo $html->link('Zpět na seznam statusů', array('action' => 'index'))?></li>
	</ul>
</div>