<h1>Upravit periodu úkolů</h1>

<?php echo $form->create('ImpositionPeriod', array('url' => array('controller' => 'imposition_periods', 'action' => 'edit', $period['ImpositionPeriod']['id'])))?>
<table class="left_heading">
	<tr>
		<th>Název</th>
		<td><?php echo $form->input('ImpositionPeriod.name', array('label' => false))?></td>
	</tr>
	<tr>
		<th>Interval</th>
		<td><?php echo $form->input('ImpositionPeriod.interval', array('label' => false))?></td>
	</tr>
</table>

<?php 
echo $form->hidden('ImpositionPeriod.id');
echo $form->submit('Uložit');
echo $form->end();
?>