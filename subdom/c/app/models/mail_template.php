<?
class MailTemplate extends AppModel{
	var $name = 'MailTemplate';
	
	var $validate = array(
		'subject' => array(
			'minLength' => array(
				'rule' => array('minLength', 1),
				'message' => 'Předmět emailu nesmí zůstat prázdný.'
			)
		),
		'content' => array(
			'minLength' => array(
				'rule' => array('minLength', 1),
				'message' => 'Obsah mailu nesmí zůstat prázdný.'
			)
		)
	);
}
?>