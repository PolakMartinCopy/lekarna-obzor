<?
	echo 'JMENO;PRIJMENI;ADRESA;EMAIL;TELEFON;DATUM_REGISTRACE' . "\n";
	foreach ($customers as $customer) {
		$created = explode(' ', $customer['Customer']['created']);
		$created = $created[0];
		echo '"' . iconv('UTF-8', 'cp1250', $customer['Customer']['first_name']) . '";"' . iconv('UTF-8', 'cp1250', $customer['Customer']['last_name']) . '";"' . iconv('UTF-8', 'cp1250', $customer[0]['address']) . '";"' . $customer['Customer']['email'] . '";"' . $customer['Customer']['phone'] . '";"' . $created . '"' . "\n";
	}
?>