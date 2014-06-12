<div class="mainContentWrapper">
<h1>Nový komentář k produktu <?php echo $product['Product']['name']?></h1>
	<?=$form->create('Comment', array('url' => array('action' => 'add', $product['Product']['id']))) ?>
	<table>
		<tr>
			<th>Jméno (přezdívka):</th>
			<td><?=$form->input('Comment.author', array('label' => false, 'div' => false, 'size' => 50)) ?></td>
		</tr>
		<tr>
			<th>Email:</th>
			<td>
				<?=$form->input('Comment.email', array('label' => false, 'div' => false, 'size' => 50)) ?>
			</td>
		</tr>
		<tr>
			<th>Předmět:</th>
			<td><?=$form->input('Comment.subject', array('label' => false, 'div' => false, 'size' => 100)) ?></td>
		</tr>
		<tr>
			<th>Komentář (dotaz):</th>
			<td><?=$form->input('Comment.body', array('label' => false, 'div' => false, 'cols' => 63, 'rows' => 10)) ?></td>
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