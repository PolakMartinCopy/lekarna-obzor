<?php
class AppModel extends Model {
	var $export_file = 'files/export.csv';
	
	// kontroluje, jestli se jedna o obycejneho uzivatele a jestli nevyzaduje pristup
	// k datum jineho uzivatele
	function checkUser($user, $checked_id) {
		if ($user['User']['user_type_id'] == 3) {
			if ($checked_id != $user['User']['id']) {
				return false;
			}
		}
		return true;
	}
	
	/**
	 * 
	 * Z data ve formatu array udela retezec pro ulozeni do db
	 * @param array $date
	 */
	function built_date($date) {
		if (strlen($date['month']) == 1) {
			$date['month'] = '0' . $date['month'];
		}
		if (strlen($date['day']) == 1) {
			$date['day'] = '0' . $date['day'];
		}
		return $date['year'] . '-' . $date['month'] . '-' . $date['day'];
	}
	
	/**
	 * 
	 * z data ve stringu udela pole
	 * @param string $date
	 */
	function unbuilt_date($date) {
		$date = explode('-', $date);
		return array('day' => $date[2], 'month' => $date[1], 'year' => $date[0]);
	}
	
	/**
	 * 
	 * provadi export dat, pouzije zadany find a data zapise do xls
	 * @param array $find
	 */
	function xls_export($find, $export_fields) {

		// pole kde jsou data typu datetime
		$datetime_fields = array(
			'BusinessSession.date',
			'BusinessSession.created',
			'Imposition.created',
			'Offer.created',
			'CSInvoice.date_of_issue',
			'CSCreditNote.date_of_issue',
			'CSTransaction.date_of_issue'
		);
		
		// pole kde jsou data typu date
		$date_fields = array(
			'Solution.accomplishment_date',
			'Cost.date',
			'DeliveryNote.date',
			'Sale.date',
			'Transaction.date',
			'CSStoring.date',
			'CSInvoice.due_date',
			'CSCreditNote.due_date'
		);
		
		// exportuju udaj o tom, ktera pole jsou soucasti vystupu
		$find['fields'] = Set::extract('/field', $export_fields);

		// vyhledam data podle zadanych kriterii
		$data = $this->find('all', $find);
		$file = fopen($this->export_file, 'w');

		// zjistim aliasy, pod kterymi se vypisuji atributy v csv souboru
		$aliases = Set::extract('/alias', $export_fields);
		
		// rozdelim datetime a date pole zvlast do sloupcu den, mesic, rok
		$res_aliases = array();
		foreach ($aliases as $alias) {
			if (in_array($alias, $datetime_fields)) {
				$res_aliases[] = $alias . '_day';
				$res_aliases[] = $alias . '_month';
				$res_aliases[] = $alias . '_year';
				$res_aliases[] = $alias . '_time';
			} elseif (in_array($alias, $date_fields)) {
				$res_aliases[] = $alias . '_day';
				$res_aliases[] = $alias . '_month';
				$res_aliases[] = $alias . '_year';
			} else {
				$res_aliases[] = $alias;
			}
		}
		$aliases = $res_aliases;

		$line = implode(';', $aliases);
		// do souboru zapisu hlavicku csv (nazvy sloupcu)
		fwrite($file, iconv('utf-8', 'windows-1250', $line . "\r\n"));

		$positions = Set::extract('/position', $export_fields);
		// do souboru zapisu data (radky vysledku)
		foreach ($data as $item) {
			$line = '';
			$results = array();
			foreach ($positions as $position) {
				$expression = '$item' . $position;
				$expression = str_replace('"', '\'', $expression);
				eval("\$result = ". $expression . ";");
				// rozdelim datetime zvlast na sloupce den, mesic, rok
				if (preg_match('/(....)-(..)-(..) (.+)/', $result, $matches)) {
					$results[] = $matches[3];
					$results[] = $matches[2];
					$results[] = $matches[1];
					$results[] = $matches[4];
				// rozdelim date zvlast na sloupce den, mesic, rok
				} elseif (preg_match('/(....)-(..)-(..)/', $result, $matches)) {
					$results[] = $matches[3];
					$results[] = $matches[2];
					$results[] = $matches[1];
				} else {
					$result = preg_replace('/^(-?\d+)\.(\d+)$/', '$1,$2', $result);
					$results[] = $result;
				}
			}
			$line = implode(';', $results);

			// ulozim radek
			fwrite($file, iconv('utf-8', 'windows-1250', $line . "\n"));
		}

		fclose($file);
		return true;
	}
}