<?php
class Tariff extends AppModel {
	var $name = 'Tariff';
	
	var $actsAs = array('Containable');
	
	var $hasMany = array('Customer');
	
	var $validate = array(
		'name' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Zadejte název bonusového tarifu.'
			)
		),
		'owner_amount' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Zadejte výši bonusu pro majitele účtu.'
			)
		),
		'recommending_amount' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Zadejte výši bonusu pro doporučující osobu.'
			)
		)
	);
	
	function beforeSave() {
		// ve vysich bonusu zmenim pripadnou desetinnou carku na tecku
		$fields = array('owner_amount', 'recommending_amount');
		foreach ($fields as $field) {
			if (isset($this->data['Tariff'][$field]) && preg_match('/\d+,\d+/', $this->data['Tariff'][$field])) {
				$this->data['Tariff'][$field] = str_replace(',', '.', $this->data['Tariff'][$field]);
			}
		}
		
		return true;
	}
}
?>