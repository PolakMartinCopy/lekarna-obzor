<?php
class RecursiveImposition extends AppModel {
	var $name = 'RecursiveImposition';
	
	var $belongsTo = array(
		'Imposition' => array(
			'className' => 'Imposition',
			'foreignKey' => 'imposition_id'
		),
		'ImpositionPeriod' => array(
			'className' => 'ImpositionPeriod',
			'foreignKey' => 'imposition_period_id'
		)
	);
}