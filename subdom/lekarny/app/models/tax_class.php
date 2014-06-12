<?php
class TaxClass extends AppModel {

	var $name = 'TaxClass';
	var $validate = array(
		'name' => array(
			'rule' => VALID_NOT_EMPTY,
			'required' => true,
			'message' => 'Musíte vyplnit název daňové třídy.'
		),
		'value' => array(
			'rule' => VALID_NUMBER,
			'required' => true,
			'message' => 'Musíte vyplnit hodnotu daně.'
		)
	);
	
	var $displayField = 'value';
}
?>