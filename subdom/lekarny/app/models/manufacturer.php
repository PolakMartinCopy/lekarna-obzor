<?php
class Manufacturer extends AppModel {

	var $name = 'Manufacturer';
	var $validate = array(
		'name' => array(
			'not_empty' => array(
				'rule' => VALID_NOT_EMPTY,
				'required' => true,
				'message' => 'Pole pro název výrobce nesmí zůstat prázdné!'
			),
			'unique' => array(
				'rule' => 'isUnique',
				'message' => 'Tento výrobce již v databázi existuje'
			)
		),
		'www_address' => array(
			'rule' => array('url'),
			'allowEmpty' => true,
			'message' => 'Uveďte www adresu ve správném formátu, nebo nechte pole prázdné.'
		)
	);
}
?>