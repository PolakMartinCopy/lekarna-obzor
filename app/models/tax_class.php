<?php
class TaxClass extends AppModel {

	var $name = 'TaxClass';
	var $validate = array(
		'name' => array(
			'rule' => array('minLength', 1),
			'required' => true,
			'message' => 'Musíte vyplnit název daňové třídy.'
		),
		'value' => array(
			'rule' => 'numeric',
			'required' => true,
			'message' => 'Musíte vyplnit hodnotu daně.'
		)
	);

	//The Associations below have been created with all possible keys, those that are not needed can be removed
}
?>