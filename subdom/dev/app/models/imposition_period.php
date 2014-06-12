<?php 
class ImpositionPeriod extends AppModel {
	var $name = 'ImpositionPeriod';
	
	var $actsAs = array('Containable');
	
	var $hasMany = array('RecursiveImposition');
	
	var $validate = array(
		'name' => array(
			'rule' => 'notEmpty',
			'message' => 'Pole Název musí být neprázdné'
		),
		'interval' => array(
			'rule' => 'notEmpty',
			'message' => 'Pole Interval musí být neprázdné'
		)
	);
}
?>