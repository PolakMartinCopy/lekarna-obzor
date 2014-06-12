<table class="top_heading">
	<tr>
		<th>ID</th>
		<th>Předmět</th>
		<th>Termín splnění</th>
		<th>Obchodní partner</th>
		<th>Zadavatel</th>
		<th>Řešitelé</th>
		<th>&nbsp;</th>
	</tr>
<?php
	$odd = '';
	foreach ($solutions as $solution) {
		$odd = ( $odd == ' class="odd"' ? '' : ' class="odd"' );
		//zvyraznim dosud nevyresene ukoly
		$style = '';
		if ($solution['Solution']['solution_state_id'] == 2) {
			$style = ' style="color:red"';
		}
?>
	<tr<?php echo $odd . $style?>>
		<td><?php echo $solution['Imposition']['id']?></td>
		<td><?php echo $html->link($solution['Imposition']['title'], array('controller' => 'impositions', 'action' => 'view', $solution['Imposition']['id']))?></td>
		<td><?php echo $solution['Solution']['accomplishment_date']?></td>
		<td><?php echo $html->link($solution['BusinessPartner']['name'], array('controller' => 'business_partners', 'action' => 'view', $solution['BusinessPartner']['id']))?></td>
		<td><?php echo $solution['User']['last_name'] . ' ' . $solution['User']['first_name']?></td>
		<td>
			<?php if (empty($solution['impositions_users'])) { ?>
			<em>Úkol nemá přiřazeny řešitele</em>
			<?php } else { ?>
			<ul>
				<?php foreach ($solution['impositions_users'] as $impositions_user) { ?>
				<li><?php echo $impositions_user['User']['last_name'] . '&nbsp;' . $impositions_user['User']['first_name']?></li>
				<?php } ?>
			</ul>
			<?php } ?>
		</td>
		<td class="actions">
			<?php
				if ($solution['Solution']['solution_state_id'] == 2) {
					echo $html->link('Vyřešeno', '#', array('class' => 'solve', 'rel' => $solution['Solution']['id'])) . ' | ';
				}
				echo $html->link('Upravit řešení', array('controller' => 'solutions', 'action' => 'edit', $solution['Solution']['id']));
				echo ' | ' . $html->link('Odstranit řešení', array('controller' => 'solutions', 'action' => 'delete', $solution['Solution']['id']), null, 'Opravdu chcete požadavek na vyřešení odstranit?');
				echo ' | ' . $html->link('Upravit úkol', array('controller' => 'impositions', 'action' => 'edit', $solution['Imposition']['id'], 'back_link' => base64_encode(serialize(array('controller' => 'impositions', 'action' => 'index')))));
				if ($solution['RecursiveImposition']['id']) {
					echo ' | ' . $html->link('Detail řady', array('controller' => 'impositions', 'action' => 'view', $solution['Imposition']['id']));
					echo ' | ' . $html->link('Odstranit řadu', array('controller' => 'impositions', 'action' => 'delete', $solution['Imposition']['id']), null, 'Opravdu chcete celé zadání odstranit?');
				} else {
					echo ' | ' . $html->link('Detail', array('controller' => 'impositions', 'action' => 'view', $solution['Imposition']['id']));
				}
			?>
		</td>
	</tr>
<?php } ?>
</table>