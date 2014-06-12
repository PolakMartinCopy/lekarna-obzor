<h1>Upravit typ obchodního jednání</h1>
<ul>
	<li><?php echo $html->link('Zpět na seznam typů obchodního jednání', array('controller' => 'business_session_types', 'action' => 'index'))?></li>
</ul>

<?php echo $form->create('BusinessSessionType', array('url' => array('controller' => 'business_session_types', 'action' => 'edit')))?>
<table class="left_heading">
	<tr>
		<th>Název</th>
		<td><?php echo $form->input('BusinessSessionType.name', array('label' => false))?></td>
	</tr>
</table>
<?php echo $form->hidden('BusinessSessionType.id')?>
<?php echo $form->submit('Upravit')?>
<?php echo $form->end() ?>

<ul>
	<li><?php echo $html->link('Zpět na seznam typů obchodního jednání', array('controller' => 'business_session_types', 'action' => 'index'))?></li>
</ul>