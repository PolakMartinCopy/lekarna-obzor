<?php
class Comment extends AppModel {
	var $name = "Comment";
	
	var $actsAs = array('Containable');

	var $belongsTo = array('Product', 'Administrator');

	var $validate = array(
 		'author' => array(
			'rule' => array('minLength', 1),
			'message' => 'Zadejte vaše jméno, nebo přezdívku.'
		),
		'email' => array(
			'email' => array(
				'rule' => array('email', true),
				'message' => 'Vyplňte prosím existující emailovou adresu, abychom Vám mohli odeslat odpověď na mail.',
				'required' => true
			)
		),
		'subject' => array(
			'rule' => array('minLength', 1),
			'message' => 'Zadejte předmět komentáře / dotazu.'
		),
		'body' => array(
			'rule' => array('minLength', 1),
			'message' => 'Zadejte tělo komentáře / dotazu.'
		)
	);

	/**
	 * Rozeznava retezce, ktere indikuji, ze dany komentar je SPAM.
	 * 
	 * @return boolean
	 */
	function is_spam($content){
		// predpoklad, ze komentar neni spam
		$result = false;
		
		// retezce, ktere indikuji SPAM
		$patterns = array(
			0 => "\[\/url\]",
			"\[\/link\]",
			"\[url=(.*)\]",
			"\[link=(.*)\]",
			"cialis",
			"penis",
			"phentermine",
			"levitra",
			"adipex",
			"acomplia",
			"viagra",
			"reductil",
			"klonopin",
			"lasix",
			"potassium",
			"insurance",
			"propecia",
			"aciphex",
			"xanax",
			"tramadol",
			"pharmacy"
		);

		// zjistim, zda se jedna o SPAM
		for ( $i = 0; $i < count($patterns); $i++ ){
			if ( eregi($patterns[$i], $content) ){
				$result = true;
			}
		}
		return $result;
	}
	
	/**
	 * Notifikace administratoru o novem dotazu v obchode.
	 *
	 * @return unknown
	 */
	function notify_new_comment(){
		// natahnu si mailovaci skript
		App::import('Vendor', 'PHPMailer', array('file' => 'class.phpmailer.php'));
		$mail = &new PHPmailer;

		// uvodni nastaveni
		$mail->CharSet = 'utf-8';
		$mail->Hostname = CUST_ROOT;
		$mail->Sender = CUST_MAIL;
		
		// nastavim adresu, od koho se poslal email
		$mail->From     = CUST_MAIL;
		$mail->FromName = "Automatické potvrzení";
		
		$mail->AddReplyTo(CUST_MAIL, CUST_NAME);

		$mail->AddAddress(CUST_MAIL, CUST_NAME);
//		$mail->AddBCC("vlado@tovarnak.com", "Vlado Tovarnak");

		$mail->Subject = 'E-SHOP (' . CUST_ROOT . ') - NOVÝ DOTAZ';
		$mail->Body = 'Právě byl položen nový dotaz.' . "\n";
		$mail->Body .= 'Pro jeho zobrazení a odpověď se přihlašte v administraci obchodu: http://www.' . CUST_ROOT . '/admin/' . "\n";

		return $mail->Send();
	}

	/**
	 * Notifikace o odpovedi na dotaz zakaznika.
	 *
	 * @param unknown_type $id
	 * @return unknown
	 */
	function notify_answer($comment){
		
		if ( isset( $comment['Comment']['email'] ) ){
			// natahnu si mailovaci skript
			App::import('Vendor', 'PHPMailer', array('file' => 'class.phpmailer.php'));
			$mail = &new PHPmailer;

			// uvodni nastaveni
			$mail->CharSet = 'utf-8';
			$mail->Hostname = CUST_ROOT;
			$mail->Sender = CUST_MAIL;
			
			// nastavim adresu, od koho se poslal email
			$mail->From     = CUST_MAIL;
			$mail->FromName = "Automatické potvrzení";
			
			$mail->AddReplyTo(CUST_MAIL, CUST_NAME);

			$mail->AddAddress($comment['Comment']['email'] , $comment['Comment']['author']);
//			$mail->AddBCC("vlado@tovarnak.com", "Vlado Tovarnak");
	
			$mail->Subject = $comment['Comment']['subject'] . " - odpověď na váš dotaz";
			$mail->Body = 'Dobrý den,' . "\n";
			$mail->Body .= 'Váš dotaz v následujícím znění:' . "\n\n";
			$mail->Body .= $comment['Comment']['body']. "\n\n";
			$mail->Body .= 'byl zodpovězen, odpověď naleznete níže:' . "\n\n";
			$mail->Body .= $comment['Comment']['reply']. "\n\n";
			
			$mail->Body .= 's pozdravem' . "\n" . 'team internetového obchodu ' . CUST_NAME;
			return $mail->Send();
		}
		return false;
	}
}
?>