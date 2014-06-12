<h1>Výsledky vyhledávání na dotaz "<?=$query ?>"</h1>

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
		<th>&nbsp;</th>
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
		<td><?=$html->link('Objednávky', array('rep' => true, 'controller' => 'orders', 'action' => 'index', 'company_id' => $company['Company']['id'])) ?></td>
	</tr>
<?
		$odd = $odd == ' class="odd"' ? '' : ' class="odd"';
	}
?>
</table>
<? } ?>