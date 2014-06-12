<h1>Přidat náklad</h1>
<ul>
	<li><?php echo $html->link('Detaily obchodního jednání', array('controller' => 'business_sessions', 'action' => 'view', $business_session['BusinessSession']['id']))?></li>
</ul>

<?php echo $form->create('Cost', array('url' => array('controller' => 'costs', 'action' => 'add', 'business_session_id' => $business_session_id)))?>
<table class="left_heading">
	<tr>
		<th>Popis</th>
		<td><?php echo $form->input('Cost.description', array('label' => false)) ?></td>
	</tr>
	<tr>
		<th>Částka</th>
		<td><?php echo $form->input('Cost.amount', array('label' => false))?></td>
	</tr>
	<tr>
		<th>Datum</th>
		<td><?php echo $form->input('Cost.date', array('label' => false, 'dateFormat' => 'DMY', 'monthNames' => $monthNames))?></td>
	</tr>
</table>
<?php echo $form->hidden('Cost.business_session_id', array('value' => $business_session_id))?>
<?php echo $form->submit('Uložit')?>
<?php echo $form->end() ?>