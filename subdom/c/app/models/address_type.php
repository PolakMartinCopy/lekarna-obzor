<?php
class AddressType extends AppModel {
	var $name = 'AddressType';
	
	var $actsAs = array('Containable');
	
	var $hasMany = array('Address');
	
	var $validate = array(
		'name' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Pole Název nesmí zůstat prázdné'
			)
		),
		'just_one' => array(
			'bool' => array(
				'rule' => 'boolean',
				'message' => 'Nesprávná hodnota pole Jen jedna adresa'
			)
		)
	);
}