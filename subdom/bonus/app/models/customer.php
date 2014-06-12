<?php
class Customer extends AppModel {
	var $name = 'Customer';
	
	var $actsAs = array('Containable');
	
	var $belongsTo = array(
		'Tariff',
		'RecommendingCustomer' => array(
			'className' => 'Customer',
			'foreignKey' => 'recommending_customer_id',
		)
	);
	
	var $hasMany = array('Sale', 'PayOut');
	
	var $validate = array(
		'number' => array(
			'minLength' => array(
				'rule' => array('minLength', 6),
				'message' => 'Číslo zákazníka musí obsahovat minimálně 6 číslic',
				'last' => true
			),
			'maxLength' => array(
				'rule' => array('maxLength', 13),
				'message' => 'Číslo zákazníka musí obsahovat maximálně 13 číslic',
				'last' => true
			),
			'numeric' => array(
				'rule' => 'numeric',
				'message' => 'Číslo zákazníka musí obsahovat 6 - 13 číslic',
				'last' => true
			),
			'isUnique' => array(
				'rule' => 'isUnique',
				'message' => 'Číslo zákazníka již v systému existuje. Zvolte jiné.'
			)
		),
		'first_name' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Zadejte křestní jméno zákazníka.'
			)
		),
		'last_name' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Zadejte příjmení zákazníka'
			)
		),
		'email' => array(
			'email' => array(
				'rule' => array('email', true),
				'allowEmpty' => true,
				'message' => 'Zadejte platnou emailovou adresu'
			)	
		),
		'sex' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Zadejte pohlaví zákazníka.'
			)
		),
		'street' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Zadejte ulici adresy zákazníka.'
			)
		),
		'zip' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Zadejte PSČ adresy zákazníka.'
			)
		),
		'city' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Zadejte město adresy zákazníka.'
			)
		),
		'birth_certificate_number' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Zadejte rodné číslo zákazníka.'	
			),
			'isUnique' => array(
				'rule' => 'isUnique',
				'message' => 'Zákazník s tímto rodným číslem již v systému existuje.'
			)
		)
	);
	
	var $export_fields = array(
		'Customer.number',
		'Customer.last_name',
		'Customer.first_name',
		'Customer.email',
		'Customer.degree_before',
		'Customer.degree_after',
		'Customer.salutation',
		'Customer.sex',
		'Customer.street',
		'Customer.zip',
		'Customer.city',
		'Customer.birth_certificate_number',
		'RecommendingCustomer.name',
		'Tariff.name',
		'Customer.account'	
	);
	
	function afterSave($created) {
		if ($created) {
			$this->data['Customer']['name'] = $this->data['Customer']['last_name'] . ' ' . $this->data['Customer']['first_name'];
			$this->save($this->data);
		}
	}
	
	// prekodovani mazani, aby delalo soft delete (active => false)
	function delete($id) {
		$customer = $this->find('first', array(
			'conditions' => array('Customer.id' => $id),
			'contain' => array(),
			'fields' => array('id')
		));
		
		if (empty($customer)) {
			return false;
		}
		
		$customer['Customer']['active'] = false;
		return $this->save($customer);
	}
	
	function do_form_search($data) {
		$conditions = array();
		if (!empty($data['Customer']['number'])) {
			$conditions[] = 'Customer.number LIKE \'%%' . $data['Customer']['number'] . '%%\'';
		}
		if (!empty($data['Customer']['last_name'])) {
			$conditions[] = 'Customer.last_name LIKE \'%%' . $data['Customer']['last_name'] . '%%\'';
		}
		if (!empty($data['Customer']['first_name'])) {
			$conditions[] = 'Customer.first_name LIKE \'%%' . $data['Customer']['first_name'] . '%%\'';
		}
		if (!empty($data['Customer']['email'])) {
			$conditions[] = 'Customer.email LIKE \'%%' . $data['Customer']['email'] . '%%\'';
		}
		if (!empty($data['Customer']['degree_before'])) {
			$conditions[] = 'Customer.degree_before LIKE \'%%' . $data['Customer']['degree_before'] . '%%\'';
		}
		if (!empty($data['Customer']['degree_after'])) {
			$conditions[] = 'Customer.degree_after LIKE \'%%' . $data['Customer']['degree_after'] . '%%\'';
		}
		if (!empty($data['Customer']['street'])) {
			$conditions[] = 'Customer.street LIKE \'%%' . $data['Customer']['street'] . '%%\'';
		}
		if (!empty($data['Customer']['zip'])) {
			$conditions[] = 'Customer.zip LIKE \'%%' . $data['Customer']['zip'] . '%%\'';
		}
		if (!empty($data['Customer']['city'])) {
			$conditions[] = 'Customer.city LIKE \'%%' . $data['Customer']['city'] . '%%\'';
		}
		if (!empty($data['Customer']['birth_certificate_number'])) {
			$conditions[] = 'Customer.birth_certificate_number LIKE \'%%' . $data['Customer']['birth_certificate_number'] . '%%\'';
		}
		if (!empty($data['RecommendingCustomer']['name'])) {
			$conditions[] = 'RecommendingCustomer.name LIKE \'%%' . $data['RecommendingCustomer']['name'] . '%%\'';
		}

		return $conditions;
	}
	
	// pripise na ucet zakaznika hodnotu (kdyz je zaporna, tak odecita)
	function move_account($id, $value) {
		$customer = $this->find('first', array(
			'conditions' => array('id' => $id),
			'contain' => array(),
			'fields' => array('id', 'account')
		));
		
		if (empty($customer)) {
			return false;
		}
		
		$customer['Customer']['account'] += $value;
		
		if ($this->save($customer)) {
			return true;
		}
		
		return false;
	}
}
?>