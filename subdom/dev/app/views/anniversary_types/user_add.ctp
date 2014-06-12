<h1>Přidat typ výročí</h1>
<ul>
	<li><?php echo $html->link('Zpět na seznam typů výročí', array('controller' => 'anniversary_types', 'action' => 'index'))?></li>
</ul>

<?php echo $form->create('AnniversaryType', array('url' => array('controller' => 'anniversary_types', 'action' => 'add')))?>
<table class="left_heading">
	<tr>
		<th>Název</th>
		<td><?php echo $form->input('AnniversaryType.name', array('label' => false))?></td>
	</tr>
	<tr>
		<th>Každý rok</th>
		<td><?php echo $form->input('AnniversaryType.every_year', array('label' => false))?></td>
	</tr>
</table>
<?php echo $form->submit('Uložit')?>
<?php echo $form->end() ?>

<ul>
	<li><?php echo $html->link('Zpět na seznam typů výročí', array('controller' => 'anniversary_types', 'action' => 'index'))?></li>
</ul>