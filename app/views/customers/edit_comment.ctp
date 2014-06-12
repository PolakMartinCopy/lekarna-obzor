<h2>Upravit komentář</h2>
<?=$html->link('Zpět na seznam komentářů', array('controller' => 'products', 'action' => 'view_comments', $product_id)) ?>
<?=$form->create('Comment', array('url' => array('controller' => 'customers', 'action' => 'edit_comment', $comment_id))) ?>
<table>
	<tr>
		<th>Předmět:</th>
		<td><?=$form->input('Comment.subject', array('label' => false)) ?></td>
	</tr>
	<tr>
		<th>Komentář:</th>
		<td><?=$form->input('Comment.body', array('label' => false)) ?></td>
	</tr>
</table>
<?=$form->hidden('Comment.confirmed', array('value' => 0)) ?>
<?=$form->end('Upravit') ?>
<?=$html->link('Zpět na seznam komentářů', array('controller' => 'products', 'action' => 'view_comments', $product_id)) ?>