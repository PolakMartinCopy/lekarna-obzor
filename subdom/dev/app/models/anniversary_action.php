<?php
class AnniversaryAction extends AppModel {
	var $name = 'AnniversaryAction';
	
	var $actsAs = array('Containable');
	
	var $hasMany = array('Anniversary');
	
	var $validate = array(
		'notEmpty' => array(
			'rule' => 'notEmpty',
			'message' => 'Pole Název nesmí zůstat prázdné'
		)
	);
}