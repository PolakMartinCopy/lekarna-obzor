<h1>Přidat stav úkolu</h1>
<ul>
	<li><?php echo $html->link('Zpět na seznam stavů úkolů', array('controller' => 'imposition_states', 'action' => 'index'))?></li>
</ul>

<?php echo $form->create('ImpositionState', array('url' => array('controller' => 'imposition_states', 'action' => 'add')))?>
<table class="left_heading">
	<tr>
		<th>Název</th>
		<td><?php echo $form->input('ImpositionState.name', array('label' => false))?></td>
	</tr>
</table>
<?php echo $form->submit('Uložit')?>
<?php echo $form->end() ?>

<ul>
	<li><?php echo $html->link('Zpět na seznam stavů úkolů', array('controller' => 'imposition_states', 'action' => 'index'))?></li>
</ul>