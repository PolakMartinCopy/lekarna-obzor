<?
class Postman extends AppModel{
	var $name = 'Postman';
	
	var $useTable = false;

	var $CharSet = 'windows-1250';
	var $Hostname = 'obchod.mte.cz';
	var $Sender = 'lekarna@mte.cz';
	var $From = 'postman@mte.cz';
	var $FromName = 'Automatický pošťák';
	var $ReplyTo = 'lekarna@mte.cz';

	function initialize($mailer_object){
		$mailer_object->CharSet = $this->CharSet = 'windows-1250';
		$mailer_object->Hostname = $this->Hostname = 'obchod.mte.cz';
		$mailer_object->Sender = $this->Sender = 'lekarna@mte.cz';
		$mailer_object->From = $this->From = 'postman@mte.cz';
		$mailer_object->FromName = $this->FromName = 'Automatický pošťák';
		$mailer_object->ReplyTo = $this->ReplyTo = 'lekarna@mte.cz';
		
		return $mailer_object;
	}

	function send($mail_template_id = null, $objects = array(), $recipient = array()){
		// @TODO: rozpracovany model, mel by obsluhovat veskere rozesilani emailu
		// zatim je udelany pouze na zmenu stavu objednavky
		include 'class.phpmailer.php';
		$ppm = &new phpmailer;
		$ppm = $this->initialize($ppm);

		App::import('Model', array('MailTemplate'));
		$this->MailTemplate = &new MailTemplate;
		$this->MailTemplate->id = $mail_template_id;
		$mail_template = $this->MailTemplate->read();

		$replace_data = array();
		foreach ( $objects as $object ){
			App::import('Model', array($object['name']));
			$this->{$object['name']} = &new $object['name'];
			$this->{$object['name']}->recursive = -1;
			$this->{$object['name']}->id = $object['id'];
			$replace_data += $this->{$object['name']}->read();
		}

		$matches = '';
		preg_match_all("/%(.*)%/U", $mail_template['MailTemplate']['content'], $matches, PREG_SET_ORDER);

		foreach ( $matches as $match ){
			$model = explode(".", $match[1]);
			$column = $model[1];
			$model = $model[0];

			$mail_template['MailTemplate']['content'] = str_replace("{$match[0]}", $replace_data[$model][$column], $mail_template['MailTemplate']['content']);
		}
//		debug($matches);

//		$mail_template['MailTemplate']['subject'] = eregi_replace("", );

//		debug($replace_data);
		debug($mail_template['MailTemplate']['content']);
		die();
	}
}
?>