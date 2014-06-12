<h1>Přidat periodu úkolů</h1>

<?php echo $form->create('ImpositionPeriod')?>
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
echo $form->submit('Uložit');
echo $form->end();
?>