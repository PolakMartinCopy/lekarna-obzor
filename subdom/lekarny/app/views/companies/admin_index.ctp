<h1>Seznam registrovaných společností</h1>
<table class="top_headed">
	<tr>
		<th>
			společnost
		</th>
		<th>
			odpovědná osoba
		</th>
		<th>
			doručovací adresa
		</th>
		<th>
			fakturační adresa
		</th>
	</tr>
<?
	$odd = ' class="odd"';
	foreach ( $companies as $company ){
?>
	<tr<?php echo $odd ?>>
		<td>
			<?=$company['Company']['name'] ?><br />
			IČO:<?=$company['Company']['ico'] ?><br />
			DIČ:<?=$company['Company']['dic'] ?>
		</td>
		<td>
			<?=$company['Company']['person_first_name'] . ' ' . $company['Company']['person_last_name'] ?><br />
			tel:<?=$company['Company']['person_phone'] ?><br />
			email:<?=$company['Company']['person_email'] ?>
		</td>
		<td>
			<?=$company['Company']['delivery_name'] ?><br />
			<?=$company['Company']['delivery_street'] . ' ' . $company['Company']['delivery_street_number'] ?><br />
			<?=$company['Company']['delivery_postal_code'] . ' ' . $company['Company']['delivery_city'] ?>
		</td>
		<td>
			<?=$company['Company']['delivery_name'] ?><br />
			<?=$company['Company']['delivery_street'] . ' ' . $company['Company']['delivery_street_number'] ?><br />
			<?=$company['Company']['delivery_postal_code'] . ' ' . $company['Company']['delivery_city'] ?>
		</td>
	</tr>
	<tr<?php echo $odd?>>
		<td colspan="4">
			<?
				// neschvalene schvalit
				// schvalene mit moznost zablokovat
				if ( $company['Company']['active'] == '0' ){
					echo $html->link('schválit', array('controller' => 'companies', 'action' => 'authorize', 'id' => $company['Company']['id']));
				} else {
					echo $html->link('zablokovat', array('controller' => 'companies', 'action' => 'block', 'id' => $company['Company']['id']));
				}
				
				echo '&nbsp;|&nbsp;' . $html->link('editovat', array('controller' => 'companies', 'action' => 'edit', 'id' => $company['Company']['id']));
				echo '&nbsp;|&nbsp;' . $html->link('smazat', array('controller' => 'companies', 'action' => 'delete', 'id' => $company['Company']['id']), array(), 'Opravdu chcete společnost smazat?');
			?>
		</td>
	</tr>
<?
		$odd = $odd == ' class="odd"' ? '' : ' class="odd"';
	}
?>
</table>