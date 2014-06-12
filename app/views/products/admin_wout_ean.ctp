<h1>Produkty bez EAN</h1>

<?php if (empty($products)) { ?>
<p><em>V systému nejsou žádné produkty bez EAN kódu.</em></p>
<?php } else { ?>
<table class="topHeading">
	<tr>
		<th>ID</th>
		<th>Název</th>
		<th>&nbsp;</th>
	</tr>
	<?php foreach ($products as $product) { ?>
	<tr>
		<td><?php echo $product['Product']['id']?></td>
		<td><?php echo $product['Product']['name']?></td>
		<td><?php echo $this->Html->link('Upravit', array('controller' => 'products', 'action' => 'edit', $product['Product']['id'], $product['CategoriesProduct'][0]['category_id']), array('target' => '_blank'))?></td>
	</tr>
	<?php } ?>
</table>
<?php } ?>