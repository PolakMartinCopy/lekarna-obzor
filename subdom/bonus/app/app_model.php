<?php
class AppModel extends Model {
	var $export_file = 'export.csv';
	
	// validace, ze v poli je hodnota vetsi nez 0
	function moreThenZero($amount) {
		$key = array_keys($amount);
		$key = $key[0];
		$amount = $amount[$key];
		
		return $amount > 0;
	}
	
	function create_csv($data) {
		$file = fopen($this->export_file, 'w');
		$header = implode(';', $this->export_fields);
		// do souboru zapisu hlavicku csv (nazvy sloupcu)
		fwrite($file, $header . "\r\n");
		
		foreach ($data as $item) {
			$line = $this->create_csv_line($item); // poskladam si radek
			
			// prekoduju do cp 1250
			$cp_1250_line = iconv('utf-8', 'windows-1250', $line);
			// zapisu do souboru
			fwrite($file, $cp_1250_line . "\r\n");
		}
		
		fclose($file);
		return true;
	}
	
	function create_csv_line($item) {
		// z definice export fields si seskladam indexy poli (napr mam Customer.name a chci ['Customer']['name']
		$indexes = $this->create_indexes();

		$line = array();
		foreach ($indexes as $index) {
			
			eval('$attribute = $item' . $index . ';');
			// cisla s desetinnou teckou musim predelat na desetinnou carku
			if (preg_match('/^-?\d+\.\d+$/', $attribute)) {
				$attribute = str_replace('.', ',', $attribute);
			}
			
			$line[] = $attribute;
		}
		
		return implode(';', $line);
	}
	
	function create_indexes() {
		$indexes = array();
		foreach ($this->export_fields as $export_field) {
			// mam Customer.last_name a potrebuju $item['Customer']['last_name']
			list($model, $field) = explode('.', $export_field);
			
			$indexes[] = '[\'' . $model . '\'][\'' . $field . '\']';
		}
		return $indexes;
	}
}