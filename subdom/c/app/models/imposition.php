<?php
class Imposition extends AppModel {
	var $name = 'Imposition';
	
	var $actsAs = array('Containable');
	
	var $transactional = true;
	
	var $belongsTo = array('User', 'BusinessPartner', 'ImpositionState');
	
	var $hasMany = array(
		'ImpositionsUser' => array('dependent' => true),
		// dokument musim mazat i z disku, takze udelam oboje rucne
		'Document',
		// pozadavky na reseni chci mazat pouze nevyresene, takze taky udelam rucne
		'Solution'
	);
	
	var $hasOne = array(
		'RecursiveImposition' => array(
			'className' => 'RecursiveImposition',
			'dependent' => true,
		)
	);
	
	var $validate = array(
		'user_id' => array(
			'rule' => 'numeric',
			'allowEmpty' => false,
			'message' => 'Zadavatel musí být zadán'
		),
		'imposition_state_id' => array(
			'rule' => 'numeric',
			'allowEmpty' => false,
			'message' => 'Stav úkolu musí být vybrán'
		),
		'accomplishment_date' => array(
			'rule' => 'date',
			'allowEmpty' => false,
			'message' => 'Termín splnění musí být zadán'
		),
		'title' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Popis úkolu musí být zadán'
			)
		),
		'business_partner_id' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Obchodní partner musí být zadán'
			)
		)
	);
	
	function do_form_search($data) {
		$conditions = array();
		if (!empty($data['BusinessPartner']['name'])) {
			$conditions[] = 'BusinessPartner.name LIKE \'%%' . $data['BusinessPartner']['name'] . '%%\'';
		}
		if (!empty($data['Imposition']['user_id'])) {
			$conditions['Imposition.user_id'] = $data['Imposition']['user_id'];
		}
		if (!empty($data['ImpositionsUser']['user_id'])) {
			$conditions['ImpositionsUser.user_id'] = $data['ImpositionsUser']['user_id'];
		}
		if (!empty($data['Imposition']['description'])) {
			$conditions[] = 'Imposition.description LIKE \'%%' . $data['Imposition']['description'] . '%%\'';
		}
		
		// pocatecni datum, od kdy chci ukoly hledat
		// pokud je nastaveno, vyhledavam podle nej
		if (!empty($data['Solution']['accomplishment_date_from'])) {
			$ad_from = explode('.', $data['Solution']['accomplishment_date_from']);
			$ad_from = $ad_from[2] . '-' . $ad_from[1] . '-' . $ad_from[0];
			$conditions['Solution.accomplishment_date >='] = $ad_from;
		// pokud neni nastaveno a hledam vyresene nebo vsechny ukoly, nastavim dnesek jako pocatecni datum a hledam podle nej
		} elseif ($data['Solution']['solution_state_id'] != 2 ) {
			$conditions['Solution.accomplishment_date >='] = date('Y-m-d');
		}
		
		if (!empty($data['Solution']['accomplishment_date_to'])) {
			$ad_to = explode('.', $data['Solution']['accomplishment_date_to']);
			$ad_to = $ad_to[2] . '-' . $ad_to[1] . '-' . $ad_to[0];
		} else {
			$ad_to = $date_to = date('Y-m-d', strtotime('+6 days'));
		}
		$conditions['Solution.accomplishment_date <='] = $ad_to;
		
		if ($data['Solution']['solution_state_id'] != 0) {
			$conditions['Solution.solution_state_id'] = $data['Solution']['solution_state_id'];
		}

		return $conditions;
	}
	
	function get_impositions_users($id) {
		$impositions_users = $this->ImpositionsUser->find('all', array(
			'conditions' => array('ImpositionsUser.imposition_id' => $id),
			'contain' => array(
				'User' => array(
					'fields' => array('id', 'first_name', 'last_name')
				)
			),
			'fields' => array('id')
		));
		
		return $impositions_users;
	}
	
	function notifyNew($user) {
		App::import('Vendor', 'PHPMailer', array('file' => 'class.phpmailer.php'));
		$mail = &new PHPMailer;
		
		$mail->CharSet = 'utf-8';
		$mail->From = CUST_MAIL;
		$mail->FromName = CUST_NAME;
		$mail->Subject = 'Nové úkoly v ' . CUST_NAME;
		
		$body = 'Dobrý den,
v ' . CUST_NAME . ' Vám byly přiděleny úkoly k vyřešení.

';
		foreach ($user['impositions'] as $imposition) {
			$body .= 'Zadavatel: ' . $imposition['Imposition']['User']['first_name'] . ' ' . $imposition['Imposition']['User']['last_name'] . '
Obchodní partner: ' . $imposition['Imposition']['BusinessPartner']['name'] . ' - http://' . CUST_ROOT . '/user/business_partners/view/' . $imposition['Imposition']['BusinessPartner']['id'] . '
Téma: ' . $imposition['Imposition']['title'] . '
Popis: 
' . $imposition['Imposition']['description'] . '
-----------------------------------------------------

';
		}

		$body .= 'Detaily úkolů můžete prostudovat po přihlášení na adrese http://' . CUST_ROOT . '/.

S pozravem tým ' . CUST_NAME . '.
';
		
		$mail->Body = $body;
		
		$mail->AddAddress($user['User']['email']);
		
		return $mail->Send();
	}
	
	function notifyEnding($imposition) {
		// natahnu si sablonu
		App::import('Model', 'MailTemplate');
		$this->MailTemplate = &new MailTemplate;
		
		$submitter_template = $this->MailTemplate->find('first', array(
			'conditins' => array('MailTemplate.id' => 2),
			'contain' => array()
		));

		// dam si zadavatele do stringu
		$submitter = $imposition['User']['first_name'] . ' ' . $imposition['User']['last_name'];
		
		// dam si resitele do stringu
		$resolvers = array();
		foreach ($imposition['ImpositionsUser'] as $user) {
			$resolvers[] = $user['User']['first_name'] . ' ' . $user['User']['last_name'];
		}
		$resolvers = implode(', ', $resolvers);
		
		// zjistim pocet dni do expirace ukolu
		$date1 = date('Y-m-d');
		$date2 = $imposition['Imposition']['accomplishment_date'];
		$diff = abs(strtotime($date2) - strtotime($date1));
		$days = floor($diff/ (60*60*24));

		// sestavim email zadavateli
		$submitter_body = str_replace('%resolvers%', $resolvers, $submitter_template['MailTemplate']['content']);
		$submitter_body = str_replace('%expiration_days%', $days, $submitter_body);
		$submitter_body = str_replace('%imposition_description%', $imposition['Imposition']['description'], $submitter_body);
		$submitter_body = str_replace('%imposition_id%', $imposition['Imposition']['id'], $submitter_body);

		// poslu email zadaveli ukolu
		$submitter_mail = $this->_setMail();
		$submitter_mail->Subject = $submitter_template['MailTemplate']['subject'];
		$submitter_mail->Body = $submitter_body;
		
		$submitter_mail->addAddress($imposition['User']['email'], $imposition['User']['first_name'] . ' ' . $imposition['User']['last_name']);

		$submitter_mail->Send();
		
		$resolvers_template = $this->MailTemplate->find('first', array(
			'conditions' => array('MailTemplate.id' => 3),
			'contain' => array()
		));
		
		// sestavim email pro resitele ukolu
		$resolvers_body = str_replace('%submitter%', $submitter, $resolvers_template['MailTemplate']['content']);
		$resolvers_body = str_replace('%expiration_days%', $days, $resolvers_body);
		$resolvers_body = str_replace('%imposition_description%', $imposition['Imposition']['description'], $resolvers_body);
		$resolvers_body = str_replace('%imposition_id%', $imposition['Imposition']['id'], $resolvers_body);
		
		// poslu email resitelum ukolu
		$resolvers_mail = $this->_setMail();
		$resolvers_mail->Subject = $resolvers_template['MailTemplate']['subject'];
		$resolvers_mail->Body = $resolvers_body;
		
		foreach ($imposition['ImpositionsUser'] as $user) {
			$resolvers_mail->addAddress($user['User']['email'], $user['User']['first_name'] . ' ' , $user['User']['last_name']);
		}
		
		$resolvers_mail->Send();
		
	}
	
	function _setMail() {
		App::import('Vendor', 'PHPMailer', array('file' => 'class.phpmailer.php'));
		$mail = &new PHPMailer;
		
		$mail->CharSet = 'utf-8';
		$mail->From = CUST_MAIL;
		$mail->FromName = CUST_NAME;
		
		return $mail;
	}
	
	function show_calendar($id, $from, $controller = null, $prefix = null) {
		$calendar = new MyCalendar($id);

		//nastavim, pro co se kalendar vykresluje
		$calendar->setFrom($from);
		$calendar->setController($controller);
		$calendar->setPrefix($prefix);
		// define czech month names
		$czechMonths = array("Leden", "Únor", "Březen", "Duben", "Květen", "Červen", "Červenec", "Srpen", "Září", "Říjen", "Listopad", "Prosinec");
		$calendar->setMonthNames($czechMonths);
		// define czech day names
		$czechDays = array ("ne", "po", "út", "st", "čt", "pá", "so");
		$calendar->setDayNames($czechDays);
		// setting Monday as the start of the week
		$calendar->setStartDay(1);
		return array('calendar' => $calendar);
	}
}