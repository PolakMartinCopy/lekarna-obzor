<?php
class BusinessSessionType extends AppModel {
	var $name = 'BusinessSessionType';
	
	var $actsAs = array('Containable');
	
	var $hasMany = 'BusinessSession';
	
	var $validate = array(
		'name' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Název musí být vyplněn'
			)
		)
	);
}