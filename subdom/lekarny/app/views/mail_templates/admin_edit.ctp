<h2>Úprava šablony</h2>
<?=$form->create('MailTemplate');?>
<table class="left_headed" cellpadding="5" cellspacing="3">
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
<?=$form->hidden('MailTemplate.id')?>
<?=$form->end('Uložit')?>