<h1>Přidat typ obchodního jednání</h1>
<ul>
	<li><?php echo $html->link('Zpět na seznam typů obchodního jednání', array('controller' => 'business_session_types', 'action' => 'index'))?></li>
</ul>

<?php echo $form->create('BusinessSessionType', array('url' => array('controller' => 'business_session_types', 'action' => 'add')))?>
<table class="left_heading">
	<tr>
		<th>Název</th>
		<td><?php echo $form->input('BusinessSessionType.name', array('label' => false))?></td>
	</tr>
</table>
<?php echo $form->submit('Uložit')?>
<?php echo $form->end() ?>

<ul>
	<li><?php echo $html->link('Zpět na seznam typů obchodního jednání', array('controller' => 'business_session_types', 'action' => 'index'))?></li>
</ul>