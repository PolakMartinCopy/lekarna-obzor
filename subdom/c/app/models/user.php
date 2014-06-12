<?php
class User extends AppModel {
	var $name = 'User';
	
	var $actsAs = array(
		'Containable',
		'Acl' => array('type' => 'requester')
	);
	
	var $belongsTo = array('UserType');
	
	var $hasMany = array(
		'BusinessPartner',
		'UserRegion',
		'Imposition',
		'ImpositionsUser' => array('dependent' => true),
		'BusinessSession', // vedouci obchodniho jednani
		'BusinessSessionsUser' => array('dependent' => true), // prizvany obchodnik na jednani
		'Transaction'
	);
	
	var $validate = array(
		'first_name' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Křestní jméno uživatele musí být zadáno'
			)
		),
		'last_name' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Příjmení uživatele musí být zadáno'
			)
		),
		'phone' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Pole Telefon musí být vyplněno'
			)
		),
		'email' => array(
			'email' => array(
				'rule' => array('email', true),
				'message' => 'Zadejte platnou emailovou adresu'
			),
			'isUnique' => array(
				'rule' => 'isUnique',
				'message' => 'Zadaná emailová adresa již v systému existuje, zadejte prosím jinou.'
			)
		),
		'login' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Login musí být zadán'
			),
			'isUnique' => array(
				'rule' => 'isUnique',
				'message' => 'Zadaný login již v databázi existuje, vložte prosím jiný'
			)
		),
		'password' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Heslo musí být zadáno'
			)
		)
	);
	
	function phoneOrEmail() {
		return !(empty($this->data['User']['phone']) && empty($this->data['User']['email']));
	}
	
	function hashPasswords($data) {
		if (!empty($data['User']['password'])) {
			$data['User']['password'] = md5($data['User']['password']);
		}
		return $data;
	}
	
	function parentNode() {
		if (!$this->id && empty($this->data)) {
			return null;
		}
		if (isset($this->data['User']['user_type_id'])) {
			$groupId = $this->data['User']['user_type_id'];
		} else {
			$groupId = $this->field('user_type_id');
		}
		if (!$groupId) {
			return null;
		} else {
			return array('UserType' => array('id' => $groupId));
		}
	}
	
	function generatePassword($user){
		// vytahnu si osm znaku z md5ky,
		// s nahodnym startem
		$start = rand(0, 23);
		$password = md5($user['User']['last_name']);
		$password = substr($password, $start, 8);
		$password = strtolower($password);
		return $password;
	}
	
	function createHash($user) {
		return md5($user['User']['id'] . $user['User']['first_name'] . Configure::read('Security.salt') . $user['User']['last_name']);
	}
	
	function sendHash($user, $hash) {
		App::import('Vendor', 'PHPMailer', array('file' => 'class.phpmailer.php'));
		$mail = new PHPMailer;
		
		$mail->CharSet = 'utf-8';
		$mail->Sender = CUST_MAIL;
		$mail->From = CUST_MAIL;
		$mail->FromName = CUST_NAME;
		$mail->AddAddress($user['User']['email']);
		$mail->Subject = 'Potvrzení žádosti o změnu přístupových údajů do ' . CUST_NAME;
		
		// vytvorim si emailovou zpravu
		$customer_mail = 'Vážená(ý) ' . $user['User']['first_name'] . ' ' . $user['User']['last_name'] . "\n\n";
		$customer_mail .= 'Tento email byl automaticky vygenerován a odeslán na Vaši žádost o změnu hesla' .
		' do ' . CUST_NAME . "\n";
		$customer_mail .= 'Pro potvrzení žádosti klikněte na následující odkaz:' . "\n";
		$customer_mail .= 'http://' . CUST_ROOT . '/user/users/confirm/' . $user['User']['id'] . '/' . $hash . "\n";
		$customer_mail .= 'V případě nefuknčnosti jej zkopírujte a vložte do příkazové řádky Vašeho prohlížeče.' . "\n";

		$mail->Body = $customer_mail;
		if (!$mail->Send()) {
			die($mail->ErrorInfo);
		}
		return true;
	}
	
	function sendPassword($password, $user) {
		App::import('Vendor', 'PHPMailer', array('file' => 'class.phpmailer.php'));
		$mail = new PHPMailer;
		
		$mail->CharSet = 'utf-8';
		$mail->Sender = CUST_MAIL;
		$mail->From = CUST_MAIL;
		$mail->FromName = CUST_NAME;
		$mail->AddAddress($user['User']['email']);
		$mail->Subject = 'Změna přístupových údajů do ' . CUST_NAME;
		
		// vytvorim si emailovou zpravu
		$customer_mail = 'Vážená(ý) ' . $user['User']['first_name'] . ' ' . $user['User']['last_name'] . "\n\n";
		$customer_mail .= 'Tento email byl automaticky vygenerován a odeslán na Vaši žádost o vygenerování nového hesla' .
		' do ' . CUST_NAME . "\n";
		$customer_mail .= 'Pro přihlášení nyní použijte následující údaje:' . "\n";
		$customer_mail .= 'LOGIN: ' . $user['User']['login'] . "\n";
		$customer_mail .= 'HESLO: ' . $password . "\n";

		$mail->Body = $customer_mail;
		if (!$mail->Send()) {
			die($mail->ErrorInfo);
		}
		return true;
	}
	
	function do_form_search($conditions, $data) {
		if (!empty($data['User']['first_name'])) {
			$conditions[] = 'User.first_name LIKE \'%%' . $data['User']['first_name'] . '%%\'';
		}
		if (!empty($data['User']['last_name'])) {
			$conditions[] = 'User.last_name LIKE \'%%' . $data['User']['last_name'] . '%%\'';
		}
		if (!empty($data['User']['login'])) {
			$conditions[] = 'User.login LIKE \'%%' . $data['User']['login'] . '%%\'';
		}
		if (!empty($data['User']['phone'])) {
			$conditions[] = 'User.phone LIKE \'%%' . $data['User']['phone'] . '%%\'';
		}
		if (!empty($data['User']['email'])) {
			$conditions[] = 'User.email LIKE \'%%' . $data['User']['email'] . '%%\'';
		}
		if (!empty($data['User']['user_type_id'])) {
			$conditions[] = 'User.user_type_id = ' . $data['User']['user_type_id'];
		}
		
		return $conditions;
	}
}