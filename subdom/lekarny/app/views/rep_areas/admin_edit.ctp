<h1>Upravit oblast</h1>
<ul>
	<li><?=$html->link('Zpět na seznam oblastí', array('controller' => 'rep_areas', 'action' => 'index', 'rep_id' => $rep_id)) ?></li>
</ul>

<?=$form->create('RepArea', array('url' => array('controller' => 'rep_areas', 'action' => 'edit', 'id' => $id))) ?>
<table class="left_headed">
	<tr>
		<th>Počáteční PSČ</th>
		<td><?=$form->input('RepArea.start_zip', array('label' => false)) ?></td>
	</tr>
	<tr>
		<th>Koncové PSČ</th>
		<td><?=$form->input('RepArea.end_zip', array('label' => false)) ?></td>
	</tr>
	<tr>
		<th>Oblast</th>
		<td><?=$form->input('RepArea.area', array('label' => false)) ?></td>
	</tr>
</table>

<?
	echo $form->hidden('RepArea.id');
	echo $form->submit('Odeslat');
	echo $form->end();
?>