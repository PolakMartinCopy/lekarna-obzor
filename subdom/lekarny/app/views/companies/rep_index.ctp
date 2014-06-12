<h1>Seznam registrovaných společností</h1>

<? if ( isset($no_areas) ) { ?>
<p style="font-style:italic">Nemáte přiřazeny žádné oblasti, proto Vám nejsou přiděleni žádní zákazníci.</p>
<? } else { ?>
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
		<th>objednávky</th>
	</tr>
<?
	$odd = ' class="odd"';
	foreach ( $companies as $company ){
?>
	<tr<?php echo $odd?>>
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
		<td>
			<?php echo $html->link('tento měsíc', array('rep' => true, 'controller' => 'orders', 'action' => 'index', 'company_id' => $company['Company']['id'], 'month' => true)) ?><br />
			<?php echo $html->link('všechny', array('rep' => true, 'controller' => 'orders', 'action' => 'index', 'company_id' => $company['Company']['id'], 'all' => true)) ?>
		</td>
	</tr>
<?
		$odd = $odd == ' class="odd"' ? '' : ' class="odd"';
	}
?>
</table>
<? } ?>