<?php
class UserRegion extends AppModel {
	var $name = 'UserRegion';
	
	var $actsAs = array('Containable');
	
	var $belongsTo = array('User');
	
	var $validate = array(
		'user_id' => array(
			'rule' => 'numeric',
			'allowEmpty' => false,
			'message' => 'Uživatel musí být vybrán'
		),
		'zip' => array(
			'numeric' => array(
				'rule' => 'numeric',
				'allowEmpty' => false,
				'message' => 'Pole PSČ musí obsahovat pouze číslice'
			),
			'fiveChars' => array(
				'rule' => array('between', 5, 5),
				'message' => 'PSČ musí obsahovat 5 znaků'
			),
			'isUnique' => array(
				'rule' => 'isUnique',
				'message' => 'Okres s tímto PSČ je již přidělen některému uživateli'
			)
		),
	);
	
	function do_form_search($conditions, $data) {
		if (!empty($data['UserRegion']['name'])) {
			$conditions[] = 'UserRegion.name LIKE \'%%' . $data['UserRegion']['name'] . '%%\'';
		}
		if (!empty($data['UserRegion']['zip'])) {
			$conditions[] = 'UserRegion.zip LIKE \'%%' . $data['UserRegion']['zip'] . '%%\'';
		}
		if (!empty($data['User']['first_name'])) {
			$conditions[] = 'User.first_name LIKE \'%%' . $data['User']['first_name'] . '%%\'';
		}
		if (!empty($data['User']['last_name'])) {
			$conditions[] = 'User.last_name LIKE \'%%' . $data['User']['last_name'] . '%%\'';
		}
		return $conditions;
	}
}