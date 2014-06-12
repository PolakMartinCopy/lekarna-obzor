<?
class Rep extends AppModel {
	var $name = 'Rep';
	
	var $actsAs = array('Containable');
	
	var $hasMany = array('RepArea');
	
	var $validate = array(
		'last_name' => array(
			'rule' => 'notEmpty',
			'message' => 'Pole Příjmení musí být neprázdné'
		),
		'login' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Pole Login musí být neprázdé'
			),
			'isUnique' => array(
				'rule' => 'isUnique',
				'message' => 'Tento login již v databázi existuje, zvolte jiný'
			)
		),
		'password' => array(
			'rule' => 'notEmpty',
			'message' => 'Pole Heslo musí být neprázdné'
		),
		'email' => array(
			'rule' => 'email',
			'allowEmpty' => true,
			'message' => 'Pole Email musí obsahovat validní emailovou adresu'
		)
	);
	
	function create_password($email) {
		return substr(md5($email . Configure::read('Security.salt')), 0, 8);
	}
	
	function create_login($last_name, $email) {
		return substr(md5($last_name . Configure::read('Security.salt') . $email), 0, 8);
	}
	
	function notify_rep($data, $password) {
		// poslu nove pridanymu repovi email na jeho adresu s jeho prihlasovacima udajama
		App::import('Vendor', 'PHPMailer', array('file' => 'class.phpmailer.php'));
		$mail = &new PHPMailer;
		
		$mail->CharSet = 'UTF-8';
		$mail->Hostname = 'lekarny.lekarna-obzor.cz';
		$mail->Sender = 'no-reply@lekarna-obzor.cz';
			
		$mail->From = 'no-reply@lekarna-obzor.cz';
		$mail->FromName = 'Automatický pošťák - lekarny.lekarna-obzor.cz';
		$mail->ReplyTo = 'no-reply@lekarna-obzor.cz';
			
		$mail->Body = '
Dobrý den,

byl Vám vytvořen účet pro přístup do sekce pro reprezentanty společnosti Pharmacorp CZ s.r.o.

Přihlašovací údaje:
login: ' . $data['Rep']['login'] . '
heslo: ' . $password . '

Do systému se můžete přihlásit na adrese http://lekarny.lekarna-obzor.cz/rep/reps/login.

V případě jakýchkoli problémů prosím pošlete své připomínky na emailové adresy webmaster@lekarna-obzor.cz a lekarny@lekarna-obzor.cz.

Děkujeme za Váš čas, s pozdravem tým Pharmacorp CZ s.r.o.

--
Pharmacorp CZ s.r.o.
http://www.lekarna-obzor.cz/
';
		$mail->Subject = 'Přihlašovací údaje - Pharmacorp CZ s.r.o.';
		$mail->AddAddress($data['Rep']['email'], $data['Rep']['first_name'] . ' ' . $data['Rep']['last_name']);
			
		return $mail->Send();
	}
}
?>