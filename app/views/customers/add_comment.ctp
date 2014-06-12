<div class="mainContentWrapper">
	<?=$form->create('Comment', array('url' => array('controller' => 'customers', 'action' => 'add_comment', $product['Product']['id']))) ?>
	<table>
		<tr>
			<th>Jméno (přezdívka):</th>
			<td><?=$form->input('Comment.author', array('label' => false)) ?></td>
		</tr>
		<tr>
			<th>Předmět:</th>
			<td><?=$form->input('Comment.subject', array('label' => false)) ?></td>
		</tr>
		<tr>
			<th>Komentář (dotaz):</th>
			<td><?=$form->input('Comment.body', array('label' => false)) ?></td>
		</tr>
	</table>
	<?=$form->hidden('Comment.product_id', array('value' => $product['Product']['id'])) ?>
	<?=$form->end('Přidat') ?>
	<div class="actions">
		<ul>
			<li><?=$html->link('zpět na seznam komentářů', array('controller' => 'products', 'action' => 'view_comments', $product['Product']['id'])) ?></li>
			<li><?=$html->link('zpět na detaily o produktu', '/' . $product['Product']['url'])?></li>
		</ul>
	</div>
	
</div>