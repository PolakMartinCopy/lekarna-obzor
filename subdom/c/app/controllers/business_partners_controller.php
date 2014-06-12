<?php
class BusinessPartnersController extends AppController {
	var $name = 'BusinessPartners';
	
	var $index_link = array('controller' => 'business_partners', 'action' => 'index');
	
	var $paginate = array(
		'limit' => 30,
		'order' => array('BusinessPartner.name' => 'asc'),
	);
	
	var $bonity = array(1 => 'A1', 'A2', 'A3', 'B1', 'B2', 'B3', 'C1', 'C2', 'C3');
	
	// zakladni nastaveni pro leve menu
	// v konkretni action se da pridat,
	// nebo upravit
	var $left_menu_list = array('business_partners');
	
	function beforeFilter(){
		parent::beforeFilter();
		$this->set('active_tab', 'business_partners');
	}
	
	function beforeRender(){
		parent::beforeRender();
		$this->set('left_menu_list', $this->left_menu_list);
	}
	
	function user_index() {
		if (!isset($this->data)) {
			$this->data = array();
		}
		
		$user_id = $this->user['User']['id'];
		
		// pokud chce uzivatel resetovat filtr
		if (isset($this->params['named']['reset'])) {
			// smazu informace ze session
			$this->Session->delete('Search.BusinessPartnerForm');
		}
		
		$conditions = array();
		if ($this->user['User']['user_type_id'] == 3) {
			$conditions = array('BusinessPartner.user_id' => $user_id);
		}
		
		$attributes = $this->BusinessPartner->attributes;
		$this->set('attributes', Set::extract('/value', $attributes));
		
		// pokud jsou zadany parametry pro vyhledavani ve formulari
		if ( isset($this->data['BusinessPartner']['search_form']) && $this->data['BusinessPartner']['search_form'] == 1 ){
			$this->Session->write('Search.BusinessPartnerForm', $this->data);
			$conditions = $this->BusinessPartner->do_form_search($conditions, $this->data);
		// jeste zkusim, jestli nejsou zadany v session
		} elseif ($this->Session->check('Search.BusinessPartnerForm')) {
			$this->data = $this->Session->read('Search.BusinessPartnerForm');
			$conditions = $this->BusinessPartner->do_form_search($conditions, $this->data);
		}
		
		$this->paginate['BusinessPartner'] = array(
			'conditions' => $conditions,
			'limit' => 30,
			'contain' => array('User'),
			'fields' => array('BusinessPartner.*', 'Address.*', 'User.*', 'CONCAT(User.last_name, " ", User.first_name) as full_name'),
			'joins' => array(
				array(
					'table' => 'addresses',
					'type' => 'INNER',
					'alias' => 'Address',
					'conditions' => array(
						'BusinessPartner.id = Address.business_partner_id',
						'Address.address_type_id = 1'
					)
				)
			)
		);
		
		$business_partners = $this->paginate('BusinessPartner');

		$this->set('business_partners', $business_partners);
		
		$this->set('bonity', $this->bonity);
		
		$find = $this->paginate['BusinessPartner'];
		unset($find['limit']);
		unset($find['fields']);
		$this->set('find', $find);
		
		$export_fields = array(
			array('field' => 'BusinessPartner.id', 'position' => '["BusinessPartner"]["id"]', 'alias' => 'BusinessPartner.id'),
			array('field' => 'BusinessPartner.name', 'position' => '["BusinessPartner"]["name"]', 'alias' => 'BusinessPartner.name'),
			array('field' => 'BusinessPartner.ico', 'position' => '["BusinessPartner"]["ico"]', 'alias' => 'BusinessPartner.ico'),
			array('field' => 'BusinessPartner.dic', 'position' => '["BusinessPartner"]["dic"]', 'alias' => 'BusinessPartner.dic'),
			array('field' => 'Address.name', 'position' => '["Address"]["name"]', 'alias' => 'Address.name'),
			array('field' => 'Address.person_first_name', 'position' => '["Address"]["person_first_name"]', 'alias' => 'Address.person_first_name'),
			array('field' => 'Address.person_last_name', 'position' => '["Address"]["person_last_name"]', 'alias' => 'Address.person_last_name'),
			array('field' => 'Address.street', 'position' => '["Address"]["street"]', 'alias' => 'Address.street'),
			array('field' => 'Address.city', 'position' => '["Address"]["city"]', 'alias' => 'Address.city'),
			array('field' => 'Address.zip', 'position' => '["Address"]["zip"]', 'alias' => 'Address.zip'),
			array('field' => 'Address.region', 'position' => '["Address"]["region"]', 'alias' => 'Address.region'),
			array('field' => 'CONCAT(User.first_name, " ", User.last_name) AS fullname', 'position' => '[0]["fullname"]', 'alias' => 'User.fullname'),
		);
		$this->set('export_fields', $export_fields);
	}
	
	function user_view($id = null) {
		
		$sort_field = '';
		if (isset($this->passedArgs['sort'])) {
			$sort_field = $this->passedArgs['sort'];
		} 
		
		$sort_direction = '';
		if (isset($this->passedArgs['direction'])) {
			$sort_direction = $this->passedArgs['direction'];
		}
		
		// do leveho menu pridam polozku pro detaily partnera
		$this->left_menu_list[] = 'business_partner_detailed';
		
		if (!$id) {
			$this->Session->setFlash('Není určen obchodní partner, kterého chcete zobrazit');
			$this->redirect($this->index_link);
		}
		
		$business_partner = $this->BusinessPartner->find('first', array(
			'conditions' => array('BusinessPartner.id' => $id),
			'contain' => array('User')
		));
		
		if (empty($business_partner)) {
			$this->Session->setFlash('Zvolený obchodní partner neexistuje');
			$this->redirect($this->index_link);
		}

		if (!$this->BusinessPartner->checkUser($this->user, $business_partner['BusinessPartner']['user_id'])) {
			$this->Session->setFlash('Nepovolený přístup. Nemáte právo pro zobrazení tohoto obchodního partnera');
			$this->redirect($this->index_link);
		}
		
		$this->set('bonity', $this->bonity);
		
		list($seat_address, $delivery_address, $invoice_address) = $this->BusinessPartner->Address->get_addresses($id);
		
		// ADRESY POBOCEK
		$addresses_conditions = array(
			'Address.business_partner_id' => $id,
			'Address.address_type_id' => 5
		);
		
		if (isset($this->params['named']['reset']) && $this->params['named']['reset'] == 'address') {
			$this->Session->delete('Search.AddressSearch');
			$this->redirect(array('controller' => 'business_partners', 'action' => 'view', $id, 'tab' => 5));
		}
	
		// pokud chci vysledky vyhledavani
		if ( isset($this->data['AddressSearch']['Address']['search_form']) && $this->data['AddressSearch']['Address']['search_form'] == 1 ){
			$this->Session->write('Search.AddressSearch', $this->data['AddressSearch']);
			$addresses_conditions = $this->BusinessPartner->Address->do_form_search($addresses_conditions, $this->data['AddressSearch']);
		// jeste zkusim, jestli nejsou zadany v session
		} elseif ($this->Session->check('Search.AddressSearch')) {
			$this->data['AddressSearch'] = $this->Session->read('Search.AddressSearch');
			$addresses_conditions = $this->BusinessPartner->do_form_search($addresses_conditions, $this->data['AddressSearch']);
		}

		unset($this->passedArgs['sort']);
		unset($this->passedArgs['direction']);
		if (isset($this->params['named']['tab']) && $this->params['named']['tab'] == 5) {
			$this->passedArgs['sort'] = $sort_field;
			$this->passedArgs['direction'] = $sort_direction;
		}
		
		$this->paginate = array();
		$this->paginate['Address'] = array(
			'conditions' => $addresses_conditions,
			'contain' => array(),
			'limit' => 30
		);

		$branch_addresses = $this->paginate('Address');
		
		$this->set('branch_addresses_paging', $this->params['paging']);
		
		$branch_addresses_find = $this->paginate['Address'];
		unset($branch_addresses_find['limit']);
		$this->set('branch_addresses_find', $branch_addresses_find);
		$this->set('model_name', 'BusinessPartner->Address');
		
		$branch_addresses_export_fields = array(
			array('field' => 'Address.name', 'position' => '["Address"]["name"]', 'alias' => 'Address.name'),
			array('field' => 'Address.person_first_name', 'position' => '["Address"]["person_first_name"]', 'alias' => 'Address.person_first_name'),
			array('field' => 'Address.person_last_name', 'position' => '["Address"]["person_last_name"]', 'alias' => 'Address.person_last_name'),
			array('field' => 'Address.street', 'position' => '["Address"]["street"]', 'alias' => 'Address.street'),
			array('field' => 'Address.number', 'position' => '["Address"]["number"]', 'alias' => 'Address.number'),
			array('field' => 'Address.o_number', 'position' => '["Address"]["o_number"]', 'alias' => 'Address.o_number'),
			array('field' => 'Address.city', 'position' => '["Address"]["city"]', 'alias' => 'Address.city'),
			array('field' => 'Address.zip', 'position' => '["Address"]["zip"]', 'alias' => 'Address.zip'),
			array('field' => 'Address.region', 'position' => '["Address"]["region"]', 'alias' => 'Address.region')
		);
		$this->set('branch_addresses_export_fields', $branch_addresses_export_fields);
		
		
		// KONTAKTNI OSOBY TOHOTO OBCHODNIHO PARTNERA
		$contact_people_conditions = array(
			'ContactPerson.business_partner_id' => $id,
			'ContactPerson.active' => true
		);
		
		if (isset($this->params['named']['reset']) && $this->params['named']['reset'] == 'contact_people') {
			$this->Session->delete('Search.ContactPersonSearch');
			$this->redirect(array('controller' => 'business_partners', 'action' => 'view', $id, 'tab' => 7));
		}

		// pokud chci vysledky vyhledavani
		if ( isset($this->data['ContactPersonSearch']['ContactPerson']['search_form']) && $this->data['ContactPersonSearch']['ContactPerson']['search_form'] == 1 ){
			$this->Session->write('Search.ContactPersonSearch', $this->data['ContactPersonSearch']);
			$contact_people_conditions = $this->BusinessPartner->ContactPerson->do_form_search($contact_people_conditions, $this->data['ContactPersonSearch']);
		} elseif ($this->Session->check('Search.ContactPersonSearch')) {
			$this->data['ContactPersonSearch'] = $this->Session->read('Search.ContactPersonSearch');
			$contact_people_conditions = $this->BusinessPartner->ContactPerson->do_form_search($contact_people_conditions, $this->data['ContactPersonSearch']);
		}
		
		unset($this->passedArgs['sort']);
		unset($this->passedArgs['direction']);
		if (isset($this->params['named']['tab']) && $this->params['named']['tab'] == 7) {
			$this->passedArgs['sort'] = $sort_field;
			$this->passedArgs['direction'] = $sort_direction;
		}

		$this->paginate['ContactPerson'] = array(
			'conditions' => $contact_people_conditions,
			'limit' => 30
		);
		$contact_people = $this->paginate('ContactPerson');
		
		$this->set('contact_people_paging', $this->params['paging']);
		
		$contact_people_find = $this->paginate['ContactPerson'];
		unset($contact_people_find['limit']);
		unset($contact_people_find['fields']);
		$this->set('contact_people_find', $contact_people_find);
		
		$contact_people_export_fields = array(
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
		$this->set('contact_people_export_fields', $contact_people_export_fields);
		
		// OBCHODNI JEDNANI TOHOTO OBCHODNIHO PARTNERA
		$business_sessions_conditions[] = 'BusinessSession.business_partner_id = ' . $id;
		
		if (isset($this->params['named']['reset']) && $this->params['named']['reset'] == 'business_session') {
			$this->Session->delete('Search.BusinessSessionSearch');
			$this->redirect(array('controller' => 'business_partners', 'action' => 'view', $id, 'tab' => 8));
		}

		// pokud chci vysledky vyhledavani
		if ( isset($this->data['BusinessSessionSearch']['BusinessSession']['search_form']) && $this->data['BusinessSessionSearch']['BusinessSession']['search_form'] == 1 ){
			$this->Session->write('Search.BusinessSessionSearch', $this->data['BusinessSessionSearch']);
			$business_sessions_conditions = $this->BusinessPartner->BusinessSession->do_form_search($business_sessions_conditions, $this->data['BusinessSessionSearch']);
		} elseif ($this->Session->check('Search.BusinessSessionSearch')) {
			$this->data['BusinessSessionSearch'] = $this->Session->read('Search.BusinessSessionSearch');
			$business_sessions_conditions = $this->BusinessPartner->BusinessSession->do_form_search($business_sessions_conditions, $this->data['BusinessSessionSearch']);
		}
		
		unset($this->passedArgs['sort']);
		unset($this->passedArgs['direction']);
		if (isset($this->params['named']['tab']) && $this->params['named']['tab'] == 8) {
			$this->passedArgs['sort'] = $sort_field;
			$this->passedArgs['direction'] = $sort_direction;
		}
		
		$this->paginate = array();
		$this->paginate['BusinessSession'] = array(
			'conditions' => $business_sessions_conditions,
			'contain' => array(
				'BusinessPartner',
				'BusinessSessionState',
				'BusinessSessionType',
				'User'
			),
			'order' => array('BusinessSession.date' => 'desc'),
			'fields' => array('*', 'SUM(Cost.amount) as celkem'),
			'limit' => 30,
			'group' => 'BusinessSession.id',
			'joins' => array(
				array(
					'table' => 'costs',
					'alias' => 'Cost',
					'type' => 'LEFT',
					'conditions' => array(
						'Cost.business_session_id = BusinessSession.id'
					)
				)
			)
		);
		$business_sessions = $this->paginate('BusinessSession');
		
		$this->set('business_sessions_paging', $this->params['paging']);
		
		$this->set('business_session_types', $this->BusinessPartner->BusinessSession->BusinessSessionType->find('list'));
		
		$business_sessions_find = $this->paginate['BusinessSession'];
		unset($business_sessions_find['limit']);
		unset($business_sessions_find['fields']);
		$this->set('business_sessions_find', $business_sessions_find);
		
		$business_sessions_export_fields = array(
			array('field' => 'BusinessSession.id', 'position' => '["BusinessSession"]["id"]', 'alias' => 'BusinessSession.id'),
			array('field' => 'BusinessSession.date', 'position' => '["BusinessSession"]["date"]', 'alias' => 'BusinessSession.date'),
			array('field' => 'BusinessSession.created', 'position' => '["BusinessSession"]["created"]', 'alias' => 'BusinessSession.created'),
			array('field' => 'BusinessPartner.name', 'position' => '["BusinessPartner"]["name"]', 'alias' => 'BusinessPartner.name'),
			array('field' => 'BusinessSessionType.name', 'position' => '["BusinessSessionType"]["name"]', 'alias' => 'BusinessSessionType.name'),
			array('field' => 'BusinessSessionState.name', 'position' => '["BusinessSessionState"]["name"]', 'alias' => 'BusinessSessionState.name'),
			array('field' => 'CONCAT(User.last_name, " ", User.first_name) AS full_name', 'position' => '[0]["full_name"]', 'alias' => 'User.fullname'),
			array('field' => 'SUM(Cost.amount) AS total_amount', 'position' => '[0]["total_amount"]', 'alias' => 'Cost.total_amount')
		);
		$this->set('business_sessions_export_fields', $business_sessions_export_fields);
		
		// DOKUMENTY OBCHODNIHO PARTNERA
		$documents_conditions = '';

		if (isset($this->params['named']['reset']) && $this->params['named']['reset'] == 'documents') {
			$this->Session->delete('Search.DocumentForm2');
			$this->redirect(array('controller' => 'business_partners', 'action' => 'view', $business_partner['BusinessPartner']['id'], 'tab' => 6));
		}
		
		// pokud chci vysledky vyhledavani
		if ( isset($this->data['DocumentForm2']['Document']['search_form']) && $this->data['DocumentForm2']['Document']['search_form'] == 1 ){
			$this->Session->write('Search.DocumentForm2', $this->data['DocumentForm2']);
			$documents_conditions = $this->BusinessPartner->Document->do_form_search($documents_conditions, $this->data['DocumentForm2']);
		} elseif ($this->Session->check('Search.DocumentForm2')) {
			$this->data['DocumentForm2'] = $this->Session->read('Search.DocumentForm2');
			$documents_conditions = $this->BusinessPartner->Document->do_form_search($documents_conditions, $this->data['DocumentForm2']);
		}
		
		$query = '
		SELECT *
		FROM
			((SELECT Document.*
			FROM
				documents AS Document
			WHERE
				Document.business_partner_id = ' . $id . '
			)
			UNION (
			SELECT Document.*
			FROM documents AS Document, offers AS Offer, business_sessions AS BusinessSession
			WHERE
				Document.offer_id = Offer.id AND
				Offer.business_session_id = BusinessSession.id AND
				BusinessSession.business_partner_id = ' . $id . '
			)) AS Document
		';
		
		if (!empty($documents_conditions)) {
			$query = $query . 'WHERE ' . $documents_conditions;
		}
		
		$documents = $this->BusinessPartner->Document->query($query);
		
		// POLOZKY SKLADU OBCHODNIHO PARTNERA
		$store_items_conditions = array('StoreItem.business_partner_id' => $id);
		
		if (isset($this->params['named']['reset']) && $this->params['named']['reset'] == 'store_items') {
			$this->Session->delete('Search.StoreItemForm2');
			$this->redirect(array('controller' => 'business_partners', 'action' => 'view', $business_partner['BusinessPartner']['id'], 'tab' => 9));
		}

		// pokud chci vysledky vyhledavani
		if ( isset($this->data['StoreItemForm2']['StoreItem']['search_form']) && $this->data['StoreItemForm2']['StoreItem']['search_form'] == 1 ){
			$this->Session->write('Search.StoreItemForm2', $this->data['StoreItemForm2']);
			$store_items_conditions = $this->BusinessPartner->StoreItem->do_form_search($store_items_conditions, $this->data['StoreItemForm2']);
		} elseif ($this->Session->check('Search.StoreItemForm2')) {
			$this->data['StoreItemForm2'] = $this->Session->read('Search.StoreItemForm2');
			$store_items_conditions = $this->BusinessPartner->StoreItem->do_form_search($store_items_conditions, $this->data['StoreItemForm2']);
		}

		unset($this->passedArgs['sort']);
		unset($this->passedArgs['direction']);
		if (isset($this->params['named']['tab']) && $this->params['named']['tab'] == 9) {
			$this->passedArgs['sort'] = $sort_field;
			$this->passedArgs['direction'] = $sort_direction;
		}
		
		// musim si k StoreItem naimportovat unit, aby fungovalo razeni
		App::import('Model', 'Unit');
		$this->BusinessPartner->StoreItem->Unit = new Unit;
		
		// chci znat pocet polozek skladu odberatele
		$count = $this->BusinessPartner->StoreItem->find('count', array(
			'conditions' => $store_items_conditions
		));
		// pomoci strankovani (abych je mohl jednoduse radit) vyberu VSECHNY polozky skladu odberatele
		$this->paginate['StoreItem'] = array(
			'conditions' => $store_items_conditions,
			'contain' => array(),
			'joins' => array(
				array(
					'table' => 'products',
					'alias' => 'Product',
					'type' => 'left',
					'conditions' => array('StoreItem.product_id = Product.id')
				),
				array(
					'table' => 'units',
					'alias' => 'Unit',
					'type' => 'left',
					'conditions' => array('Product.unit_id = Unit.id')
				)
			),
			'fields' => array(
				'StoreItem.id',
				'StoreItem.quantity',
				'StoreItem.item_total_price',
					
				'Product.id',
				'Product.name',
				'Product.vzp_code',
				'Product.group_code',
				'Product.price',
					
				'Unit.shortcut'
			),
			'limit' => $count,
			'order' => array('Product.vzp_code' => 'asc')
		);
		$store_items = $this->paginate('StoreItem');

		// budu pocitat celkove soucty polozek a soucet ceny vsech polozek
		$store_items_quantity = 0;
		$store_items_price = 0;
		// k polozkam skladu doplnim datum posledniho prodeje, ve kterem byla polozka obsazena
		foreach ($store_items as &$store_item) {
			$store_items_quantity += $store_item['StoreItem']['quantity'];
			$store_items_price += $store_item['StoreItem']['item_total_price'];
			
			$last_sale = $this->BusinessPartner->Sale->ProductsTransaction->find('first', array(
				'conditions' => array(
					'Sale.business_partner_id' => $id,
					'ProductsTransaction.product_id' => $store_item['Product']['id'],
					// kdyz mam sale v contain, nebere to Sale::beforeFind, takze omezeni na to, ze je to typ "prodej" musim pridat implicitne
					'Sale.transaction_type_id' => 3
				),
				'contain' => array('Sale'),
				'fields' => array('Sale.date'),
				'order' => array('Sale.date' => 'desc')
			));

			$store_item['StoreItem']['last_sale_date'] = null;
			if (!empty($last_sale)) {
				$store_item['StoreItem']['last_sale_date'] = $last_sale['Sale']['date'];
			}
		}
		$this->set('store_items_quantity', $store_items_quantity);
		$this->set('store_items_price', $store_items_price);
		
		$this->set('store_items_paging', $this->params['paging']);
		
		$store_items_find = $this->paginate['StoreItem'];
		unset($store_items_find['limit']);
		unset($store_items_find['fields']);
		$this->set('store_items_find', $store_items_find);
		
		$store_items_export_fields = array(
			array('field' => 'StoreItem.id', 'position' => '["StoreItem"]["id"]', 'alias' => 'StoreItem.id'),
			array('field' => 'Product.id', 'position' => '["Product"]["id"]', 'alias' => 'Product.id'),
			array('field' => 'Product.vzp_code', 'position' => '["Product"]["vzp_code"]', 'alias' => 'Product.vzp_code'),
			array('field' => 'Product.name', 'position' => '["Product"]["name"]', 'alias' => 'Product.name'),
			array('field' => 'StoreItem.quantity', 'position' => '["StoreItem"]["quantity"]', 'alias' => 'StoreItem.quantity'),
			array('field' => 'Unit.shortcut', 'position' => '["Unit"]["shortcut"]', 'alias' => 'Unit.shortcut'),
			array('field' => 'Product.price', 'position' => '["Product"]["price"]', 'alias' => 'Product.price'),
			array('field' => 'StoreItem.item_total_price', 'position' => '["StoreItem"]["item_total_price"]', 'alias' => 'StoreItem.item_total_price'),
			array('field' => 'Product.group_code', 'position' => '["Product"]["group_code"]', 'alias' => 'Product.group_code'),
//			array('field' => 'StoreItem.last_sale_date', 'position' => '["StoreItem"]["last_sale_date"]', 'alias' => 'StoreItem.last_sale_date'),
		);
		$this->set('store_items_export_fields', $store_items_export_fields);
		
		// DODACI LISTY
		$delivery_notes_conditions = array(
			'DeliveryNote.business_partner_id' => $id,
			'Address.address_type_id' => 1
		);
		
		if (isset($this->params['named']['reset']) && $this->params['named']['reset'] == 'delivery_notes') {
			$this->Session->delete('Search.DeliveryNoteForm2');
			$this->redirect(array('controller' => 'business_partners', 'action' => 'view', $business_partner['BusinessPartner']['id'], 'tab' => 10));
		}
		
		// pokud chci vysledky vyhledavani
		if ( isset($this->data['DeliveryNoteForm2']['DeliveryNote']['search_form']) && $this->data['DeliveryNoteForm2']['DeliveryNote']['search_form'] == 1 ){
			$this->Session->write('Search.DeliveryNoteForm2', $this->data['DeliveryNoteForm2']);
			$delivery_notes_conditions = $this->BusinessPartner->DeliveryNote->do_form_search($delivery_notes_conditions, $this->data['DeliveryNoteForm2']);
		} elseif ($this->Session->check('Search.DeliveryNoteForm2')) {
			$this->data['DeliveryNoteForm2'] = $this->Session->read('Search.DeliveryNoteForm2');
			$delivery_notes_conditions = $this->BusinessPartner->DeliveryNote->do_form_search($delivery_notes_conditions, $this->data['DeliveryNoteForm2']);
		}
		
		unset($this->passedArgs['sort']);
		unset($this->passedArgs['direction']);
		if (isset($this->params['named']['tab']) && $this->params['named']['tab'] == 10) {
			$this->passedArgs['sort'] = $sort_field;
			$this->passedArgs['direction'] = $sort_direction;
		}
		
		// musim si k StoreItem naimportovat unit, aby fungovalo razeni
		App::import('Model', 'Product');
		$this->BusinessPartner->DeliveryNote->Product = new Product;
		App::import('Model', 'Unit');
		$this->BusinessPartner->DeliveryNote->Unit = new Unit;
		
		$this->paginate['DeliveryNote'] = array(
			'conditions' => $delivery_notes_conditions,
			'limit' => 30,
			'contain' => array(),
			'joins' => array(
				array(
					'table' => 'products_transactions',
					'alias' => 'ProductsTransaction',
					'type' => 'left',
					'conditions' => array('DeliveryNote.id = ProductsTransaction.transaction_id')
				),
				array(
					'table' => 'products',
					'alias' => 'Product',
					'type' => 'left',
					'conditions' => array('ProductsTransaction.product_id = Product.id')
				),
				array(
					'table' => 'business_partners',
					'alias' => 'BusinessPartner',
					'type' => 'left',
					'conditions' => array('BusinessPartner.id = ' . 'DeliveryNote.business_partner_id')
				),
				array(
					'table' => 'addresses',
					'alias' => 'Address',
					'type' => 'left',
					'conditions' => array('Address.business_partner_id = BusinessPartner.id')
				),
				array(
					'table' => 'units',
					'alias' => 'Unit',
					'type' => 'left',
					'conditions' => array('Product.unit_id = Unit.id')
				),
				array(
					'table' => 'transaction_types',
					'alias' => 'TransactionType',
					'type' => 'LEFT',
					'conditions' => array('DeliveryNote.transaction_type_id = TransactionType.id')
				),
				array(
					'table' => 'users',
					'alias' => 'User',
					'type' => 'left',
					'conditions' => array('DeliveryNote.user_id = User.id')
				)
			),
			'fields' => array(
				'DeliveryNote.id',
				'DeliveryNote.date',
				'DeliveryNote.code',
				'DeliveryNote.total_price',
				'DeliveryNote.margin',
		
				'ProductsTransaction.id',
				'ProductsTransaction.quantity',
				'ProductsTransaction.unit_price',
				'ProductsTransaction.product_margin',
		
				'Product.id',
				'Product.name',
				'Product.vzp_code',
				'Product.group_code',
					
				'BusinessPartner.id',
				'BusinessPartner.name',
					
				'Unit.id',
				'Unit.shortcut',
			),
			'order' => array(
				'DeliveryNote.date' => 'desc',
				'DeliveryNote.time' => 'desc'
			)
		);
		$delivery_notes = $this->paginate('DeliveryNote');

		$this->set('delivery_notes_paging', $this->params['paging']);
		
		$delivery_notes_find = $this->paginate['DeliveryNote'];
		unset($delivery_notes_find['limit']);
		unset($delivery_notes_find['fields']);
		$this->set('delivery_notes_find', $delivery_notes_find);
		
		$delivery_notes_export_fields = $this->BusinessPartner->DeliveryNote->export_fields();
		$this->set('delivery_notes_export_fields', $delivery_notes_export_fields);
		
		// seznam uzivatelu pro select ve filtru
		$delivery_notes_users_conditions = array();
		if ($this->user['User']['user_type_id'] == 3) {
			$delivery_notes_users_conditions = array('User.id' => $this->user['User']['id']);
		}
		$delivery_notes_users = $this->BusinessPartner->DeliveryNote->User->find('all', array(
			'conditions' => $delivery_notes_users_conditions,
			'contain' => array(),
			'fields' => array('User.id', 'User.first_name', 'User.last_name')
		));
		$delivery_notes_users = Set::combine($delivery_notes_users, '{n}.User.id', array('{0} {1}', '{n}.User.first_name', '{n}.User.last_name'));
		$this->set('delivery_notes_users', $delivery_notes_users);
		
		// PRODEJE
		$sales_conditions = array(
			'Sale.business_partner_id' => $id,
			'Address.address_type_id' => 1
		);
		
		if (isset($this->params['named']['reset']) && $this->params['named']['reset'] == 'sales') {
			$this->Session->delete('Search.SaleForm2');
			$this->redirect(array('controller' => 'business_partners', 'action' => 'view', $business_partner['BusinessPartner']['id'], 'tab' => 11));
		}
		
		// pokud chci vysledky vyhledavani
		if ( isset($this->data['SaleForm2']['Sale']['search_form']) && $this->data['SaleForm2']['Sale']['search_form'] == 1 ){
			$this->Session->write('Search.SaleForm2', $this->data['SaleForm2']);
			$sales_conditions = $this->BusinessPartner->Sale->do_form_search($sales_conditions, $this->data['SaleForm2']);
		} elseif ($this->Session->check('Search.SaleForm2')) {
			$this->data['SaleForm2'] = $this->Session->read('Search.SaleForm2');
			$sales_conditions = $this->BusinessPartner->Sale->do_form_search($sales_conditions, $this->data['SaleForm2']);
		}
		
		unset($this->passedArgs['sort']);
		unset($this->passedArgs['direction']);
		if (isset($this->params['named']['tab']) && $this->params['named']['tab'] == 11) {
			$this->passedArgs['sort'] = $sort_field;
			$this->passedArgs['direction'] = $sort_direction;
		}
		
		// musim si k StoreItem naimportovat unit, aby fungovalo razeni
		App::import('Model', 'Product');
		$this->BusinessPartner->Sale->Product = new Product;
		App::import('Model', 'Unit');
		$this->BusinessPartner->Sale->Unit = new Unit;
		
		$this->paginate['Sale'] = array(
			'conditions' => $sales_conditions,
			'limit' => 30,
			'contain' => array(),
			'joins' => array(
				array(
					'table' => 'products_transactions',
					'alias' => 'ProductsTransaction',
					'type' => 'left',
					'conditions' => array('Sale.id = ProductsTransaction.transaction_id')
				),
				array(
					'table' => 'products',
					'alias' => 'Product',
					'type' => 'left',
					'conditions' => array('ProductsTransaction.product_id = Product.id')
				),
				array(
					'table' => 'business_partners',
					'alias' => 'BusinessPartner',
					'type' => 'left',
					'conditions' => array('BusinessPartner.id = ' . 'Sale.business_partner_id')
				),
				array(
					'table' => 'addresses',
					'alias' => 'Address',
					'type' => 'left',
					'conditions' => array('Address.business_partner_id = BusinessPartner.id')
				),
				array(
					'table' => 'units',
					'alias' => 'Unit',
					'type' => 'left',
					'conditions' => array('Product.unit_id = Unit.id')
				),
				array(
					'table' => 'transaction_types',
					'alias' => 'TransactionType',
					'type' => 'LEFT',
					'conditions' => array('Sale.transaction_type_id = TransactionType.id')
				),
				array(
					'table' => 'users',
					'alias' => 'User',
					'type' => 'left',
					'conditions' => array('Sale.user_id = User.id')
				)
			),
			'fields' => array(
				'Sale.id',
				'Sale.date',
				'Sale.code',
				'Sale.abs_quantity',
				'Sale.abs_total_price',
				'Sale.abs_margin',
		
				'ProductsTransaction.id',
				'ProductsTransaction.unit_price',
				'ProductsTransaction.product_margin',
		
				'Product.id',
				'Product.name',
				'Product.vzp_code',
				'Product.group_code',
					
				'BusinessPartner.id',
				'BusinessPartner.name',
					
				'Unit.id',
				'Unit.shortcut',
			),
			'order' => array(
				'Sale.date' => 'desc',
				'Sale.time' => 'desc'
			)
		);
		$sales = $this->paginate('Sale');
		$this->set('sales_paging', $this->params['paging']);
		
		$sales_find = $this->paginate['Sale'];
		unset($sales_find['limit']);
		unset($sales_find['fields']);
		$this->set('sales_find', $sales_find);
		
		$sales_export_fields = $this->BusinessPartner->Sale->export_fields();
		$this->set('sales_export_fields', $sales_export_fields);
		
		// seznam uzivatelu pro select ve filtru
		$sales_users_conditions = array();
		if ($this->user['User']['user_type_id'] == 3) {
			$sales_users_conditions = array('User.id' => $this->user['User']['id']);
		}
		$sales_users = $this->BusinessPartner->Sale->User->find('all', array(
				'conditions' => $sales_users_conditions,
				'contain' => array(),
				'fields' => array('User.id', 'User.first_name', 'User.last_name')
		));
		$sales_users = Set::combine($sales_users, '{n}.User.id', array('{0} {1}', '{n}.User.first_name', '{n}.User.last_name'));
		$this->set('sales_users', $sales_users);
		
		// POHYBY
		$transactions_conditions = array(
			'Transaction.business_partner_id' => $id,
			'Address.address_type_id' => 1
		);
		
		if (isset($this->params['named']['reset']) && $this->params['named']['reset'] == 'transactions') {
			$this->Session->delete('Search.TransactionForm2');
			$this->redirect(array('controller' => 'business_partners', 'action' => 'view', $business_partner['BusinessPartner']['id'], 'tab' => 12));
		}
		
		// pokud chci vysledky vyhledavani
		if ( isset($this->data['TransactionForm2']['Transaction']['search_form']) && $this->data['TransactionForm2']['Transaction']['search_form'] == 1 ){

			$this->Session->write('Search.TransactionForm2', $this->data['TransactionForm2']);
			$transactions_conditions = $this->BusinessPartner->Transaction->do_form_search($transactions_conditions, $this->data['TransactionForm2']);
		} elseif ($this->Session->check('Search.TransactionForm2')) {
			$this->data['TransactionForm2'] = $this->Session->read('Search.TransactionForm2');
			$transactions_conditions = $this->BusinessPartner->Transaction->do_form_search($transactions_conditions, $this->data['TransactionForm2']);
		}
		
		unset($this->passedArgs['sort']);
		unset($this->passedArgs['direction']);
		if (isset($this->params['named']['tab']) && $this->params['named']['tab'] == 12) {
			$this->passedArgs['sort'] = $sort_field;
			$this->passedArgs['direction'] = $sort_direction;
		}
		
		// musim si k StoreItem naimportovat unit, aby fungovalo razeni
		App::import('Model', 'Product');
		$this->BusinessPartner->Transaction->Product = new Product;
		App::import('Model', 'Unit');
		$this->BusinessPartner->Transaction->Unit = new Unit;
		
		$this->paginate['Transaction'] = array(
			'conditions' => $transactions_conditions,
			'limit' => 30,
			'contain' => array(),
			'joins' => array(
				array(
					'table' => 'products_transactions',
					'alias' => 'ProductsTransaction',
					'type' => 'left',
					'conditions' => array('Transaction.id = ProductsTransaction.transaction_id')
				),
				array(
					'table' => 'products',
					'alias' => 'Product',
					'type' => 'left',
					'conditions' => array('ProductsTransaction.product_id = Product.id')
				),
				array(
					'table' => 'business_partners',
					'alias' => 'BusinessPartner',
					'type' => 'left',
					'conditions' => array('BusinessPartner.id = ' . 'Transaction.business_partner_id')
				),
				array(
					'table' => 'addresses',
					'alias' => 'Address',
					'type' => 'left',
					'conditions' => array('Address.business_partner_id = BusinessPartner.id')
				),
				array(
					'table' => 'units',
					'alias' => 'Unit',
					'type' => 'left',
					'conditions' => array('Product.unit_id = Unit.id')
				),
				array(
					'table' => 'transaction_types',
					'alias' => 'TransactionType',
					'type' => 'LEFT',
					'conditions' => array('Transaction.transaction_type_id = TransactionType.id')
				),
				array(
					'table' => 'users',
					'alias' => 'User',
					'type' => 'left',
					'conditions' => array('Transaction.user_id = User.id')
				)
			),
			'fields' => array(
				'Transaction.id',
				'Transaction.date',
				'Transaction.code',
				'Transaction.quantity',
				'Transaction.total_price',
				'Transaction.margin',
		
				'ProductsTransaction.id',
				'ProductsTransaction.unit_price',
				'ProductsTransaction.product_margin',
		
				'Product.id',
				'Product.name',
				'Product.vzp_code',
				'Product.group_code',
					
				'BusinessPartner.id',
				'BusinessPartner.name',
					
				'Unit.id',
				'Unit.shortcut',
					
				'TransactionType.id',
				'TransactionType.subtract'
			),
			'order' => array(
				'Transaction.date' => 'desc',
				'Transaction.time' => 'desc'
			)
		);
		$transactions = $this->paginate('Transaction');
		$this->set('transactions_paging', $this->params['paging']);
		
		$transactions_find = $this->paginate['Transaction'];
		unset($transactions_find['limit']);
		unset($transactions_find['fields']);
		$this->set('transactions_find', $transactions_find);
		
		$transactions_export_fields = $this->BusinessPartner->Transaction->export_fields();
		$this->set('transactions_export_fields', $transactions_export_fields);
		
		// seznam uzivatelu pro select ve filtru
		$transactions_users_conditions = array();
		if ($this->user['User']['user_type_id'] == 3) {
			$transactions_users_conditions = array('User.id' => $this->user['User']['id']);
		}
		$transactions_users = $this->BusinessPartner->Transaction->User->find('all', array(
				'conditions' => $transactions_users_conditions,
				'contain' => array(),
				'fields' => array('User.id', 'User.first_name', 'User.last_name')
		));
		$transactions_users = Set::combine($transactions_users, '{n}.User.id', array('{0} {1}', '{n}.User.first_name', '{n}.User.last_name'));
		$this->set('transactions_users', $transactions_users);
		
		// POZNAMKY
		$business_partner_notes = $this->BusinessPartner->BusinessPartnerNote->find('all', array(
			'conditions' => array('BusinessPartnerNote.business_partner_id' => $business_partner['BusinessPartner']['id']),
			'contain' => array(),
			'order' => array('BusinessPartnerNote.created' => 'desc')
		));
		
		$this->set('business_partner', $business_partner);
		$this->set('contact_people', $contact_people);
		$this->set('seat_address', $seat_address);
		$this->set('delivery_address', $delivery_address);
		$this->set('invoice_address', $invoice_address);
		$this->set('branch_addresses', $branch_addresses);
		$this->set('business_sessions', $business_sessions);
		$this->set('documents', $documents);
		$this->set('store_items', $store_items);
		$this->set('delivery_notes', $delivery_notes);
		$this->set('sales', $sales);
		$this->set('transactions', $transactions);
		$this->set('business_partner_notes', $business_partner_notes);
	}
	
	function user_search() {
		$this->set('user_id', $this->user['User']['id']);
		
		if (!empty($this->params['named'])) {
			$named = $this->params['named'];
			unset($named['page']);
			unset($named['sort']);
			unset($named['direction']);
			foreach ($named as $key => $item) {
				$indexes = explode('.', $key);
				$this->data[$indexes[0]][$indexes[1]] = $item;
			}
		}
		
		if (isset($this->data)) {
			$conditions = array();
			if (isset($this->data['BusinessPartner'])) {
				foreach ($this->data['BusinessPartner'] as $key => $item) {
					if ($key == 'active') {
						$conditions['BusinessPartner.active'] = $item;
					} elseif (!empty($item)) {
						$conditions[] = 'BusinessPartner.' . $key . ' LIKE \'%%' . $item . '%%\'';
					}
				}
			}
			if (isset($this->data['Address'])) {
				foreach ($this->data['Address'] as $key => $item) {
					if (!empty($item)) {
						$conditions[] = 'Address.' . $key . ' LIKE \'%%' . $item . '%%\'';
					}
				}
			}
			$this->paginate['BusinessPartner'] = array(
				'conditions' => $conditions,
				'limit' => 30,
				'contain' => array('User'),
				'fields' => array('BusinessPartner.*', 'Address.*', 'User.*', 'CONCAT(User.last_name, " ", User.first_name) as full_name'),
				'joins' => array(
					array(
						'table' => 'addresses',
						'type' => 'INNER',
						'alias' => 'Address',
						'conditions' => array(
							'BusinessPartner.id = Address.business_partner_id',
							'Address.address_type_id = 1'
						)
					)
				)
			);
			
			$business_partners = $this->paginate('BusinessPartner');
			$this->set('business_partners', $business_partners);
			$this->set('bonity', $this->bonity);
			
			$find = $this->paginate['BusinessPartner'];
			unset($find['limit']);
			$find['fields'] = $this->BusinessPartner->export_fields;
			$this->set('find', $find);
		}
	}
	
	function user_add() {
		$user_id = $this->user['User']['id'];
		$this->set('user_id', $user_id);
		
		if (isset($this->data)) {
			if (!isset($this->data['BusinessPartner']['ares_search'])) {
				if ($this->data['InvoiceAddress']['same']) {
					$invoice_address = $this->data['Address'][0];
					$invoice_address['address_type_id'] = 3;
					$this->data['Address'][] = $invoice_address;
				}
				
				if ($this->data['DeliveryAddress']['same']) {
					$delivery_address = $this->data['Address'][0];
					$delivery_address['address_type_id'] = 4;
					$this->data['Address'][] = $delivery_address;
				}
	
				if ($this->BusinessPartner->saveAll($this->data, array('validate' => 'first'))) {
					$this->Session->setFlash('Obchodní partner byl vytvořen');
					$this->redirect(array('controller' => 'business_partners', 'action' => 'view', $this->BusinessPartner->id));
				} else {
					$this->Session->setFlash('Obchodního partnera se nepodařilo vytvořit, opravte chyby ve formuláři a opakujte prosím akci');
				}
			} else {
				$this->Session->setFlash('Údaje o obchodním partnerovi byly doplněny ze systému Ares');
			}
		} else {
			if (isset($this->params['named']['data'])) {
				$data = unserialize(base64_decode($this->params['named']['data']));
				$this->data['BusinessPartner']['name'] = $data['ojm'];
				$this->data['Address'][0]['name'] = $data['ojm'];
				$this->data['BusinessPartner']['ico'] = $data['ico'];
				$address = explode(',', $data['jmn']);
				if (count($address) > 1) {
					$street = explode(' ', $address[count($address) - 1]);
					unset($address[count($address) - 1]);
					$this->data['Address'][0]['city'] = implode(', ', $address);
					$this->data['Address'][0]['number'] = $street[count($street) - 1];
					unset($street[count($street) - 1]);
					$this->data['Address'][0]['street'] = implode(' ', $street);
				} else {
					$street = explode(' ', $address[0]);
					$this->data['Address'][0]['number'] = $street[count($street) - 1];
					unset($street[count($street) - 1]);
					$this->data['Address'][0]['city'] = implode(' ', $street);
				}
				
			}
		}
	}
	
	function user_ares_search() {
		$user_id = $this->user['User']['id'];
		$this->set('user_id', $user_id);
		
		if (isset($this->data)) {
			$iso_data = array();
			foreach ($this->data['BusinessPartner'] as $key => $item) {
				$iso_data['BusinessPartner'][$key] = iconv('utf-8', 'CP1250', $item);
			}

			$url = 'http://wwwinfo.mfcr.cz/cgi-bin/ares/ares_es.cgi?jazyk=cz&obch_jm=' . urlencode($iso_data['BusinessPartner']['company']) . '&ico=' . $iso_data['BusinessPartner']['ico'] . '&cestina=cestina&obec=' . urlencode($iso_data['BusinessPartner']['city']) . '&k_fu=&maxpoc=' . $iso_data['BusinessPartner']['items'] . '&ulice=' . urlencode($iso_data['BusinessPartner']['street']) . '&cis_or=' . $iso_data['BusinessPartner']['number'] . '&cis_po=' . $iso_data['BusinessPartner']['number'] . '&setrid=' . $iso_data['BusinessPartner']['sort'] . '&pr_for=' . $iso_data['BusinessPartner']['law_form'] . '&nace=' . $iso_data['BusinessPartner']['cz_nace'] . '&xml=0&filtr=' . $iso_data['BusinessPartner']['filter'];
			if (!$ares_xml = file_get_contents($url)) {
				$this->Session->setFlash('Dokument se nepodařilo stáhnout.');
			} else {
	
				// mam vysledky z aresu, musim odlisit chybovy vysledky od regulernich a pokud jsou regulerni, tak je vypsat
				$dom = new DOMDocument('1.0');
				$dom->formatOutput = true;
				$dom->preserveWhiteSpace = false;
				libxml_use_internal_errors(true);
				if (!$dom->loadXML($ares_xml)) {
					die('dokument se nenaloudoval');
				}
				$domXPath = new DOMXPath($dom);
				
				$error = $domXPath->query('//dtt:R');
				// vystup obsahuje chybovou hlasku
				if ($error->length) {
					$flash = array();
					for ($i=0; $i<$error->length; $i++) {
						$flash []= $error->item($i)->nodeValue;
					}
					$this->Session->setFlash(implode('<br/>', $flash));
				} else {
					// uspech - musim vyparsovat data a predat k zobrazeni
					$result = $domXPath->query('//dtt:S');
					if ($result->length) {
						$search_results = array();
						foreach ($result as $r) {
							$search_result = array();
							$data = $r->childNodes;
							foreach ($data as $d) {
								switch ($d->nodeName) {
									case 'dtt:ico':
										$search_result['ico'] = $d->nodeValue;
										break;
									case 'dtt:pf':
										$search_result['pf'] = $d->nodeValue;
										break;
									case 'dtt:ojm':
										$search_result['ojm'] = $d->nodeValue;
										break;
									case 'dtt:jmn':
										$search_result['jmn'] = $d->nodeValue;
										break;
								}
							}
							$search_results []= $search_result;
						}
						$this->set('search_results', $search_results);
					} else {
						$this->Session->setFlash('Tohle by se nemělo vůbec ukázat');
					}
				}
			}
		}
	}
	
	function user_edit($id = null) {
		if (!$id) {
			$this->Session->setFlash('Není určen obchodní partner, kterého chcete upravovat');
			$this->redirect($this->index_link);
		}
		
		$business_partner = $this->BusinessPartner->find('first', array(
			'conditions' => array('BusinessPartner.id' => $id),
			'contain' => array(
				'Address' => array(
					'conditions' => array('Address.address_type_id' => 1)
				)
			)
		));
		
		if (empty($business_partner)) {
			$this->Session->setFlash('Zvolený obchodní partner neexistuje');
			$this->redirect($this->index_link);
		}

		if (!$this->BusinessPartner->checkUser($this->user, $business_partner['BusinessPartner']['user_id'])) {
				$this->Session->setFlash('Nepovolený přístup. Nemáte právo upravovat tohoto obchodního partnera');
				$this->redirect($this->index_link);
		}
		
		// do leveho menu pridam polozku pro detaily partnera
		$this->left_menu_list[] = 'business_partner_detailed';
		$seat_address = $this->BusinessPartner->Address->find('first', array(
			'conditions' => array(
				'Address.business_partner_id' => $id,
				'Address.address_type_id' => 1
			)
		));
		
		$delivery_address = $this->BusinessPartner->Address->find('first', array(
			'conditions' => array(
				'Address.business_partner_id' => $id,
				'Address.address_type_id' => 4
			)
		));
		
		$invoice_address = $this->BusinessPartner->Address->find('first', array(
			'conditions' => array(
				'Address.business_partner_id' => $id,
				'Address.address_type_id' => 3
			)
		));
		$this->set(compact('business_partner', 'seat_address', 'delivery_address', 'invoice_address'));

		
		if (isset($this->data)) {
			if ($this->BusinessPartner->saveAll($this->data)) {
				$this->Session->setFlash('Obchodní partner byl upraven');
				$this->redirect($this->index_link);
			} else {
				$this->Session->setFlash('Obchodního partnera se nepodařilo upravit, opravte chyby ve formuláři a opakujte prosím akci');
			}
		} else {
			$this->data = $business_partner;
		}
	}
	
	function user_edit_user($id = null) {
		if (!$id) {
			$this->Session->setFlash('Není určen obchodní partner, kterého chcete upravovat');
			$this->redirect($this->index_link);
		}
		
		$business_partner = $this->BusinessPartner->find('first', array(
			'conditions' => array('BusinessPartner.id' => $id),
			'contain' => array(
				'User' => array(
					'fields' => array('User.id', 'CONCAT(User.last_name, " ", User.first_name) as full_name')
				)
			)
		));
		
		if (empty($business_partner)) {
			$this->Session->setFlash('Zvolený obchodní partner neexistuje');
			$this->redirect($this->index_link);
		}
		
		$this->set('business_partner', $business_partner);
		
		$users = $this->BusinessPartner->User->find('all', array(
			'fields' => array('User.id', 'CONCAT(User.last_name, " ", User.first_name) as full_name'),
			'order' => array('full_name' => 'asc'),
			'contain' => array()
		));
		
		$autocomplete_users = array();
		foreach ($users as $key => $user) {
			$autocomplete_users[] = array('label' => $user[0]['full_name'], 'value' => $user['User']['id']);
		}

		$this->set('users', json_encode($autocomplete_users));
		
		if (isset($this->data)) {
			// zmenim uzivatele u obchodniho partnera
			if ($this->BusinessPartner->save($this->data)) {
				// a taky u obchodnich jednani daneho partnera
				$business_sessions = $this->BusinessPartner->BusinessSession->find('all', array(
					'conditions' => array('BusinessSession.business_partner_id' => $business_partner['BusinessPartner']['id']),
					'contain' => array(),
					'fields' => array('id')
				));

				foreach ($business_sessions as $business_session) {
					$business_session['BusinessSession']['user_id'] = $this->data['BusinessPartner']['user_id'];
					$this->BusinessPartner->BusinessSession->save($business_session);
				}
				
				// a u ukolu k danemu obchodnimu partnerovi
				$impositions = $this->BusinessPartner->Imposition->find('all', array(
					'conditions' => array('Imposition.business_partner_id' => $business_partner['BusinessPartner']['id']),
					'contain' => array(),
					'fields' => array('id')
				));
				
				foreach ($impositions as $imposition) {
					$imposition['Imposition']['user_id'] = $this->data['BusinessPartner']['user_id'];
					$this->BusinessPartner->Imposition->save($imposition);
				}
				
				$this->Session->setFlash('Uživatel zodpovědný za obchodního partnera byl upraven.');
				$this->redirect(array('controller' => 'business_partners', 'action' => 'view', $business_partner['BusinessPartner']['id']));
			} else {
				$this->Session->setFlash('Uživatele se nepodařilo upravit, opakujte prosím akci.');
			}
		} else {
			$this->data['BusinessPartner']['user_name'] = $business_partner[0]['full_name'];
		}
	}
	
	function user_delete($id) {
		if (!$id) {
			$this->Session->setFlash('Není určen obchodní partner, kterého chcete smazat');
			$this->redirect($this->index_link);
		}
		
		$business_partner = $this->BusinessPartner->find('first', array(
			'conditions' => array('BusinessPartner.id' => $id),
			'contain' => array(
				'Document'
			)
		));
		
		if (empty($business_partner)) {
			$this->Session->setFlash('Zvolený obchodní partner neexistuje');
			$this->redirect($this->index_link);
		}
		
		if (!$this->BusinessPartner->checkUser($this->user, $business_partner['BusinessPartner']['user_id'])) {
			$this->Session->setFlash('Nepovolený přístup. Nemáte právo smazat tohoto obchodního partnera');
			$this->redirect($this->index_link);
		}
		
		if ($this->BusinessPartner->delete($id)) {
			foreach ($business_partner['Document'] as $document) {
				if (file_exists('files/documents/' . $document['name'])) {
					unlink('files/documents/' . $document['name']);
				}
			}
			$this->Session->setFlash('Obchodní partner byl odstraněn');
		} else {
			$this->Session->setFlash('Obchodního partnera se nepodařilo odstranit');
		}
		$this->redirect($this->index_link);
	}
	
	function user_autocomplete_list() {
		$term = null;
		if ($_GET['term']) {
			$term = $_GET['term'];
		}

		echo $this->BusinessPartner->autocomplete_list($this->user, $term);
		die();
	}
}
?>