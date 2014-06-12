<?php
class Address extends AppModel {
	var $name = 'Address';
	
	var $actsAs = array('Containable');
	
	var $belongsTo = array('BusinessPartner', 'AddressType');
	
	var $validate = array(
		'name' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Pole Název nesmí zůstat prázdné'
			)
		),
		'city' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Pole Město nesmí zůstat prázdné'
			)
		),
		'number' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Pole Číslo popisné nesmí zůstat prázdné'
			)
		),
/*		'city' => array(
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
		),*/
		'address_type_id' => array(
			'numeric' => array(
				'rule' => 'numeric',
				'allowEmpty' => false,
				'message' => 'Typ adresy musí být vybrán'
			)
		)
	);
	
	function get_addresses($business_partner_id) {
		$seat_address = $this->find('first', array(
			'conditions' => array(
				'Address.business_partner_id' => $business_partner_id,
				'Address.address_type_id' => 1
			)
		));
		
		$delivery_address = $this->find('first', array(
			'conditions' => array(
				'Address.business_partner_id' => $business_partner_id,
				'Address.address_type_id' => 4
			)
		));
		
		$invoice_address = $this->find('first', array(
			'conditions' => array(
				'Address.business_partner_id' => $business_partner_id,
				'Address.address_type_id' => 3
			)
		));
		
		return array($seat_address, $delivery_address, $invoice_address);
	}
	
	function do_form_search($conditions, $data) {
		if (!empty($data['Address']['name'])) {
			$conditions[] = 'Address.name LIKE \'%%' . $data['Address']['name'] . '%%\'';
		}
		if (!empty($data['Address']['person_first_name'])) {
			$conditions[] = 'Address.person_first_name LIKE \'%%' . $data['Address']['person_first_name'] . '%%\'';
		}
		if (!empty($data['Address']['person_last_name'])) {
			$conditions[] = 'Address.person_last_name LIKE \'%%' . $data['Address']['person_last_name'] . '%%\'';
		}
		if (!empty($data['Address']['street'])) {
			$conditions[] = 'Address.street LIKE \'%%' . $data['Address']['street'] . '%%\'';
		}
		if (!empty($data['Address']['number'])) {
			$conditions[] = 'Address.number LIKE \'%%' . $data['Address']['number'] . '%%\'';
		}
		if (!empty($data['Address']['o_number'])) {
			$conditions[] = 'Address.o_number LIKE \'%%' . $data['Address']['o_number'] . '%%\'';
		}
		if (!empty($data['Address']['city'])) {
			$conditions[] = 'Address.city LIKE \'%%' . $data['Address']['city'] . '%%\'';
		}
		if (!empty($data['Address']['zip'])) {
			$conditions[] = 'Address.zip LIKE \'%%' . $data['Address']['zip'] . '%%\'';
		}
		if (!empty($data['Address']['region'])) {
			$conditions[] = 'Address.region LIKE \'%%' . $data['Address']['region'] . '%%\'';
		}
		
		return $conditions;
	}
}