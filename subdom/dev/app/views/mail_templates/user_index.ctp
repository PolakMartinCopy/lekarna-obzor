<h1>Emailové šablony</h1>

<?php if (empty($mail_templates)) { ?>
<p><em>V databázi nejsou žádné emailové šablony.</em></p>
<?php } else { ?>
<div class="actions">
	<ul>
		<li><?=$html->link('přidat šablonu', array('controller' => 'mail_templates', 'action' => 'add'))?></li>
	</ul>
</div>

<table class="top_heading" cellpadding="5" cellspacing="3">
	<tr>
		<th>ID</th>
		<th>Popis</th>
		<th>&nbsp;</th>
	</tr>
<? foreach ( $mail_templates as $mail_template ){ ?>
		<tr>
			<td><?php echo $mail_template['MailTemplate']['id'] ?></td>
			
			<td><?php echo $mail_template['MailTemplate']['description'] ?></td>
			<td>
				<?php echo $html->link('upravit', array('controller' => 'mail_templates', 'action' => 'edit', $mail_template['MailTemplate']['id'])) ?>
				<?php echo $html->link('smazat', array('controller' => 'mail_templates', 'action' => 'del', $mail_template['MailTemplate']['id']), array(), 'Opravdu chcete tuto šablonu smazat?') ?>
			</td>
		</tr>
<?php 	}
} ?>
</table>
<div class="actions">
	<ul>
		<li><?=$html->link('přidat šablonu', array('controller' => 'mail_templates', 'action' => 'add'))?></li>
	</ul>
</div>