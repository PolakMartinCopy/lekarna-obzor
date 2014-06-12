<h2>Vložení nové šablony</h2>
<?=$form->create('MailTemplate');?>
	<fieldset>
 		<legend>Šablona</legend>
		<table class="leftHeading" cellpadding="5" cellspacing="3">
			<tr>
				<th>
					Předmět emailu
				</th>
				<td>
					<?=$form->input('MailTemplate.subject', array('label' => false, 'size' => 90))?>
				</td>
			</tr>
			<tr>
				<th>
					Obsah emailu
				</th>
				<td>
					<?=$form->input('MailTemplate.content', array('label' => false, 'cols' => 68, 'rows' => 15))?>
				</td>
			</tr>
		</table>
	</fieldset>
<?=$form->hidden('MailTemplate.id')?>
<?=$form->end('Uložit')?>