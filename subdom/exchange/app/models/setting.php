<?php 
class Setting extends AppModel {
	var $name = 'Setting';
	
	var $validate = array(
		'rate' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Zadejte hodnotu kurzu pro odeslání upozornění.'
			),
			'decimal' => array(
				'rule' => array('decimal'),
				'message' => 'Kurz musí být desetinné číslo.'
			)
		),
		'phone' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Zadejte telefonní číslo.'
			)
		)
	);
	
	function beforeValidate() {
		if (preg_match('/^\d+$/', $this->data['Setting']['rate'])) {
			$this->data['Setting']['rate'] .= '.00';
		} else {
			$this->data['Setting']['rate'] = str_replace(',', '.', $this->data['Setting']['rate']);
		}
	}
}
?>