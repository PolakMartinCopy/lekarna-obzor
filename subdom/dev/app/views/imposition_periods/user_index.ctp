<h1>Periody úkolů</h1>

<?php if (empty($periods)) { ?>
<p><em>V databázi nejsou žádné periody úkolů.</em></p>
<?php } else { ?>
<table class="top_heading">
	<tr>
		<th>ID</th>
		<th>Název</th>
		<th>Interval</th>
		<th>&nbsp;</th>
	</tr>
	<?php foreach ($periods as $period) { ?>
	<tr>
		<td><?php echo $period['ImpositionPeriod']['id']?></td>
		<td><?php echo $period['ImpositionPeriod']['name']?></td>
		<td><?php echo $period['ImpositionPeriod']['interval']?></td>
		<td>
			<?php echo $html->link('Upravit', array('controller' => 'imposition_periods', 'action' => 'edit', $period['ImpositionPeriod']['id']))?>
			<?php echo $html->link('Smazat', array('controller' => 'imposition_periods', 'action' => 'delete', $period['ImpositionPeriod']['id']), null, 'Opravdu chcete periodu úkolů odstranit?')?>
		</td>
	</tr>
	<?php } ?>
</table>
<?php } ?>