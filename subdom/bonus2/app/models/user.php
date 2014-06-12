<?php
class User extends AppModel {
	var $name = 'User';
	
	var $actsAs = array('Containable');
	
	var $hasMany = array('Sale', 'PayOut');
	
	var $validate = array(
		'login' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Zadejte login uživatele.'
			)
		),
		'password' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Zadejte heslo uživatele.'
			)
		)
	);
	
	var $virtualFields = array(
		'name' => "CONCAT(User.first_name, ' ', User.last_name)"
	);
}
?>