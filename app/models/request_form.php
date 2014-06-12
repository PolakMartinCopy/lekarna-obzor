<?
class RequestForm extends AppModel {
	var $name = 'RequestForm';
	
	var $actsAs = array('Containable');
	
	var $belongsTo = array('Product');
	
	var $validate = array(
		'name' => array(
			'notEmpty' => array(
				'rule' => array('minLength', 1),
				'message' => 'Jméno a příjmení musí být vyplněno.'
			),
			'not_link' => array(
				'rule' => array('not_link'),
				'message' => 'Nelze vložit odkaz, vložte jej prosím bez tagů'
			)
		),
		'phone' => array(
			'contact_not_empty' => array(
				'rule' => array('contact_not_empty'),
				'message' => 'Alespoň jedna z kontaktních informací musí být vyplněna.'
			),
			'valid_phone' => array(
				'rule' => array('valid_phone'),
				'message' => 'Vložte Vaše telefonní číslo, například ve tvaru +420 123 456 789'
			)
		),
		'email' => array(
			'contact_not_empty' => array(
				'rule' => array('contact_not_empty'),
				'message' => 'Alespoň jedna z kontaktních informací musí být vyplněna.',
				'last' => true
			),
			'email' => array(
				'rule' => array('email', true),
				'message' => 'Vyplňte prosím platnou emailovou adresu.',
				'allowEmpty' => true
			)
		),
		'message' => array(
			'not_empty' => array(
				'rule' => array('minLength', 1),
				'message' => 'Vyplňte prosím co nám chcete sdělit, nebo Vaši otázku.'
			),
			'not_link' => array(
				'rule' => array('not_link'),
				'message' => 'Nelze vložit odkaz, vložte jej prosím bez tagů'
			)
		),
		'hack_field' => array(
			'empty' => array(
				'rule' => array('maxLength', 0),
				'message' => 'Validacni pole musi zustat prazdne'
			)
		)
	);

	function contact_not_empty() {
		if ( strlen($this->data['RequestForm']['email']) == 0 && strlen($this->data['RequestForm']['phone']) == 0 ) {
				return false;
		}
		return true;
	}
	
	function not_link($data) {
		foreach ($data as $key => $value) {
			if (preg_match('/<a href/', $value) || preg_match('/\[url=/', $value) || preg_match('/\[link=/', $value)) {
				return false;
			}
		}
		return true;
	}
	
	function valid_phone($data) {
		if (empty($data['phone'])) {
			return true;
		}
		
		$pattern = '/(?:(?:(?:\+|00)(?: )?\d{3})?(?: )?)(?:(?:\d{3}(?: )?\d{3}(?: )?\d{3})|(?:\d{3}(?: )?\d{2}(?: )?\d{2}(?: )?\d{2}))/';
		return (preg_match($pattern, $data['phone']));
	}
	
	// testuje, jestli z ipcka neprislo uz vice nez 2 poptavky behem posledni hodiny
	// chceme tim zamezit spammerum, ktery z jednoho ipcka posilaji requesty
	function checkSpam($data) {
		// zkontroluju, jestli pro danou ip neexistuji v db za posledni hodinu 2 zaznamy a pokud ano, odeslani formulare nepovolim
		$start_date = date('Y-m-d H:i:s', strtotime('-1 hour'));
		$end_date = date('Y-m-d H:i:s');
		
		$count = $this->find('count', array(
			'conditions' => array(
				'RequestForm.created >' => $start_date,
				'RequestForm.created <' => $end_date,
				'RequestForm.ip' => $data['RequestForm']['ip']
			)
		));

		return $count < 2;
	}
	
	/**
	 * 
	 * posle email s dotazem z poptavkoveho formu
	 * @param unknown_type $data
	 */
	function notify($data, $product) {
		// poslat email
		// zatim nastaveno na nas
		App::import('Vendor', 'phpmailer', array('file' => 'class.phpmailer.php'));
		$mail = &new phpmailer;
		// poskladam email
		$mail->CharSet = 'utf-8';
		$mail->From = $data['RequestForm']['email'];
		$mail->FromName = $data['RequestForm']['name'];
$mail->AddAddress(CUST_MAIL, CUST_NAME);				
//					$mail->AddBCC('vlado@tovarnak.com');
//					$mail->AddBCC('brko11@gmail.com');
		$mail->Subject = 'Zpráva z poptávkového formuláře na www.' . CUST_ROOT;
		$mail->Body = 'Dobrý den,
prostřednictvím poptávkového formuláře na www.' . CUST_ROOT . ' jste obdrželi následující zprávu:

' . $data['RequestForm']['message'] . '

';
		$mail->Body .= 'Zpráva se vztahuje k produktu: http://www.' . CUST_ROOT . '/' . $product['Product']['url'] . '

';
				
		$mail->Body .= 'Jméno: ' . $data['RequestForm']['name'] . '
Telefon: ' . $data['RequestForm']['phone'];
		if (!empty($this->data['RequestForm']['email'])) {
			$mail->Body .= '
Email: ' . $data['RequestForm']['email'];
		}

		return $mail->Send();
	}
}
?>