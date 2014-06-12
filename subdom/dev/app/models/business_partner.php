<?php
class BusinessPartner extends AppModel {
	var $name = 'BusinessPartner';
	
	var $actsAs = array('Containable');
	
	var $belongsTo = array('User');
	
	var $hasMany = array(
		'ContactPerson' => array(
			'dependent' => true
		),
		'Address' => array(
			'dependent' => true
		),
		'BusinessPartnerNote' => array(
			'dependent' => true	
		),
		'Imposition',
		'BusinessSession',
		'Document' => array(
			'dependent' => true
		),
		'Transaction',
		'Sale',
		'DeliveryNote',
		'StoreItem' => array(
			'dependent' => true
		),
		'CSInvoice',
		'CSCreditNote',
		'CSTransactionItem'
	);
	
	var $validate = array(
		'name' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Jméno firmy nesmí zůstat prázdné'
			)
		),
		'ico' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'IČO nesmí zůstat prázdné'
			)
		),
		'active' => array(
			'bool' => array(
				'rule' => 'boolean',
				'message' => 'Nesprávná hodnota pole Aktivní'
			)
		),
		'bonity' => array(
			'rule' => 'numeric',
			'allow_empty' => false,
			'message' => 'Zadejte bonitu'
		),
		'user_id' => array(
			'numeric' => array(
				'rule' => 'numeric',
				'allowEmpty' => false,
				'message' => 'Není zvolen odpovídající uživatel'
			)
		),
		'email' => array(
			'email' => array(
				'rule' => array('email', true),
				'allowEmpty' => true,
				'message' => 'Email není platný. Zadejte email ve tvaru email@email.cz'
			)
		),
	);
	
	// pole nazvu atributu pro ucely vyhledavani pomoci filtru
	var $attributes = array(
		0 => array('name' => 'BusinessPartner.name', 'value' => 'Název'),
		array('name' => 'BusinessPartner.ico', 'value' => 'IČO'),
		array('name' => 'BusinessPartner.dic', 'value' => 'DIČ'),
		array('name' => 'BusinessPartner.bonity', 'value' => 'Bonita'),
		array('name' => 'BusinessPartner.note', 'value' => 'Poznámka'),
		array('name' => 'Address.name', 'value' => 'Jméno'),
		array('name' => 'Address.first_name', 'value' => 'Křestní jméno'),
		array('name' => 'Address.last_name', 'value' => 'Příjmení'),
		array('name' => 'Address.street', 'value' => 'Ulice'),
		array('name' => 'Address.city', 'value' => 'Město'),
		array('name' => 'Address.zip', 'value' => 'PSČ'),
		array('name' => 'Address.region', 'value' => 'Okres')
	);
	
	function do_form_search($conditions, $data){
		if ( !empty($data['BusinessPartner']['name']) ){
			$conditions[] = 'BusinessPartner.name LIKE \'%%' . $data['BusinessPartner']['name'] . '%%\'';
		}
		
		if ( !empty($data['BusinessPartner']['ico']) ){
			$conditions[] = 'BusinessPartner.ico LIKE \'%%' . $data['BusinessPartner']['ico'] . '%%\'';
		}
		
		if ( !empty($data['BusinessPartner']['dic']) ){
			$conditions[] = 'BusinessPartner.dic LIKE \'%%' . $data['BusinessPartner']['dic'] . '%%\'';
		}
		
		if ( !empty($data['BusinessPartner']['note']) ){
			$conditions[] = 'BusinessPartner.note LIKE \'%%' . $data['BusinessPartner']['note'] . '%%\'';
		}
		
		if ( !empty($data['BusinessPartner']['bonity']) ){
			$conditions[] = 'BusinessPartner.bonity IN (\'' . implode("', '", $data['BusinessPartner']['bonity']) . '\')';
		}
		
		if ( !empty($data['Address']['name']) ){
			$conditions[] = 'Address.name LIKE \'%%' . $data['Address']['name'] . '%%\'';
		}
		
		if ( !empty($data['Address']['person_first_name']) ){
			$conditions[] = 'Address.person_first_name LIKE \'%%' . $data['Address']['person_first_name'] . '%%\'';
		}
		
		if ( !empty($data['Address']['person_last_name']) ){
			$conditions[] = 'Address.person_last_name LIKE \'%%' . $data['Address']['person_last_name'] . '%%\'';
		}
		
		if ( !empty($data['Address']['street']) ){
			$conditions[] = 'Address.street LIKE \'%%' . $data['Address']['street'] . '%%\'';
		}
		
		if ( !empty($data['Address']['number']) ){
			$conditions[] = 'Address.number LIKE \'%%' . $data['Address']['number'] . '%%\'';
		}
		
		if ( !empty($data['Address']['o_number']) ){
			$conditions[] = 'Address.o_number LIKE \'%%' . $data['Address']['o_number'] . '%%\'';
		}
		
		if ( !empty($data['Address']['city']) ){
			$conditions[] = 'Address.city LIKE \'%%' . $data['Address']['city'] . '%%\'';
		}

		if ( !empty($data['Address']['zip']) ){
			$conditions[] = 'Address.zip LIKE \'%%' . $data['Address']['zip'] . '%%\'';
		}
		
		if ( !empty($data['Address']['region']) ){
			$conditions[] = 'Address.region LIKE \'%%' . $data['Address']['region'] . '%%\'';
		}
				
		return $conditions;
	}
	
	function autocomplete_list($user, $term = null) {
		$conditions = array();
		if ($user['User']['user_type_id'] == 3) {
			$conditions = array('BusinessPartner.user_id' => $user['User']['id']);
		}
		if ($term) {
			$conditions['BusinessPartner.name LIKE'] = '%' . $term . '%';
		}
		
		$business_partners = $this->find('all', array(
			'conditions' => $conditions,
			'order' => array('name' => 'asc'),
			'contain' => array(
				'Address' => array(
					'conditions' => array('Address.address_type_id' => 1)
				)
			),
			'fields' => array('BusinessPartner.name')
		));
		
		$autocomplete_business_partners = array();
		foreach ($business_partners as $business_partner) {
			$autocomplete_business_partners[] = array(
				'label' => $business_partner['BusinessPartner']['name'] . ', ' . $business_partner['Address'][0]['street'] . ' ' . $business_partner['Address'][0]['number'] . ', ' . $business_partner['Address'][0]['city'] . ', ' . $business_partner['Address'][0]['zip'],
				'value' => $business_partner['BusinessPartner']['id']
			);
		}
		return json_encode($autocomplete_business_partners);
	}
	
}