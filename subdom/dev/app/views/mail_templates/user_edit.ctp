<h2>Upravit šablonu</h2>
<ul>
	<li><?php echo $html->link('Zpět na seznam emailových šablon', array('controller' => 'mail_templates', 'action' => 'index'))?></li>
</ul>

<?=$form->create('MailTemplate', array('url' => array('controller' => 'mail_templates', 'action' => 'edit', $mail_template['MailTemplate']['id'])));?>
<table class="left_heading" cellpadding="5" cellspacing="3">
	<tr>
		<th>Popis šablony</th>
		<td><?php echo $form->input('MailTemplate.description', array('label' => false, 'size' => 90))?></td>
	</tr>
	<tr>
		<th>Předmět emailu</th>
		<td><?=$form->input('MailTemplate.subject', array('label' => false, 'size' => 90))?></td>
	</tr>
	<tr>
		<th>Obsah emailu</th>
		<td><?=$form->input('MailTemplate.content', array('label' => false, 'cols' => 68, 'rows' => 15))?></td>
	</tr>
</table>

<?=$form->hidden('MailTemplate.id')?>
<?=$form->end('Uložit')?>