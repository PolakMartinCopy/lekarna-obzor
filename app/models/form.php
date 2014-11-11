<?php 
class Form extends AppModel {
	var $name = 'Form';
	
	var $actsAs = array('Containable');
	
	var $useTable = false;
	
	var $validate = array(
		'name' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Zadejte Vaše jméno'
			)
		),
		'phone' => array(
			'phone_or_email_not_empty' => array(
				'rule' => array('phone_or_email_not_empty'),
				'message' => 'Zadejte telefon nebo emailovou adresu'
			)
		),
		'email' => array(
			'email' => array(
				'rule' => array('email', true),
				'allowEmpty' => true,
				'last' => true,
				'message' => 'Zadaná adresa obsahuje chybu. Vlože platnou emailovou adresu.'
			),
			'phone_or_email_not_empty' => array(
				'rule' => array('phone_or_email_not_empty'),
				'message' => 'Zadejte telefon nebo emailovou adresu'
			)
		),
		'message' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Zadejte zprávu'
			)
		)
	);
	
	function phone_or_email_not_empty() {
		if (array_key_exists('phone', $this->data['Form']) && array_key_exists('email', $this->data['Form'])) {
			return ($this->data['Form']['phone'] || $this->data['Form']['email']);
		}
	}
	
	function send($data, $target = CUST_MAIL) {
		App::import('Vendor', 'PHPMailer', array('file' => 'phpmailer/class.phpmailer.php'));
		$email = new PHPMailer();

		// uvodni nastaveni
		$email->CharSet = 'utf-8';
		$email->Hostname = CUST_ROOT;
		$email->Sender = 'no-reply@' . CUST_ROOT;
		
		// nastavim adresu, od koho se poslal email
		$email->From     = 'no-reply@' . CUST_ROOT;
		$email->FromName = 'Automatický pošťák - www.' . CUST_ROOT;
		$email->AddAddress($target);
		$email->Subject = 'Zpráva z formuláže pro objednávku léků na předpis';
		
		$body = "Dobrý den,

právě jsme obdrželi následující žádost z formuláře pro objednání léků na předpis:

";
		
		foreach ($data['Form'] as $key => $value) {
			$body .= $key . ': ' . $value . "\n";
		}
		
$body .= 'S pozdravem team www.' . CUST_ROOT . '.';
		
		$email->Body = $body;
		return $email->Send();
		
	}
}
?>