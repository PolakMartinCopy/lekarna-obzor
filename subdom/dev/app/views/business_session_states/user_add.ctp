<h1>Přidat stav obchodního jednání</h1>
<ul>
	<li><?php echo $html->link('Zpět na seznam stavů obchodního jednání', array('controller' => 'business_session_states', 'action' => 'index'))?></li>
</ul>

<?php echo $form->create('BusinessSessionState', array('url' => array('controller' => 'business_session_states', 'action' => 'add')))?>
<table class="left_heading">
	<tr>
		<th>Název</th>
		<td><?php echo $form->input('BusinessSessionState.name', array('label' => false))?></td>
	</tr>
</table>
<?php echo $form->submit('Uložit')?>
<?php echo $form->end() ?>

<ul>
	<li><?php echo $html->link('Zpět na seznam stavů obchodního jednání', array('controller' => 'business_session_states', 'action' => 'index'))?></li>
</ul>