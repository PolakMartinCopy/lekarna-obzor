<?php
class TransactionType extends AppModel {
	var $name = 'TransactionType';
	
	var $actsAs = array('Containable');
	
	var $hasMany = array('Transaction');
	
	var $validate = array(
		'name' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Zadejte název typu transakce'
			)
		)
	);
}