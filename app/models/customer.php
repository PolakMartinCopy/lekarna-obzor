<?php
class Customer extends AppModel {
	var $name = 'Customer';

	var $hasMany = array('Order', 'Address');

	var $actsAs = array('Containable');
	
	var $validate = array(
		'first_name' => array(
			'rule' => array('minLength', 3),
			'required' => true,
			'message' => 'Vyplňte prosím vaše jméno.'
		),
		'last_name' => array(
			'rule' => array('minLength', 3),
			'required' => true,
			'message' => 'Vyplňte prosím vaše příjmení.'
		),
		'phone' => array(
			'minLength' => array(
				'rule' => array('minLength', 8),
				'required' => true,
				'message' => 'Vyplňte prosím správně vaše telefonní číslo.'
			),
			'numeric' => array(
				'rule' => array('numeric'),
				'required' => true,
				'message' => 'Telefonní číslo obsahuje nepovolené znaky.'
			)
		),
		'email' => array(
			'email' => array(
				'rule' => array('email', false),
				'message' => 'Vyplňte prosím existující emailovou adresu.',
				'required' => true
			),
			'isUnique' => array(
				'rule' => 'isUnique',
				'message' => 'Uživatel s touto emailovou adresou již existuje. Zvolte jinou emailovou adresu, nebo se přihlašte.'
			)
		),
		'login' => array(
			'isUnique' => array(
				'rule' => 'isUnique',
				'message' => 'Uživatel s tímto loginem již existuje. Zvolte prosím jiný login'
			),
			'between' => array(
				'rule' => array('between', 8, 20),
				'message' => 'Pole Login musí obsahovat alespoň 8 a maximálně 20 znaků'
			)
		)
	);

	function assignPassword($id, $email){
		$start = rand(0, 23);
		$password = md5($email . Configure::read('Security.salt'));
		$password = substr($password, $start, 8);
		$password = strtolower($password);
		$this->data['Customer']['password'] = md5($password);
		$this->id = $id;
		$this->save($this->data, false);
		return $password;
	}
	
	
	function changePassword($customer){
		include 'class.phpmailer.php';

		$mail = &new phpmailer;

		$mail->CharSet = 'utf-8';
		$mail->Hostname = CUST_ROOT;
		$mail->Sender = CUST_MAIL;
		$mail->From = CUST_MAIL;
		$mail->FromName = CUST_NAME;
		$mail->ReplyTo = CUST_MAIL;
		
		$mail->AddAddress($customer['Customer']['email'], $customer['Customer']['first_name'] . " " . $customer['Customer']['last_name']);
		$mail->Subject = 'změna hesla pro přístup do www.' . CUST_ROOT;
		$mail->Body = "Dobrý den,\n\n";
		$mail->Body .= "Váš požadavek na změnu hesla byl vykonán, pro přihlášení k účtu,
		použijte následující údaje: \n";
		$mail->Body .= "login: " . $customer['Customer']['login'] . "\n";
		$mail->Body .= "heslo: " . $this->assignPassword($customer['Customer']['id'], $customer['Customer']['email']) . "\n";
		$mail->Body .= "team " . CUST_NAME . "\n";
		$mail->Body .= "--\n";
		$mail->Body .= "emailová adresa " . $customer['Customer']['email'] . " byla použita pro vyžádání změny hesla pro přístup\n";
		$mail->Body .= "na www." . CUST_ROOT . ". Jste-li majitelem emailové schránky a neprováděl(a) jste žádnou žádost o změnu,\n";
		$mail->Body .= "upozorněte nás prosím na tuto skutečnost na adrese webmaster@" . CUST_ROOT;

		$mail->Send();
	}
	
	function loginExists($login){
		$condition = array('login' => $login);
		return $this->hasAny($condition);
	}
	
	function generateLogin($customer){
		// vygeneruje nahodne login
		do{
			// vytahnu si osm znaku z md5ky,
			// s nahodnym startem
			$start = rand(0, 23);
			$login = md5($customer['last_name'] . date("Y-m-d"));
			$login = substr($login, $start, 8);
			// dam si login do uppercase
			$login = strtoupper($login);
		} while ( $this->loginExists($login) === true );
		
		return $login;
	}
	
	
	function generatePassword($customer){
		// vytahnu si osm znaku z md5ky,
		// s nahodnym startem
		$start = rand(0, 23);
		$password = md5($customer['last_name']);
		$password = substr($password, $start, 8);
		$password = strtolower($password);
		return $password;
	}

	
	function notify_account_created($customer){
		// musim zjistit, zda zakaznik uvedl
		// emailovou adresu, jinak nebudu mail posilat
		if ( isset($customer['email']) && !empty($customer['email']) ){
			// vytahnu si heslo ze session
			App::import('Model', 'CakeSession');
			$this->Session = &new CakeSession;
			$customer_password = $this->Session->read('cpass');
			
			// vytvorim si objekt mailu
			App::import('Vendor', 'phpmailer', array('file' => 'phpmailer/class.phpmailer.php'));
			$mail = new phpmailer();

			// uvodni nastaveni maileru
			$mail->CharSet = 'utf-8';
			$mail->Hostname = CUST_ROOT;
			$mail->Sender = CUST_MAIL;

			// nastavim adresu, od koho se poslal email
			$mail->From     = CUST_MAIL;
			$mail->FromName = "Automatické potvrzení";
			$mail->AddReplyTo(CUST_MAIL, CUST_NAME);

			// nastavim kam se posila email
			$mail->AddAddress($customer['email'], $customer['first_name'] . ' ' . $customer['last_name']);
			$mail->Subject = 'Vytvoření zákaznického účtu na ' . CUST_NAME;

			// vytvorim si emailovou zpravu
			$customer_mail = 'Vážená(ý) ' . $customer['first_name'] . ' ' . $customer['last_name'] . "\n\n";
			$customer_mail .= 'Tento email byl automaticky vygenerován a odeslán, abychom potvrdili Vaši registraci' .
			' v online obchodě http://www.' . CUST_ROOT . '/' . "\n";
			$customer_mail .= 'Váš účet byl vytvořen s těmito přihlašovacími údaji:' . "\n";
			$customer_mail .= 'LOGIN: ' . $customer['login'] . "\n";
			$customer_mail .= 'HESLO: ' . $customer_password . "\n";
			$customer_mail .= 'Pro přihlášení k Vašemu uživatelskému účtu použijte prosím přihlašovací formulář, který' .
			' najdete na adrese http://www.' . CUST_ROOT . '/customers/login ' . "\n";
			$customer_mail .= 'Pomocí Vašeho uživatelského účtu můžete operovat s uskutečněnými objednávkami, sledovat' .
			' jejich stav a vytvářet objednávky nové.' . "\n\n";
			$customer_mail .= 'Velmi si vážíme Vaší důvěry, děkujeme.' . "\n";

			$mail->Body = $customer_mail;
			$mail->Send();
		}
	}	
}
?>