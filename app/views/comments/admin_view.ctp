<?//debug($comment) ?>

<h2>Administrace komentáře</h2>
<table class="leftHeading">
	<tr>
		<th>vytvořen</th>
		<td><?=$comment['Comment']['created'] ?></td>
	</tr>
	<tr>
		<th>posl. úprava</th>
		<td><?=$comment['Comment']['modified'] ?></td>
	</tr>
	<tr>
		<th>autor</th>
		<td><?=$comment['Comment']['author'] ?></td>
	</tr>
	<tr>
		<th>k produktu</th>
		<td><?=$html->link($comment['Product']['name'], '/' . $comment['Product']['url']) ?></td>
	</tr>
	<tr>
		<th>předmět</th>
		<td><?=$comment['Comment']['subject'] ?></td>
	</tr>
	<tr>
		<th>obsah</th>
		<td><?=$comment['Comment']['body'] ?></td>
	</tr>
	<tr>
		<th>odpověď</th>
		<td><?=$comment['Comment']['reply'] ?></td>
	</tr>
	<tr>
		<th>
			schválen
		</th>
		<td>
			<?=( $comment['Comment']['confirmed'] == '1' ? '<strong style="color:green">ANO</strong>' : '<strong style="color:red">NE</strong>')  ?>
</table>
<ul class="actions">
	<li>
		<?
			if ($comment['Comment']['confirmed'] == '1') {
				echo $html->link('zakázat', array('controller' => 'comments', 'action' => 'admin_unconfirm', $comment['Comment']['id']));
			} else {
				echo $html->link('schválit', array('controller' => 'comments', 'action' => 'admin_confirm', $comment['Comment']['id']));
			}
		?>
	</li>
	<li>
		<?=$html->link('editovat', array('controller' => 'comments', 'action' => 'admin_edit', $comment['Comment']['id'])) ?>
	</li>
	<li>
		<?=$html->link('smazat', array('controller' => 'comments', 'action' => 'admin_delete', $comment['Comment']['id']), array(), 'Opravdu chcete tenoto komentář smazat?') ?>
	</li>
	<li>
		&nbsp;
	</li>
	<li>
		<?=$html->link('zpět na seznam komentářů', array('controller' => 'comments', 'action' => 'index')) ?>
	</li>
</ul>



			
			
