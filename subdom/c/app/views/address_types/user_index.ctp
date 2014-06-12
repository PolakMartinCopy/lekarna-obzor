<h1>Typy adres</h1>
<ul>
	<li><?php echo $html->link('Přidat typ adresy', array('controller' => 'address_types', 'action' => 'add'))?></li>
</ul>

<?php if (empty($address_types)) { ?>
<p><em>V databázi nejsou žádné typy adresy.</em></p>
<?php } else { ?>
<table class="top_heading">
	<tr>
		<th>ID</th>
		<th>Název</th>
		<th>Jen jednou</th>
		<th>&nbsp;</th>
	</tr>
<?php
	$odd = '';
	foreach ($address_types as $address_type) {
		$odd = ( $odd == ' class="odd"' ? '' : ' class="odd"' );
?>
	<tr<?php echo $odd?>>
		<td><?php echo $address_type['AddressType']['id']?></td>
		<td><?php echo $address_type['AddressType']['name']?></td>
		<td><?php echo $address_type['AddressType']['just_one']?></td>
		<td class="actions">
			<?php echo $html->link('Upravit', array('controller' => 'address_types', 'action' => 'edit', $address_type['AddressType']['id']))?>
			<?php echo $html->link('Smazat', array('controller' => 'address_types', 'action' => 'delete', $address_type['AddressType']['id']), null, 'Opravdu si přejete typ adresy ' . $address_type['AddressType']['name'] . ' smazat?')?>
		</td>
	</tr>
<?php } // end foreach?>
</table>
<?php } // end if?>

<ul>
	<li><?php echo $html->link('Přidat typ adresy', array('controller' => 'address_types', 'action' => 'add'))?></li>
</ul>