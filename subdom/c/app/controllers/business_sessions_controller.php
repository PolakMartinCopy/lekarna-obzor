<?php
class BusinessSessionsController extends AppController {
	var $name = 'BusinessSessions';
	
	var $index_link = array('controller' => 'business_sessions', 'action' => 'index');
	
	var $left_menu_list = array('business_sessions');

	function beforeFilter(){
		parent::beforeFilter();
		$this->set('active_tab', 'business_sessions');
	}
	
	function beforeRender(){
		parent::beforeRender();
		$this->set('left_menu_list', $this->left_menu_list);
		
		$business_session_types = $this->BusinessSession->BusinessSessionType->find('list');
		$this->set('business_session_types', $business_session_types);
	}
	
	function user_index() {
		$user_id = $this->user['User']['id'];
		
		$attributes = array(0 => 'Obchodní partner', 'Kontakt', 'Popis');
		$this->set('attributes', $attributes);
		
		$conditions = array();
		
		if (isset($this->params['named']['reset']) && $this->params['named']['reset'] == 'business_sessions') {
			$this->Session->delete('Search.BusinessSessionSearch2');
			$this->redirect(array('controller' => 'business_sessions', 'action' => 'index'));
		}

		// pokud chci vysledky vyhledavani
		if ( isset($this->data['BusinessSessionSearch2']['BusinessSession']['search_form']) && $this->data['BusinessSessionSearch2']['BusinessSession']['search_form'] == 1 ){
			$this->Session->write('Search.BusinessSessionSearch2', $this->data['BusinessSessionSearch2']);
			$conditions = $this->BusinessSession->do_form_search($conditions, $this->data['BusinessSessionSearch2']);
		} elseif ($this->Session->check('Search.BusinessSessionSearch2')) {
			$this->data['BusinessSessionSearch2'] = $this->Session->read('Search.BusinessSessionSearch2');
			$conditions = $this->BusinessSession->do_form_search($conditions, $this->data['BusinessSessionSearch2']);
		}
	
		$order = array('BusinessSession.date' => 'desc');
		if (isset($this->params['named']['sort']) && $this->params['named']['sort'] == 'celkem') {
			$order = array($this->params['named']['sort'] => $this->params['named']['direction']);
			unset($this->params['named']['sort']);
			unset($this->params['named']['direction']);
		}
		
		if ($this->user['User']['user_type_id'] == 3) {
			$conditions[] = '(BusinessSession.user_id = ' . $user_id . ' OR BusinessSessionsUser.user_id = ' . $user_id . ')';
		}
		
		$this->paginate['BusinessSession'] = array(
			'conditions' => $conditions,
			'contain' => array(
				'BusinessPartner',
				'BusinessSessionState',
				'BusinessSessionType',
				'User',
				'Offer' => array(
					'fields' => array('id')
				)
			),
			'order' => $order,
			'fields' => array('*', 'SUM(Cost.amount) as celkem'),
			'limit' => 30,
		);
		
		$business_sessions = $this->paginate('BusinessSession');
		$this->set('business_sessions', $business_sessions);

		$find = $this->paginate['BusinessSession'];
		unset($find['limit']);
		unset($find['contain']['Offer']);
		unset($find['fields']);

		$this->set('find', $find);
		
		$export_fields = array(
			array('field' => 'BusinessSession.id', 'position' => '["BusinessSession"]["id"]', 'alias' => 'BusinessSession.id'),
			array('field' => 'BusinessSession.date', 'position' => '["BusinessSession"]["date"]', 'alias' => 'BusinessSession.date'),
			array('field' => 'BusinessSession.created', 'position' => '["BusinessSession"]["created"]', 'alias' => 'BusinessSession.created'),
			array('field' => 'BusinessPartner.name', 'position' => '["BusinessPartner"]["name"]', 'alias' => 'BusinessPartner.name'),
			array('field' => 'BusinessSessionType.name', 'position' => '["BusinessSessionType"]["name"]', 'alias' => 'BusinessSessionType.name'),
			array('field' => 'BusinessSessionState.name', 'position' => '["BusinessSessionState"]["name"]', 'alias' => 'BusinessSessionState.name'),
			array('field' => 'CONCAT(User.last_name, " ", User.first_name) AS full_name', 'position' => '[0]["full_name"]', 'alias' => 'User.fullname'),
			array('field' => 'SUM(Cost.amount) AS total_amount', 'position' => '[0]["total_amount"]', 'alias' => 'Cost.total_amount')
		);
		$this->set('export_fields', $export_fields);
}
	
	function user_view($id = null) {
		if (!$id) {
			$this->Session->setFlash('Není určeno obchodní jednání, které chcete zobrazit');
			$this->redirect(array('controller' => 'business_sessions', 'action' => 'index'));
		}
		
		$offers_conditions = array();
		
		if (isset($this->params['named']['reset']) && $this->params['named']['reset'] == 'offers') {
			$this->Session->delete('Search.OfferForm');
			$this->redirect(array('controller' => 'business_sessions', 'action' => 'view', $id, 'tab' => 4));
		}
		
		// pokud chci vysledky vyhledavani v nakladech
		if ( isset($this->data['OfferForm']['Offer']['search_form']) && $this->data['OfferForm']['Offer']['search_form'] == 1 ){
			$this->Session->write('Search.OfferForm', $this->data['OfferForm']);
			$offers_conditions = $this->BusinessSession->Offer->do_form_search($offers_conditions, $this->data['OfferForm']);
		} elseif ($this->Session->check('Search.OfferForm')) {
			$this->data['OfferForm'] = $this->Session->read('Search.OfferForm');
			$offers_conditions = $this->BusinessSession->Offer->do_form_search($offers_conditions, $this->data['OfferForm']);
		}
		
		$costs_conditions = array();
	
		if (isset($this->params['named']['reset']) && $this->params['named']['reset'] == 'costs') {
			$this->Session->delete('Search.CostForm');
			$this->redirect(array('controller' => 'business_sessions', 'action' => 'view', $id, 'tab' => 3));
		} 
		
		// pokud chci vysledky vyhledavani v nakladech
		if ( isset($this->data['CostForm']['Cost']['search_form']) && $this->data['CostForm']['Cost']['search_form'] == 1 ){
			$this->Session->write('Search.CostForm', $this->data['CostForm']);
			$costs_conditions = $this->BusinessSession->Cost->do_form_search($costs_conditions, $this->data['CostForm']);
		} elseif ($this->Session->check('Search.CostForm')) {
			$this->data['CostForm'] = $this->Session->read('Search.CostForm');
			$costs_conditions = $this->BusinessSession->Cost->do_form_search($costs_conditions, $this->data['CostForm']);
		}
		
		$business_session = $this->BusinessSession->find('first', array(
			'conditions' => array('BusinessSession.id' => $id),
			'contain' => array(
				'BusinessPartner',
				'User',
				'BusinessSessionsUser' => array('User'),
				'BusinessSessionState',
				'BusinessSessionType',
				'Offer' => array(
					'conditions' => $offers_conditions,
					'order' => array('Offer.created' => 'desc'),
				),
				'Cost' => array(
					'conditions' => $costs_conditions,
					'order' => array('Cost.date' => 'desc')
				)
			)
		));
		
		if (empty($business_session)) {
			$this->Session->setFlash('Zvolené obchodní jednání neexistuje');
			$this->redirect($this->index_link);
		}
		
		if (!$this->BusinessSession->checkUser($this->user, $business_session['BusinessSession']['user_id'])) {
			$this->Session->setFlash('Neoprávněný přístup. Nemáte právo zobrazit informace o tomto obchodním jednání.');
			$this->redirect($this->index_link);
		}
		
		$this->set('business_session', $business_session);
		
		$contact_people_conditions = array('BusinessSessionsContactPerson.business_session_id' => $id);
		
		if (isset($this->params['named']['reset']) && $this->params['named']['reset'] == 'contact_people') {
			$this->Session->delete('Search.ContactPersonSearch2');
			$this->redirect(array('controller' => 'business_sessions', 'action' => 'view', $business_session['BusinessSession']['id'], 'tab' => 2));
		}

		// pokud chci vysledky vyhledavani
		if ( isset($this->data['ContactPersonSearch2']['ContactPerson']['search_form']) && $this->data['ContactPersonSearch2']['ContactPerson']['search_form'] == 1 ){
			$this->Session->write('Search.ContactPersonSearch2', $this->data['ContactPersonSearch2']);
			$contact_people_conditions = $this->BusinessSession->BusinessSessionsContactPerson->ContactPerson->do_form_search($contact_people_conditions, $this->data['ContactPersonSearch2']);
		} elseif ($this->Session->check('Search.ContactPersonSearch2')) {
			$this->data['ContactPersonSearch2'] = $this->Session->read('Search.ContactPersonSearch2');
			$contact_people_conditions = $this->BusinessSession->BusinessSessionsContactPerson->ContactPerson->do_form_search($contact_people_conditions, $this->data['ContactPersonSearch2']);
		}

		$invited_contact_people_find = array(
			'conditions' => $contact_people_conditions,
			'contain' => array(),
			'fields' => array('*'),
			'joins' => array(
				array(
					'table' => 'contact_people',
					'alias' => 'ContactPerson',
					'type' => 'INNER',
					'conditions' => array(
						'ContactPerson.id = BusinessSessionsContactPerson.contact_person_id'
					)
				),
				array(
					'table' => 'business_partners',
					'alias' => 'BusinessPartner',
					'type' => 'INNER',
					'conditions' => array(
						'ContactPerson.business_partner_id = BusinessPartner.id'
					)
				)
			)
		);
		$invited_contact_people = $this->BusinessSession->BusinessSessionsContactPerson->find('all', $invited_contact_people_find);
		$this->set('contact_people', $invited_contact_people);
		
		unset($invited_contact_people_find['fields']);
		$this->set('invited_contact_people_find', $invited_contact_people_find);
		
		$invited_contact_people_export_fields = array(
			array('field' => 'ContactPerson.id', 'position' => '["ContactPerson"]["id"]', 'alias' => 'ContactPerson.id'),
			array('field' => 'ContactPerson.first_name', 'position' => '["ContactPerson"]["first_name"]', 'alias' => 'ContactPerson.first_name'),
			array('field' => 'ContactPerson.last_name', 'position' => '["ContactPerson"]["last_name"]', 'alias' => 'ContactPerson.last_name'),
			array('field' => 'ContactPerson.prefix', 'position' => '["ContactPerson"]["prefix"]', 'alias' => 'ContactPerson.prefix'),
			array('field' => 'BusinessPartner.name', 'position' => '["BusinessPartner"]["name"]', 'alias' => 'BusinessPartner.name'),
			array('field' => 'ContactPerson.phone', 'position' => '["ContactPerson"]["phone"]', 'alias' => 'ContactPerson.phone'),
			array('field' => 'ContactPerson.cellular', 'position' => '["ContactPerson"]["cellular"]', 'alias' => 'ContactPerson.cellular'),
			array('field' => 'ContactPerson.email', 'position' => '["ContactPerson"]["email"]', 'alias' => 'ContactPerson.email'),
			array('field' => 'ContactPerson.note', 'position' => '["ContactPerson"]["note"]', 'alias' => 'ContactPerson.note'),
			array('field' => 'ContactPerson.hobby', 'position' => '["ContactPerson"]["hobby"]', 'alias' => 'ContactPerson.hobby'),
			array('field' => 'ContactPerson.active', 'position' => '["ContactPerson"]["active"]', 'alias' => 'ContactPerson.active')
		);
		$this->set('invited_contact_people_export_fields', $invited_contact_people_export_fields);
		
		$costs_conditions['BusinessSession.id'] = $id;
		$costs_find = array(
			'conditions' => $costs_conditions,
			'contain' => array(),
			'joins' => array(
				array(
					'table' => 'business_sessions',
					'alias' => 'BusinessSession',
					'type' => 'INNER',
					'conditions' => array(
						'Cost.business_session_id = BusinessSession.id'
					)
				),
				array(
					'table' => 'business_partners',
					'alias' => 'BusinessPartner',
					'type' => 'INNER',
					'conditions' => array(
						'BusinessPartner.id = BusinessSession.business_partner_id'
					)
				)
			)
		);
		$this->set('costs_find', $costs_find);
		
		$costs_export_fields = array(
			array('field' => 'Cost.id', 'position' => '["Cost"]["id"]', 'alias' => 'Cost.id'),
			array('field' => 'Cost.date', 'position' => '["Cost"]["date"]', 'alias' => 'Cost.date'),
			array('field' => 'Cost.amount', 'position' => '["Cost"]["amount"]', 'alias' => 'Cost.amount'),
			array('field' => 'Cost.description', 'position' => '["Cost"]["description"]', 'alias' => 'Cost.description'),
			array('field' => 'BusinessSession.id', 'position' => '["BusinessSession"]["id"]', 'alias' => 'BusinessSession.id'),
			array('field' => 'BusinessPartner.name', 'position' => '["BusinessPartner"]["name"]', 'alias' => 'BusinessPartner.name')
		);
		$this->set('costs_export_fields', $costs_export_fields);
		
		$offers_conditions['Offer.business_session_id'] = $id;
		$offers_find = array(
			'conditions' => $offers_conditions,
			'contain' => array(),
			'joins' => array(
				array(
					'table' => 'business_sessions',
					'alias' => 'BusinessSession',
					'type' => 'INNER',
					'conditions' => array(
						'Offer.business_session_id = BusinessSession.id'
					)
				),
				array(
					'table' => 'business_partners',
					'alias' => 'BusinessPartner',
					'type' => 'INNER',
					'conditions' => array(
						'BusinessSession.business_partner_id = BusinessPartner.id'
					)
				)
			)
		);
		$this->set('offers_find', $offers_find);
		
		$offers_export_fields = array(
			array('field' => 'Offer.id', 'position' => '["Offer"]["id"]', 'alias' => 'Offer.id'),
			array('field' => 'Offer.created', 'position' => '["Offer"]["created"]', 'alias' => 'Offer.created'),
			array('field' => 'Offer.content', 'position' => '["Offer"]["content"]', 'alias' => 'Offer.content'),
			array('field' => 'BusinessSession.id', 'position' => '["BusinessSession"]["id"]', 'alias' => 'BusinessSession.id'),
			array('field' => 'BusinessPartner.name', 'position' => '["BusinessPartner"]["name"]', 'alias' => 'BusinessPartner.name'),
		);
		$this->set('offers_export_fields', $offers_export_fields);
		
		// do leveho menu pridam polozku pro detaily partnera
		$this->left_menu_list[] = 'business_session_detailed';
	}
	
	function user_search() {
		$this->set('monthNames', $this->monthNames);
		$this->set('user', $this->user);
		$this->set('business_session_states', $this->BusinessSession->BusinessSessionState->find('list'));
		
		//$this->data['BusinessSession']['from']['checked'] = false;
		if (isset($this->params['named']['BusinessSession.from.date'])) {
			$this->data['BusinessSession']['from']['checked'] = true;
			$this->data['BusinessSession']['from']['date'] = $this->BusinessSession->unbuilt_date($this->params['named']['BusinessSession.from.date']);
		}
		//$this->data['BusinessSession']['to']['checked'] = false;
		if (isset($this->params['named']['BusinessSession.to.date'])) {
			$this->data['BusinessSession']['to']['checked'] = true;
			$this->data['BusinessSession']['to']['date'] = $this->BusinessSession->unbuilt_date($this->params['named']['BusinessSession.to.date']);
		}
		if (isset($this->params['named']['BusinessPartner.name'])) {
			$this->data['BusinessPartner']['name'] = $this->params['named']['BusinessPartner.name'];
		}
		if (isset($this->params['named']['ContactPerson.name'])) {
			$this->data['ContactPerson']['name'] = $this->params['named']['ContactPerson.name'];
		}
		if (isset($this->params['named']['BusinessSession.business_session_type_id'])) {
			$this->data['BusinessSession']['business_session_type_id'] = $this->params['named']['BusinessSession.business_session_type_id'];
		}
		if (isset($this->params['named']['BusinessSession.business_session_state_id'])) {
			$this->data['BusinessSession']['business_session_state_id'] = $this->params['named']['BusinessSession.business_session_state_id'];
		}
		if (isset($this->params['named']['Address.city'])) {
			$this->data['Address']['city'] = $this->params['named']['Address.city'];
		}
		if (isset($this->params['named']['BusinessPartner.ico'])) {
			$this->data['BusinessPartner']['ico'] = $this->params['named']['BusinessPartner.ico'];
		}
		if (isset($this->params['named']['BusinessSession.description_query'])) {
			$this->data['BusinessSession']['description_query'] = $this->params['named']['BusinessSession.description_query'];
		}
		
		if (isset($this->data)) {
			$conditions = array();
			if (isset($this->data['BusinessSession']['from']['checked']) && isset($this->data['BusinessSession']['to']['checked']) && $this->data['BusinessSession']['from']['checked'] && $this->data['BusinessSession']['to']['checked']) {
				$conditions[] = 'BusinessSession.date BETWEEN \'' . $this->BusinessSession->built_date($this->data['BusinessSession']['from']['date']) . ' 00:00:00\' AND \'' . $this->BusinessSession->built_date($this->data['BusinessSession']['to']['date']) . ' 00:00:00\'';
			} elseif (isset($this->data['BusinessSession']['from']['checked']) && $this->data['BusinessSession']['from']['checked']) {
				$conditions[] = 'BusinessSession.date > \'' . $this->BusinessSession->built_date($this->data['BusinessSession']['from']['date']) . ' 00:00:00\'';
			} elseif (isset($this->data['BusinessSession']['to']['checked']) && $this->data['BusinessSession']['to']['checked']) {
				$conditions[] = 'BusinessSession.date < \'' . $this->BusinessSession->built_date($this->data['BusinessSession']['to']['date']) . ' 00:00:00\'';
			}

			$conditions['BusinessSession.business_session_state_id'] = $this->data['BusinessSession']['business_session_state_id'];
			$conditions['BusinessSession.business_session_type_id'] = $this->data['BusinessSession']['business_session_type_id'];
			if (!empty($this->data['BusinessSession']['description_query'])) {
				$conditions[] = 'BusinessSession.description LIKE \'%%' . $this->data['BusinessSession']['description_query'] . '%%\'';
			}
			if (!empty($this->data['BusinessPartner']['name'])) {
				$conditions[] = 'BusinessPartner.name LIKE \'%%' . $this->data['BusinessPartner']['name'] . '%%\'';
			}
			if (!empty($this->data['BusinessPartner']['ico'])) {
				$conditions[] = 'BusinessPartner.name LIKE \'%%' . $this->data['BusinessPartner']['ico'] . '%%\'';
			}
			if (!empty($this->data['ContactPerson']['name'])) {
				$conditions[] = '(ContactPerson.first_name LIKE \'%%' . $this->data['ContactPerson']['name'] . '%%\' OR ContactPerson.last_name LIKE \'%%' . $this->data['ContactPerson']['name'] . '%%\')';
			}
			if (!empty($this->data['Address']['city'])) {
				$conditions[] = 'Address.city LIKE \'%%' . $this->data['Address']['city'] . '%%\'';
			}
			
			$joins = array(
				array(
					'table' => 'business_partners',
					'alias' => 'BusinessPartner',
					'type' => 'LEFT',
					'conditions' => array(
						'BusinessSession.business_partner_id = BusinessPartner.id'
					)
				),
				array(
					'table' => 'addresses',
					'alias' => 'Address',
					'type' => 'INNER',
					'conditions' => array(
						'BusinessPartner.id = Address.business_partner_id',
						'Address.address_type_id = 1'
					)
				),
				array(
					'table' => 'costs',
					'alias' => 'Cost',
					'type' => 'LEFT',
					'conditions' => array(
						'Cost.business_session_id = BusinessSession.id'
					)
				)
			);
			
			if (isset($this->data['ContactPerson']['name'])) {
				$contact_joins = array(
					array(
						'table' => 'business_sessions_contact_people',
						'alias' => 'BusinessSessionsContactPerson',
						'type' => 'RIGHT',
						'conditions' => array(
							'BusinessSession.id = BusinessSessionsContactPerson.business_session_id'
						)
					),
					array(
						'table' => 'contact_people',
						'alias' => 'ContactPerson',
						'type' => 'LEFT',
						'conditions' => array(
							'BusinessSessionsContactPerson.contact_person_id = ContactPerson.id'
						)
					)
				);
				$joins = array_merge($joins, $contact_joins);
			}
			
			$this->paginate['BusinessSession'] = array(
				'conditions' => $conditions,
				'contain' => array('BusinessSessionType', 'BusinessSessionState', 'User'),
				'fields' => array('*', 'SUM(Cost.amount) as celkem'),
				'group' => array('BusinessSession.id'),
				'limit' => 30,
				'order' => array('BusinessSession.date' => 'desc'),
				'joins' => $joins
			);
			$business_sessions = $this->paginate('BusinessSession');
			
			foreach ($business_sessions as $index => $business_session) {
				$cost = $this->BusinessSession->Cost->find('first', array(
					'fields' => array('SUM(Cost.amount)'),
					'conditions' => array('Cost.business_session_id' => $business_session['BusinessSession']['id']),
					'contain' => array(),
					'group' => array('Cost.business_session_id')
				));
				$business_sessions[$index][0]['celkem'] = $cost[0]['SUM(`Cost`.`amount`)'];
			}
			$this->set('business_sessions', $business_sessions);
		}
	}

	function user_add() {
		$user_id = $this->user['User']['id'];
		$this->set('user_id', $user_id);
		
		$conditions = array();
		if ($this->user['User']['user_type_id'] == 3) {
			$conditions = array('BusinessPartner.user_id' => $user_id);
		}
		
		$business_partners = $this->BusinessSession->BusinessPartner->find('all', array(
			'conditions' => $conditions,
			'order' => array('name' => 'asc'),
			'contain' => array(
				'Address' => array(
					'conditions' => array('Address.address_type_id' => 1)
				)
			)
		));
		if (isset($this->params['named']['business_partner_id'])) {
			$this->set('business_partner_id', $this->params['named']['business_partner_id']);
			$this->set('business_partners', $business_partners);
		} else {
			$autocomplete_business_partners = array();
			foreach ($business_partners as $business_partner) {
				$autocomplete_business_partners[] = array(
					'label' => $business_partner['BusinessPartner']['name'] . ', ' . $business_partner['Address'][0]['street'] . ' ' . $business_partner['Address'][0]['number'] . ', ' . $business_partner['Address'][0]['city'] . ', ' . $business_partner['Address'][0]['zip'],
					'value' => $business_partner['BusinessPartner']['id']
				);
			}
			$this->set('business_partners', json_encode($autocomplete_business_partners));
		}
		
		$this->set('monthNames', $this->monthNames);
		
		$users = $this->BusinessSession->BusinessSessionsUser->User->find('all', array(
			'conditions' => array('User.id !=' => $user_id),
			'fields' => array(
				'id',
				'CONCAT(User.first_name, " ", User.last_name) AS name'
			),
			'contain' => array()
		));
		$users = Set::combine($users, '{n}.User.id', '{n}.0.name');
		$this->set('users', $users);
		
		if (isset($this->data)) {
			$data_backup = $this->data;
			$business_sessions_users = array();
			if ($this->data['BusinessSessionsUser']['user_id']) {
				foreach ($this->data['BusinessSessionsUser']['user_id'] as $user_id) {
					$business_sessions_users[] = array(
						'user_id' => $user_id
					);
				}
			}
			$this->data['BusinessSessionsUser'] = $business_sessions_users;
			
			$this->data['BusinessSession']['date'] = cal2db_date($this->data['BusinessSession']['date']);
			$this->data['BusinessSession']['date'] = array_merge($this->data['BusinessSession']['date'], $this->data['BusinessSession']['time']);

			if ($this->BusinessSession->saveAll($this->data)) {
				$this->Session->setFlash('Obchodní jednání bylo uloženo');
				$this->redirect($this->index_link);
			} else {
				$this->data = $data_backup;
				$this->Session->setFlash('Obchodní jednání se nepodařilo uložit, opakujte prosím akci');
			}
		}
	}
	
	function user_edit($id = null) {
		if (!$id) {
			$this->Session->setFlash('Není určeno obchodní jednání, které chcete upravit');
			$this->redirect($this->index_link);
		}
		
		$business_partners_conditions = array();
		if ($this->user['User']['user_type_id'] == 3) {
			$business_partners_conditions = array('BusinessPartner.user_id' => $user_id);
		}
		
		$business_session = $this->BusinessSession->find('first', array(
			'conditions' => array('BusinessSession.id' => $id),
			'contain' => array(
				'BusinessSessionsUser' => array(
					'User'
				),
				'BusinessPartner' => array(
					'Address' => array(
						'conditions' => array('Address.address_type_id' => 1)
					)
				)
			)
		));
		
		if (empty($business_session)) {
			$this->Session->setFlash('Zvolené obchodní jednání neexistuje');
			$this->redirect($this->index_link);
		}
		
		if (!$this->BusinessSession->checkUser($this->user, $business_session['BusinessSession']['user_id'])) {
			$this->Session->setFlash('Neoprávněný přístup. Nemáte právo upravit toto jednání.');
			$this->redirect($this->index_link);
		}
		
		$this->set('business_session', $business_session);
		// do leveho menu pridam polozku pro detaily partnera
		$this->left_menu_list[] = 'business_session_detailed';
		
		$user_id = $this->user['User']['id'];
		$this->set('user_id', $user_id);
				
		$conditions = array();
		if ($this->user['User']['user_type_id'] == 3) {
			$conditions = array('BusinessPartner.user_id' => $user_id);
		}
		
		$business_partners = $this->BusinessSession->BusinessPartner->find('all', array(
			'conditions' => $conditions,
			'order' => array('name' => 'asc'),
			'contain' => array(
				'Address' => array(
					'conditions' => array('Address.address_type_id' => 1)
				)
			)
		));
		$autocomplete_business_partners = array();
		foreach ($business_partners as $business_partner) {
			$autocomplete_business_partners[] = array(
				'label' => $business_partner['BusinessPartner']['name'] . ', ' . $business_partner['Address'][0]['street'] . ' ' . $business_partner['Address'][0]['number'] . ', ' . $business_partner['Address'][0]['city'] . ', ' . $business_partner['Address'][0]['zip'],
				'value' => $business_partner['BusinessPartner']['id']
			);
		}
		$this->set('business_partners', json_encode($autocomplete_business_partners));
		
		$this->set('monthNames', $this->monthNames);
		
		$users = $this->BusinessSession->BusinessSessionsUser->User->find('all', array(
			'conditions' => array('User.id !=' => $user_id),
			'fields' => array(
				'id',
				'CONCAT(User.first_name, " ", User.last_name) AS name'
			),
			'contain' => array()
		));
		$users = Set::combine($users, '{n}.User.id', '{n}.0.name');
		$this->set('users', $users);
		
		if (isset($this->data)) {
			$data_backup = $this->data;
			
			$this->data['BusinessSession']['date'] = cal2db_date($this->data['BusinessSession']['date']);
			$this->data['BusinessSession']['date'] = array_merge($this->data['BusinessSession']['date'], $this->data['BusinessSession']['time']);

			if (empty($this->data['BusinessSession']['business_partner_id'])) {
				unset($this->data['BusinessSession']['business_partner_id']);
			}
			
			// pri zmene obchodniho partnera smazu prizvane kontaktni osoby
			if (
				isset($this->data['BusinessSession']['business_partner_id']) &&
				$this->data['BusinessSession']['business_partner_id'] != $business_session['BusinessSession']['business_partner_id']
			) {
				$this->BusinessSession->BusinessSessionsContactPerson->deleteAll(array(
					'business_session_id' => $this->data['BusinessSession']['id']
				));
			}
			
			// vytahnu si uzivatele, kteri maji byt prizvani k obchodnimu jednani
			$tmp = array();
			if (is_array($this->data['BusinessSessionsUser']['user_id'])) {
				foreach ($this->data['BusinessSessionsUser']['user_id'] as $business_sessions_user) {
					$tmp[] = array(
						'user_id' => $business_sessions_user,
						'business_session_id' => $id
					);
				}
			}
			$this->data['BusinessSessionsUser'] = $tmp;

			$this->BusinessSession->begin();
			try {
				$this->BusinessSession->save($this->data);
				$in_db_ids = array();
				if (empty($this->data['BusinessSessionsUser'])) {
					$to_del = $this->BusinessSession->BusinessSessionsUser->find('all', array(
						'conditions' => array('BusinessSessionsUser.business_session_id' => $id),
						'contain' => array()
					));
				} else {
					// ulozim do db ty z multiple selectu, ktery tam nejsou
					foreach ($this->data['BusinessSessionsUser'] as $save) {
						$db_business_sessions_user = $this->BusinessSession->BusinessSessionsUser->find('first', array(
							'conditions' => $save,
							'contain' => array()
						));
	
						if (empty($db_business_sessions_user)) {
							$this->BusinessSession->BusinessSessionsUser->save($save);
							$in_db_ids[] = $this->BusinessSession->BusinessSessionsUser->id;
							unset($this->BusinessSession->BusinessSessionsUser->id);
						} else {
							$in_db_ids[] = $db_business_sessions_user['BusinessSessionsUser']['id'];
						}
					}
					// smazu z db ty, ktery nejsou v multiple selectu
					$to_del = $this->BusinessSession->BusinessSessionsUser->find('all', array(
						'conditions' => array(
							'BusinessSessionsUser.business_session_id' => $id,
							'BusinessSessionsUser.id NOT IN (' . implode(',', $in_db_ids) . ')'
						)
					));
				}
				foreach ($to_del as $item) {
					$this->BusinessSession->BusinessSessionsUser->delete($item['BusinessSessionsUser']['id']);
				}
			} catch (Exception $e) {
				$this->BusinessSession->rollback();
				$this->data = $data_backup;
				$this->Session->setFlash('Obchodní jednání se nepodařilo uložit, opravte chyby ve formuláři a opakujte prosím akci');
			}
			$this->BusinessSession->commit();
			$this->Session->setFlash('Obchodní jednání bylo uloženo.');
			$this->redirect(array('controller' => 'business_sessions', 'action' => 'view', $id));
		} else {
			$this->data = $business_session;
			$time = explode(' ', $business_session['BusinessSession']['date']);
			$date = $time[0];
			$this->data['BusinessSession']['time'] = $time[1];
			$this->data['BusinessSession']['date'] = db2cal_date($date);
			$this->data['BusinessSession']['business_partner_name'] = $business_session['BusinessPartner']['name'] . ', ' . $business_session['BusinessPartner']['Address'][0]['street'] . ' ' . $business_session['BusinessPartner']['Address'][0]['number'] . ', ' . $business_session['BusinessPartner']['Address'][0]['city'] . ', ' . $business_session['BusinessPartner']['Address'][0]['zip'];
			unset($this->data['BusinessSessionsUser']);
			foreach ($business_session['BusinessSessionsUser'] as $user) {
				$this->data['BusinessSessionsUser']['user_id'][] = $user['User']['id'];
			}
		}
	}
	
	function user_invite($id = null) {
		if (!$id) {
			$this->Session->setFlash('Není určeno obchodní jednání, ke kterému chcete přidat kontaktní osoby');
			$this->redirect($this->index_link);
		}
		
		$business_session = $this->BusinessSession->find('first', array(
			'conditions' => array('BusinessSession.id' => $id),
			'contain' => array(
				'BusinessSessionsContactPerson'
			)
		));
		
		if (empty($business_session)) {
			$this->Session->setFlash('Zvolené obchodní jednání neexistuje');
			$this->redirect($this->index_link);
		}
		
		if (!$this->BusinessSession->checkUser($this->user, $business_session['BusinessSession']['user_id'])) {
			$this->Session->setFlash('Neoprávněný přístup. Nemáte právo pozvat kontaktní osoby na toto jednání.');
			$this->redirect($this->index_link);
		}
		
		$contact_people = $this->BusinessSession->BusinessSessionsContactPerson->ContactPerson->find('all', array(
			'conditions' => array(
				'ContactPerson.business_partner_id' => $business_session['BusinessSession']['business_partner_id'],
				'ContactPerson.active' => true
			),
			'contain' => array('BusinessPartner'),
			'order' => array('last_name' => 'asc')
		));
		
		$this->set('contact_people', $contact_people);
		$this->set('business_session', $business_session);
		// do leveho menu pridam polozku pro detaily partnera
		$this->left_menu_list[] = 'business_session_detailed';
		
		if (isset($this->data)) {
			$this->data = array_filter($this->data['BusinessSessionsContactPerson'], array('BusinessSessionsController', 'filter_not_checked'));
			$this->BusinessSession->BusinessSessionsContactPerson->deleteAll(
				array('business_session_id' => $business_session['BusinessSession']['id'])
			);
			$this->BusinessSession->BusinessSessionsContactPerson->saveAll($this->data);
			$this->Session->setFlash('Přizvané kontaktní osoby byly upraveny');
			$this->redirect(array('controller' => 'business_sessions', 'action' => 'view', $business_session['BusinessSession']['id']));
		} else {
			foreach ($business_session['BusinessSessionsContactPerson'] as $contact_person) {
				$this->data['BusinessSessionsContactPerson'][$contact_person['contact_person_id']] = $contact_person;
			}
		}
	}
	
	function user_close($id = null) {
		if (!$id) {
			$this->Session->setFlash('Není určeno obchodní jednání, které chcete uzavřít');
			$this->redirect($this->index_link);
		}
		
		$business_session = $this->BusinessSession->find('first', array(
			'conditions' => array('BusinessSession.id' => $id),
			'contain' => array()
		));
		
		if (empty($business_session)) {
			$this->Session->setFlash('Zvolené obchodní jednání neexistuje');
			$this->redirect($this->index_link);
		}
		
		if (!$this->BusinessSession->checkUser($this->user, $business_session['BusinessSession']['user_id'])) {
			$this->Session->setFlash('Neoprávněný přístup. Nemůžete uzavřít toto obchodní jednání.');
			$this->redirect($this->index_link);
		}
		
		$business_session['BusinessSession']['business_session_state_id'] = 2;
		if ($this->BusinessSession->save($business_session)) {
			$this->Session->setFlash('Obchodní jednání bylo uzavřeno');
		} else {
			$this->Session->setFlash('Obchodní jednání se nepodařilo uzavřít, opakujte prosím akci');
		}
		$this->redirect(array('controller' => 'business_sessions', 'action' => 'view', $id));
	}
	
	function user_storno($id = null) {
		if (!$id) {
			$this->Session->setFlash('Není určeno obchodní jednání, které chcete stornovat');
			$this->redirect($this->index_link);
		}
		
		$business_session = $this->BusinessSession->find('first', array(
			'conditions' => array('BusinessSession.id' => $id),
			'contain' => array()
		));
		
		if (empty($business_session)) {
			$this->Session->setFlash('Zvolené obchodní jednání neexistuje');
			$this->redirect($this->index_link);
		}
		
		if (!$this->BusinessSession->checkUser($this->user, $business_session['BusinessSession']['user_id'])) {
			$this->Session->setFlash('Neoprávněný přístup. Nemůžete stornovat toto obchodní jednání.');
			$this->redirect($this->index_link);
		}
		
		$business_session['BusinessSession']['business_session_state_id'] = 3;
		if ($this->BusinessSession->save($business_session)) {
			$this->Session->setFlash('Obchodní jednání bylo stornováno');
		} else {
			$this->Session->setFlash('Obchodní jednání se nepodařilo stornovat, opakujte prosím akci');
		}
		$this->redirect(array('controller' => 'business_sessions', 'action' => 'view', $id));
	}
	
	function user_delete($id = null) {
		if (!$id) {
			$this->Session->setFlash('Není určeno obchodní jednání, které chcete smazat.');
			$this->redirect($this->index_link);
		}
		
		$business_session = $this->BusinessSession->find('first', array(
			'conditions' => array('BusinessSession.id' => $id),
			'contain' => array()
		));

		if (!$this->BusinessSession->checkUser($this->user, $business_session['BusinessSession']['user_id'])) {
			$this->Session->setFlash('Neoprávněný přístup. Nemůžete smazat toto obchodní jednání.');
			$this->redirect($this->index_link);
		}

		if ($this->BusinessSession->delete($id)) {
			$this->Session->setFlash('Obchodní jednání bylo odstraněno.');
		} else {
			$this->Session->setFlash('Obchodní jednání se nepodařilo odstranit.');
		}
		$this->redirect($this->index_link);
	}
	
	function filter_not_checked($a) {
		return ($a['contact_person_id'] != 0);
	}
}