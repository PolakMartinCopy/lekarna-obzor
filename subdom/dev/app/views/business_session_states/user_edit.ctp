<h1>Upravit stav obchodního jednání</h1>
<ul>
	<li><?php echo $html->link('Zpět na seznam stavů obchodního jednání', array('controller' => 'business_session_states', 'action' => 'index'))?></li>
</ul>

<?php echo $form->create('BusinessSessionState', array('url' => array('controller' => 'business_session_states', 'action' => 'edit')))?>
<table class="left_heading">
	<tr>
		<th>Název</th>
		<td><?php echo $form->input('BusinessSessionState.name', array('label' => false))?></td>
	</tr>
</table>
<?php echo $form->hidden('BusinessSessionState.id')?>
<?php echo $form->submit('Upravit')?>
<?php echo $form->end() ?>

<ul>
	<li><?php echo $html->link('Zpět na seznam stavů obchodního jednání', array('controller' => 'business_session_states', 'action' => 'index'))?></li>
</ul>