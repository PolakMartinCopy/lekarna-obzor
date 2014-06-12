<?php
class Unit extends AppModel {
	var $name = 'Unit';
	
	var $actsAs = array('Containable');
	
	var $hasMany = array('Product');
	
	var $validate = array(
		'name' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Zadejte název jednotky'
			)
		),
		'shortcut' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Zadejte zkratku jednotky'
			)
		)
	);
}