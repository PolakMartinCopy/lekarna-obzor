<?php
class Address extends AppModel {

	var $name = 'Address';

	var $belongsTo = array('Customer');

	var $validate = array(
		'name' => array(
			'rule' => array('minLength', 2),
			'required' => true,
			'message' => 'Vyplňte prosím jméno a příjmení, nebo název společnosti.'
		),
		'street' => array(
			'rule' => array('minLength', 3),
			'required' => true,
			'message' => 'Vyplňte prosím název ulice.'
		),
		'zip' => array(
			'rule' => array('between', 5, 6),
			'required' => true,
			'message' => 'Vyplňte prosím správné PSČ.'
		),
		'city' => array(
			'rule' => array('minLength', 2),
			'required' => true,
			'message' => 'Vyplňte prosím název města.'
		)
	);
}
?>