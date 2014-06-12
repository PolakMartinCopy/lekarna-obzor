<h1>Přesunout kategorii <?=$category['Category']['name'] ?></h1>

<?=$form->create('Category', array('url' => '/admin/categories/move/' . $category['Category']['id'])) ?>
<?=$form->hidden('Category.id', array('value' => $category['Category']['id'])) ?>
<table class="left_headed">
	<tr>
		<th>do kategorie</th>
		<td><?=$form->select('Category.parent_id', $categories, $category['Category']['parent_id'], array('label' => false), false) ?></td>
	</tr>
</table>
<?=$form->submit('Odeslat') ?>
<?=$form->end() ?>