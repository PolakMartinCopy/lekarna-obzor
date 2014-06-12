<?php 
class RepAttribute extends AppModel {
	var $name = 'RepAttribute';
	
	var $actsAs = array('Containable');
	
	var $belongsTo = array(
		'Rep' => array(
			'foreignKey' => 'rep_id'
		)
	);
	
	var $validate = array(
		'street' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Pole Ulice nesmí zůstat prázdné'
			)
		),
		'street_number' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Pole Číslo popisné nesmí zůstat prázdné'
			)
		),
		'city' => array(
			'notEmpty' => array(
		 		'rule' => 'notEmpty',
		 		'message' => 'Pole Město nesmí zůstat prázdné'
			)
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
			)
		)
	);
}
?>