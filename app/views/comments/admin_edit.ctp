<h2>Úprava komentáře</h2>

<?=$form->create('Comment', array('action' => 'edit')) ?>
<table class="leftHeading">
	<tr>
		<th>autor</th>
		<td><?=$form->input('Comment.author', array('label' => false)) ?></td>
	</tr>
	<tr>
		<th>předmět</th>
		<td><?=$form->input('Comment.subject', array('label' => false)) ?></td>
	</tr>
	<tr>
		<th>obsah</th>
		<td><?=$form->input('Comment.body', array('label' => false)) ?></td>
	</tr>
	<tr>
		<th>odpověď</th>
		<td><?=$form->input('Comment.reply', array('label' => false)) ?></td>
	</tr>
</table>
<?=$form->hidden('Comment.id') ?>
<?=$form->end('Upravit') ?>

<ul class="actions">
	<li><?=$html->link('zpět na seznam komentářů', array('controller' => 'comments', 'action' => 'admin_index')) ?></li>
</ul>
			