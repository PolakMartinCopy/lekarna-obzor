<?php 
class Rate extends AppModel {
	var $name = 'Rate';
	
	var $actsAs = array('Containable');
	
	var $validate = array(
		'value' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty'
			),
			'decimal' => array(
				'rule' => array('decimal'),
				'message' => 'Kurz musí být desetinné číslo.'
			)
		)
	);
	
	var $export_fields = array(
		'Rate.date',
		'Rate.time',
		'Rate.value'
	);
	
	var $export_file = 'export.csv';
	
	function beforeValidate() {
		$this->data['Rate']['value'] = str_replace(',', '.', $this->data['Rate']['value']);
	}
	
	function afterSave() {
		App::import('Model', 'Setting');
		$this->Setting = &new Setting;
		$setting = $this->Setting->find('first');
		if (isset($setting['Setting']['rate']) && isset($setting['Setting']['phone'])) {
			// pokud je aktualni kurz vyssi nez zadany v nastaveni, poslu sms na cislo z nastaveni
			if ($this->data['Rate']['value'] > $setting['Setting']['rate']) {
				$address = '00420' . $setting['Setting']['phone'] . '@sms.cz.o2.com';
				
				App::import('Vendor', 'PHPMailer', array('file' => 'class.phpmailer.php'));
				$mail = &new PHPMailer();
				
				$mail->CharSet = 'UTF-8';
				
				$mail->From = 'no-reply@exchange.lekarna-obzor.cz';
				$mail->FromName = 'Systém pro sledování kurzů';
				
				$mail->Body = 'Aktuální kurz je vyšší, než je sledovaná hranice.';
				$mail->AddAddress($address);
				$mail->Send();
			}
		}
		return true;
	}
	
	function do_form_search($data = null) {
		$conditions = array();
		if (isset($data)) {
			$from = $data['from'];
			$to = $data['to'];
			$from = cz2db_date($from);
			$to = cz2db_date($to);
			$from .= ' 00:00:00';
			$to .= ' 23:59:59';
			$conditions = array(
				array('Rate.created >=' => $from),
				array('Rate.created <=' => $to)
			);
		}
		return $conditions;
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
	
	function notify($body) {
		App::import('Vendor', 'PHPMailer', array('file' => 'class.phpmailer.php'));
		$mail = &new PHPMailer();
		
		$mail->CharSet = 'UTF-8';
		
		$mail->From = 'no-reply@exchange.lekarna-obzor.cz';
		$mail->FromName = 'Systém pro sledování kurzů';
		
		$mail->Subject = 'Chyba v systému pro sledování kurzů';
		$mail->Body = $body;
		
		$mail->AddAddress(CUSTOMER_EMAIL);
		
		return $mail->Send();
	}
}
?>