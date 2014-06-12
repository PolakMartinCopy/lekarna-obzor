<?
class Redirect extends AppModel{
	var $name = 'Redirect';

	var $validate = array(
		'target_uri' => array(
			'rule' => array('minLength', 1), 
			'message' => 'Cílové URI nesmí zůstat prázné.'
		),
		'request_uri' => array(
			'minLenght' => array(
				'rule' => array('minLength', 2),
				'message' => 'Zdrojové URI nesmí zůstat prázné.'
			),
			'isUnique' => array(
				'rule' => 'isUnique',
				'message' => 'Toto URI již v databázi figuruje.'
			)

		)
	);
	
	function check($uri){
		$r = $this->find('first', array(
			'conditions' => array(
				'request_uri' => $uri
			)
		));

		if ( !empty($r) ){
			return $r;
		}
		return false;
	}
}
?>