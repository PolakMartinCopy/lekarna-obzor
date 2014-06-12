<h1>Upravit typ výročí</h1>
<ul>
	<li><?php echo $html->link('Zpět na seznam typů výročí', array('controller' => 'anniversary_types', 'action' => 'index'))?></li>
</ul>

<?php echo $form->create('AnniversaryType', array('url' => array('controller' => 'anniversary_types', 'action' => 'edit')))?>
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
<?php echo $form->hidden('AnniversaryType.id')?>
<?php echo $form->submit('Upravit')?>
<?php echo $form->end() ?>

<ul>
	<li><?php echo $html->link('Zpět na seznam typů výročí', array('controller' => 'anniversary_types', 'action' => 'index'))?></li>
</ul>