<?php
class AnniversaryType extends AppModel {
	var $name = 'AnniversaryType';
	
	var $actsAs = array('Containable');
	
	var $hasMany = array('Anniversary');
	
	var $validate = array(
		'name' => array(
			'rule' => 'notEmpty',
			'message' => 'Pole Název nesmí zůstat prázdné'
		)
	);
}