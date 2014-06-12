<?
class Company extends AppModel{
	var $name = 'Company';
	
	var $actsAs = array('Containable');
	
	var $validate = array(
		'person_first_name' => array(
			'rule' => 'notEmpty',
			'message' => 'Vyplňte prosím jméno odpovědné osoby.'
		),
		'person_last_name' => array(
			'rule' => 'notEmpty',
			'message' => 'Vyplňte prosím příjmení odpovědné osoby.'
		),
		'person_phone' => array(
			'rule' => 'notEmpty',
			'message' => 'Vyplňte prosím telefonní kontakt na odpovědnou osobu.'
		),
		'person_email' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Vyplňte prosím emailovou adresu odpovědné osoby.'
			),
			'email' => array(
				'rule' => array('email', false), // false - nechci kontrolovat host name
				'message' => 'Vyplněná emailová adresa není platná.'
			)
		),

		'name' => array(
			'rule' => 'notEmpty',
			'message' => 'Vyplňte prosím název společnosti.'
		),
		'ico' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Vyplňte prosím IČO společnosti.'
			)/*,
			'isUnique' => array(
				'rule' => 'isUnique',
				'message' => 'Společnost s tímto IČem už v databází existuje'
			)*/
		),
		
		'delivery_name' => array(
			'rule' => 'notEmpty',
			'message' => 'Vyplňte prosím adresáta.'
		),
		'delivery_street' => array(
			'rule' => 'notEmpty',
			'message' => 'Vyplňte prosím ulici.'
		),
		'delivery_street_number' => array(
			'rule' => 'notEmpty',
			'message' => 'Vyplňte prosím číslo popisné.'
		),
		'delivery_postal_code' => array(
			'rule' => 'notEmpty',
			'message' => 'Vyplňte prosím psč.'
		),
		'delivery_city' => array(
			'rule' => 'notEmpty',
			'message' => 'Vyplňte prosím město.'
		),
		
		'payment_name' => array(
			'rule' => 'notEmpty',
			'message' => 'Vyplňte prosím název společnosti.'
		),
		'payment_street' => array(
			'rule' => 'notEmpty',
			'message' => 'Vyplňte prosím ulici.'
		),
		'payment_street_number' => array(
			'rule' => 'notEmpty',
			'message' => 'Vyplňte prosím číslo popisné.'
		),
		'payment_postal_code' => array(
			'rule' => 'notEmpty',
			'message' => 'Vyplňte prosím psč.'
		),
		'payment_city' => array(
			'rule' => 'notEmpty',
			'message' => 'Vyplňte prosím město.'
		),
		
		'login' => array(
			'isUnique' => array(
				'rule' => 'isUnique',
				'message' => 'Tento login je již používán někým jiným, zvolte si prosím jiný login.'
			),
			'minLength' => array(
				'rule' => array('minLength', 10),
				'message' => 'Login musí být sestaven minimálně z 10ti znaků.'
			)
		),
		'password' => array(
			'rule' => array('minLength', 10),
			'message' => 'Heslo musí být sestaveno minimálně z 10ti znaků.'
		)
	);
	
	function generate_login(){
		$random_index = rand(0, 21);
		$login = md5(mktime());
		$login = substr($login, $random_index, 10);
		$login = strtoupper($login);
		
		if ( $this->hasAny(array('login' => $login)) ){
			$login = $this->generate_login();
		}
		
		return $login;
	}
	
	function generate_password(){
		$random_index = rand(0, 21);
		$password = md5(mktime());
		$password = substr($password, $random_index, 10);
		$password = strtoupper($password);
		
		return $password;
	}
	
	function notify_new_order($order_id, $company_id){
		$company = $this->find('first', array(
			'conditions' => array(
				'Company.id' => $company_id
			),
			'contain' => array()
		));

		App::import('Vendor', 'phpmailer', array('file' => 'phpmailer/class.phpmailer.php'));
		$mail = &new phpmailer;
		
		// uvodni nastaveni maileru
		$mail->CharSet = 'UTF-8';
		$mail->Hostname = 'lekarny.lekarna-obzor.cz';
		$mail->Sender = 'lekarny@lekarna-obzor.cz';
		
		// nastavim adresu, od koho se poslal email
		$mail->From     = "no-reply@lekarna-obzor.cz";
		$mail->FromName = "Automatické potvrzení";
		$mail->AddReplyTo("lekarny@lekarna-obzor.cz","Pharmacorp CZ s.r.o.");
		
		// nastavim kam se posila email
		$mail->AddAddress($company['Company']['person_email'], $company['Company']['person_first_name'] . ' ' . $company['Company']['person_last_name']);

		$mail->Subject = 'Potvrzení o přijetí nové objednávky s číslem ' . $order_id . ' do systému Pharmacorp CZ s.r.o.';

		$mail->Body = "Vážený zákazníku,
právě byla uložena Vaše nová objednávka do objednávkového systému společnosti Pharmacorp CZ s.r.o. pod číslem " . $order_id . "
Stav a průběh objednávky můžete kontrolovat v objednávkovém systému.
Pro přihlášení do systému použijte následující URL a Vaše přihlašovací údaje:
http://lekarny.lekarna-obzor.cz/users/companies/login

--
Pharmacorp CZ s.r.o.
http://lekarny.lekarna-obzor.cz/
http://www.lekarna-obzor.cz/
tel: 533 101 360
mail: lekarny@lekarna-obzor.cz
";
		
		return $mail->Send();
	}
	
	function notify_new_password($company, $password){
		App::import('Vendor', 'phpmailer', array('file' => 'phpmailer/class.phpmailer.php'));
		$mail = &new phpmailer;
		
		// uvodni nastaveni maileru
		$mail->CharSet = 'UTF-8';
		$mail->Hostname = 'lekarny.lekarna-obzor.cz';
		$mail->Sender = 'lekarny@lekarna-obzor.cz';
		
		// nastavim adresu, od koho se poslal email
		$mail->From     = "no-reply@lekarna-obzor.cz";
		$mail->FromName = "Automatické potvrzení";
		$mail->AddReplyTo("admin@lekarna-obzor.cz","Pharmacorp CZ s.r.o.");
		
		// nastavim kam se posila email
		$mail->AddAddress($company['Company']['person_email'], $company['Company']['person_first_name'] . ' ' . $company['Company']['person_last_name']);
		$mail->Subject = 'Změna hesla do objednávkového systému Pharmacorp CZ s.r.o.';
		
		$mail->Body = "Vážený zákazníku,
Vaše heslo pro přístup do objednávkového systému společnosti Pharmacorp CZ s.r.o. bylo změněno. Nyní se můžete přihlásit
do systému na URL http://lekarny.lekarna-obzor.cz/users/companies/login

Vaše přihlašovací údaje jsou:
login: " . $company['Company']['login'] . "
heslo: " . $password . "

--
Pharmacorp CZ s.r.o.
http://lekarny.lekarna-obzor.cz/
http://www.lekarna-obzor.cz/
tel: 533 101 360
mail: lekarny@lekarna-obzor.cz
";
		return $mail->Send();
	}
	
	function send_notification($company){
		App::import('Vendor', 'phpmailer', array('file' => 'phpmailer/class.phpmailer.php'));
		$mail = &new phpmailer;
		
		// uvodni nastaveni maileru
		$mail->CharSet = 'UTF-8';
		$mail->Hostname = 'lekarny.lekarna-obzor.cz';
		$mail->Sender = 'lekarny@lekarna-obzor.cz';
		
		// nastavim adresu, od koho se poslal email
		$mail->From     = "no-reply@lekarna-obzor.cz";
		$mail->FromName = "Automatické potvrzení";
		$mail->AddReplyTo("lekarny@lekarna-obzor.cz","Pharmacorp CZ s.r.o.");
		
		// nastavim kam se posila email
		$mail->AddAddress($company['Company']['person_email'], $company['Company']['person_first_name'] . ' ' . $company['Company']['person_last_name']);
		$mail->Subject = 'Schválení registrace do objednávkového systému Pharmacorp CZ s.r.o.';
		
		$mail->Body = "Vážený zákazníku,
Vaše registrace do objednávkového systému společnosti Pharmacorp CZ s.r.o. byla schválena. Nyní se můžete přihlásit
do systému na URL http://lekarny.lekarna-obzor.cz/users/companies/login

Vaše přihlašovací údaje jsou:
login: " . $company['Company']['login'] . "
heslo: " . $company['Company']['password'] . "

--
Pharmacorp CZ s.r.o.
http://lekarny.lekarna-obzor.cz/
http://www.lekarna-obzor.cz/
tel: 533 101 360
mail: lekarny@lekarna-obzor.cz
";
		return $mail->Send();
	}
	
	function send_rejection($company) {
		App::import('Vendor', 'phpmailer', array('file' => 'phpmailer/class.phpmailer.php'));
		$mail = &new phpmailer;
		
		// uvodni nastaveni maileru
		$mail->CharSet = 'UTF-8';
		$mail->Hostname = 'lekarny.lekarna-obzor.cz';
		$mail->Sender = 'lekarny@lekarna-obzor.cz';
		
		// nastavim adresu, od koho se poslal email
		$mail->From     = "no-reply@lekarna-obzor.cz";
		$mail->FromName = "Automatické potvrzení";
		$mail->AddReplyTo("lekarny@lekarna-obzor.cz","Pharmacorp CZ s.r.o.");
		
		// nastavim kam se posila email
		$mail->AddAddress($company['Company']['person_email'], $company['Company']['person_first_name'] . ' ' . $company['Company']['person_last_name']);
		$mail->Subject = 'Zablokování účtu v objednávkovém systému Pharmacorp CZ s.r.o.';
		
		$mail->Body = "Vážený zákazníku,
Váš přístup do objednávkového systému Pharmacorp CZ s.r.o. byl zablokován. Pro zjištění důvodů blokace Vašeho účtu nás prosím kontaktujte (viz níže).

--
Pharmacorp CZ s.r.o.
http://lekarny.lekarna-obzor.cz/
http://www.lekarna-obzor.cz/
tel: 533 101 360
mail: lekarny@lekarna-obzor.cz
";
		return $mail->Send();
	}

	function notify_new_registration($data) {
		App::import('Vendor', 'phpmailer', array('file' => 'phpmailer/class.phpmailer.php'));
		$mail = &new phpmailer;
		
		// uvodni nastaveni maileru
		$mail->CharSet = 'UTF-8';
		$mail->Hostname = 'lekarny.lekarna-obzor.cz';
		$mail->Sender = 'lekarny@lekarna-obzor.cz';
		
		// nastavim adresu, od koho se poslal email
		$mail->From     = "no-reply@lekarna-obzor.cz";
		$mail->FromName = "Automatické potvrzení";
		$mail->AddReplyTo("lekarny@lekarna-obzor.cz","Pharmacorp CZ s.r.o.");
		
		// nastavim kam se posila email
		$mail->AddAddress('lekarny@lekarna-obzor.cz');
//		$mail->AddBCC('vlado@tovarnak.com');
		$mail->Subject = 'Byla přijata nová registrace';
		
		$mail->Body = "Na lekarny.lekarna-obzor.cz byla přijata nová registrace k vyřízení.
Jméno: " . $data['Company']['person_first_name'] . " 
Příjmení: " . $data['Company']['person_last_name'] . "
Telefon: " . $data['Company']['person_phone'] . "
Email: " . $data['Company']['person_email'] . "
Název společnosti: " . $data['Company']['name'] . "

Registraci můžete po přihlášení schválit či zamítnout zde:
http://lekarny.lekarna-obzor.cz/admin/companies/index/authorized:0
";
		return $mail->Send();
	}
}
?>