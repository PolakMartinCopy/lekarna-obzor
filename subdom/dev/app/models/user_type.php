<?php
class UserType extends AppModel {
	var $name = 'UserType';
	
	var $actsAs = array(
		'Containable',
		'Acl' => array('type' => 'requester')
	);
	
	var $hasMany = array('User');
	
	var $validate = array(
		'name' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Název musí být vyplněn.'
			)
		)
	);
	
	function parentNode() {
		return false;
	}
}