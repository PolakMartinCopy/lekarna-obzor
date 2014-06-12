<?php // reprezentuje resitele pridane k ukolum
class ImpositionsUser extends AppModel {
	var $name = 'ImpositionsUser';
	
	var $actsAs = array('Containable');
	
	var $belongsTo = array('User', 'Imposition');
	
	var $validate = array(
		'user_id' => array(
			'rule' => 'numeric',
			'allowEmpty' => false,
			'message' => 'Řešitel musí být zadán'
		)
/*		'imposition_id' => array(
			'rule' => 'numeric',
			'allowEmpty' => false,
			'message' => 'Úkol musí být zadán'
		)*/
	);
}