<?php
class ImpositionState extends AppModel {
	var $name = 'ImpositionState';
	
	var $actsAs = array('Containable');
	
	var $hasMany = array('Imposition');
	
	var $validate = array(
		'name' => array(
			'rule' => 'notEmpty',
			'message' => 'Název musí zůstat neprázdný'
		)
	);
}