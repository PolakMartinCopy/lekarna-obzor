<?php
class ContactPerson extends AppModel {
	var $name = 'ContactPerson';
	
	var $actsAs = array('Containable');
	
	var $belongsTo = array('BusinessPartner');
	
	var $hasMany = array(
		'Anniversary' => array(
			'dependent' => true
		),
		'BusinessSessionsContactPerson' // kontaktni osoba prizvana na jednani
	);
	
	var $validate = array(
		'last_name' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Příjmení musí být vyplněno'
			)
		),
		'phone' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Telefon musí být zadán'
			)
		),
		'email' => array(
			'email' => array(
				'rule' => array('email', true),
				'allowEmpty' => true,
				'message' => 'Zadejte platnou emailovou adresu'
			)
		),
		'business_partner_id' => array(
			'rule' => 'numeric',
			'allowEmpty' => false,
			'message' => 'Není vybrán obchodní partner'
		)
	);
	
	function delete($id) {
		$save = array(
			'ContactPerson' => array(
				'id' => $id,
				'active' => false
			)
		);
		
		if ($this->save($save)) {
			$this->Anniversary->deleteAll(array(
				'contact_person_id' => $id
			));
			$this->BusinessSessionsContactPerson->deleteAll(array(
				'contact_person_id' => $id
			));
			return true;
		}
		
		return false;
	}
	
	function afterFind($results) {
		if (isset($results['id'])) {
			$salutation = '';
			if (!empty($results['last_name'])) {
				$salutation = $results['last_name'];
			}
			if (!empty($results['first_name'])) {
				$salutation = $results['first_name'] . ' ' . $salutation;
			}
			if (!empty($results['prefix'])) {
				$salutation = $results['prefix'] . ' ' . $salutation;
			}
			$results['salutation'] = $salutation;
		} else {
			foreach ($results as $index => $result) {
				if (!isset($result['ContactPerson'])) {
					break;
				}
				$salutation = '';
				if (!empty($result['ContactPerson']['last_name'])) {
					$salutation = $result['ContactPerson']['last_name'];					
				}
				if (!empty($result['ContactPerson']['first_name'])) {
					$salutation = $result['ContactPerson']['first_name'] . ' ' . $salutation;
				}
				if (!empty($result['ContactPerson']['prefix'])) {
					$salutation = $result['ContactPerson']['prefix'] . ' ' . $salutation;
				}
				$results[$index]['ContactPerson']['salutation'] = $salutation;
			}
		}
		return $results;
	}
	
	function do_form_search($conditions, $data) {
		if (!empty($data['ContactPerson']['prefix'])) {
			$conditions[] = 'ContactPerson.prefix LIKE \'%%' . $data['ContactPerson']['prefix'] . '%%\'';
		}
		if (!empty($data['ContactPerson']['first_name'])) {
			$conditions[] = 'ContactPerson.first_name LIKE \'%%' . $data['ContactPerson']['first_name'] . '%%\'';
		}
		if (!empty($data['ContactPerson']['last_name'])) {
			$conditions[] = 'ContactPerson.last_name LIKE \'%%' . $data['ContactPerson']['last_name'] . '%%\'';
		}
		if (!empty($data['ContactPerson']['phone'])) {
			$conditions[] = 'ContactPerson.phone LIKE \'%%' . $data['ContactPerson']['phone'] . '%%\'';
		}
		if (!empty($data['ContactPerson']['cellular'])) {
			$conditions[] = 'ContactPerson.cellular LIKE \'%%' . $data['ContactPerson']['cellular'] . '%%\'';
		}
		if (!empty($data['ContactPerson']['email'])) {
			$conditions[] = 'ContactPerson.email LIKE \'%%' . $data['ContactPerson']['email'] . '%%\'';
		}
		if (!empty($data['BusinessPartner']['name'])) {
			$conditions[] = 'BusinessPartner.name LIKE \'%%' . $data['BusinessPartner']['name'] . '%%\'';
		}
		
		return $conditions;
	}
}