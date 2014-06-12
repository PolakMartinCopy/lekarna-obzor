<h1>Upravit stav úkolu</h1>
<ul>
	<li><?php echo $html->link('Zpět na seznam stavů úkolů', array('controller' => 'imposition_states', 'action' => 'index'))?></li>
</ul>

<?php echo $form->create('ImpositionState', array('url' => array('controller' => 'imposition_states', 'action' => 'edit')))?>
<table class="left_heading">
	<tr>
		<th>Název</th>
		<td><?php echo $form->input('ImpositionState.name', array('label' => false))?></td>
	</tr>
</table>
<?php echo $form->hidden('ImpositionState.id')?>
<?php echo $form->submit('Upravit')?>
<?php echo $form->end() ?>

<ul>
	<li><?php echo $html->link('Zpět na seznam stavů úkolů', array('controller' => 'imposition_states', 'action' => 'index'))?></li>
</ul>