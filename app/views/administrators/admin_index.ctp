<h2>Seznam administrátorů</h2>
<table class="topHeading">
	<tr>
		<th>ID</th>
		<th>příjmení, jméno</th>
		<th>&nbsp;</th>
	</tr>
	<? foreach ($admins as $admin) { ?>
	<tr>
		<td><?=$admin['Administrator']['id'] ?></td>
		<td><?=$admin['Administrator']['last_name'] . ', ' . $admin['Administrator']['first_name'] ?></td>
		<td>
			<?=$html->link('upravit', array('controller' => 'administrators', 'action' => 'edit', $admin['Administrator']['id'])) ?>
		</td>

	</tr>
		
	<? } ?>
</table>
<div class="actions">
	<ul>
		<li><?=$html->link('nový administrátor', array('controller' => 'administrators', 'action' => 'add'))?></li>
	</ul>
</div>