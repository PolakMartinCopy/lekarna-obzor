<?php
class BusinessSessionState extends AppModel {
	var $name = 'BusinessSessionState';
	
	var $actsAs = array('Containable');
	
	var $hasMany = array('BusinessSession');
	
	var $validate = array(
		'name' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Název musí být vyplněn'
			)
		)
	);
}