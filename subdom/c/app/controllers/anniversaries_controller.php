<?php
class AnniversariesController extends AppController {
	var $name = 'Anniversaries';
	
	var $left_menu_list = array('anniversaries');
	
	function beforeFilter(){
		parent::beforeFilter();
		$this->set('active_tab', 'anniversaries');
	}
	
	function beforeRender(){
		parent::beforeRender();
		$this->set('left_menu_list', $this->left_menu_list);
		
		$anniversary_types = $this->Anniversary->AnniversaryType->find('list');
		$this->set('anniversary_types', $anniversary_types);
		
		$anniversary_actions = $this->Anniversary->AnniversaryAction->find('list');
		$this->set('anniversary_actions', $anniversary_actions);
	}
	
	function user_index() {
		$user = $this->Session->read('Auth');
		$user_id = $user['User']['id'];
		
		$business_partner_conditions = array(
			'BusinessPartner.id = ContactPerson.business_partner_id'
		);
		
		if ($user['User']['user_type_id'] == 3) {
			$business_partner_conditions[] = 'BusinessPartner.user_id = ' . $user_id;
		}
		
		$conditions = array(); // tady bude asi nejaka defaultni podminka,
		// ta se v pripade vyhledavani zameni vyhledavacima podminkama... NIZE
		
		if (isset($this->params['named']['reset']) && $this->params['named']['reset'] == 'anniversary') {
			$this->Session->delete('Search.AnniversaryForm');
			$this->redirect(array('controller' => 'anniversaries', 'action' => 'index'));
		}
		
		// pokud chci vysledky vyhledavani
		if ( isset($this->data['Anniversary']['search_form']) && $this->data['Anniversary']['search_form'] == 1 ){
			if (empty($this->data['Anniversary']['date_from'])) {
				$this->data['Anniversary']['date_from'] = date('d.m.Y');
			}
			if (empty($this->data['Anniversary']['date_to'])) {
				$from = explode('.', $this->data['Anniversary']['date_from']);
				$next_year = $from[2] + 1;
				$this->data['Anniversary']['date_to'] = $from[0] . '.' . $from[1] . '.' . $next_year;
			}
			$this->Session->write('Search.AnniversaryForm', $this->data);
		} else {
			if ($this->Session->check('Search.AnniversaryForm')) {
				$this->data = $this->Session->read('Search.AnniversaryForm');
			} else {
				$this->data['Anniversary']['date_from'] = date('d.m.Y');
				$next_year = date('Y') + 1;
				$this->data['Anniversary']['date_to'] = date('d') . '.' . date('m') . '.' . $next_year;
			}
		}
		
		$today_anniversaries = $this->Anniversary->find('all', array(
			'fields' => array('Anniversary.*', 'ContactPerson.*', 'BusinessPartner.*', 'AnniversaryType.*', 'AnniversaryAction.*', 'STR_TO_DATE( CONCAT_WS( \',\', Month( Anniversary.date ) , Day( Anniversary.date ) , Year( CURDATE( ) ) ) , \'%m,%d,%Y\' ) AS univ_date'),
			'conditions' => array(
				'OR' => array(
					'Anniversary.date = "' . date('Y-m-d') . '"',
					'AND' => array(
						'Anniversary.date LIKE "%' . date('m-d') . '"',
						'AnniversaryType.every_year' => true
					)
				)
			),
			'contain' => array('AnniversaryType', 'AnniversaryAction'),
			'limit' => 30,
			'joins' => array(
				array(
					'table' => 'contact_people',
					'alias' => 'ContactPerson',
					'type' => 'INNER',
					'conditions' => array(
						'ContactPerson.active' => true,
						'ContactPerson.id = Anniversary.contact_person_id'
					)
				),
				array(
					'table' => 'business_partners',
					'alias' => 'BusinessPartner',
					'type' => 'INNER',
					'conditions' => $business_partner_conditions
				)
			)
		));
		$this->set('today_anniversaries', $today_anniversaries);

		$from = date('Y-m-d', strtotime('- 2 days'));
		$to = date('Y-m-d', strtotime('+ 10 days'));
		
		$between_every_year = array();
		for ($i=0; $i < 13; $i++) {
			$difference = -2 + $i;
			$between_every_year[] = date('m-d', strtotime($difference . ' days'));
		}
		
		$interval_anniversaries = $this->Anniversary->find('all', array(
			'fields' => array('Anniversary.*', 'ContactPerson.*', 'BusinessPartner.*', 'AnniversaryType.*', 'AnniversaryAction.*'),
			'conditions' => array(
				'OR' => array(
					'Anniversary.date BETWEEN "' . $from . '" AND "' . $to . '"',
					'AND' => array(
						'Anniversary.date LIKE "%' . implode('" OR Anniversary.date LIKE "%', $between_every_year) . '"',
						'AnniversaryType.every_year' => true
					)
				)
			),
			'contain' => array('AnniversaryType', 'AnniversaryAction'),
			'limit' => 30,
			'joins' => array(
				array(
					'table' => 'contact_people',
					'alias' => 'ContactPerson',
					'type' => 'INNER',
					'conditions' => array(
						'ContactPerson.active' => true,
						'ContactPerson.id = Anniversary.contact_person_id'
					)
				),
				array(
					'table' => 'business_partners',
					'alias' => 'BusinessPartner',
					'type' => 'INNER',
					'conditions' => $business_partner_conditions
				)
			)
		));
		$this->set('interval_anniversaries', $interval_anniversaries);

		$this->paginate['Anniversary'] = array(
			'data' => $this->data,
			'limit' => 30
		);

		if (!empty($business_partner_conditions)) {
			$this->paginate['Anniversary']['conditions'] = $business_partner_conditions;
		}
		$union = $this->paginate('Anniversary');
		$this->set('anniversaries', $union);
	}
	
	function user_add() {
		if (!isset($this->params['named']['contact_person_id'])) {
			$this->Session->setFlash('Neznámá kontaktní osoba, u které chce přidat výročí.');
			$this->redirect(array('controller' => 'contact_people', 'action' => 'index'));
		}
		
		$contact_person_id = $this->params['named']['contact_person_id'];
		$contact_person = $this->Anniversary->ContactPerson->find('first', array(
			'conditions' => array('ContactPerson.id' => $contact_person_id, 'ContactPerson.active' => true),
			'contain' => array('BusinessPartner')
		));
		
		if (empty($contact_person)) {
			$this->Session->setFlash('Kontaktní osoba neexistuje');
			$this->redirect(array('controller' => 'contact_people', 'action' => 'index'));
		}
		
		if (!$this->Anniversary->checkUser($this->user, $contact_person['BusinessPartner']['user_id'])) {
			$this->Session->setFlash('Neoprávněný přístup. Nemáte právo přidat výročí k této kontaktní osobě');
			$this->redirect(array('controller' => 'contact_people', 'action' => 'index'));
		}
		$this->set('contact_person_id', $contact_person_id);
		
		$this->set('contact_person', $contact_person);
		$this->left_menu_list = array('contact_people', 'contact_person_detailed');
		$this->set('active_tab', 'contact_people');
		
		if (isset($this->data)) {
			$data_backup = $this->data;
			$this->data['Anniversary']['date'] = cal2db_date($this->data['Anniversary']['date']);
			if ($this->Anniversary->save($this->data)) {
				$this->Session->setFlash('Výročí bylo uloženo');
				$this->redirect(array('controller' => 'contact_people', 'action' => 'view', $contact_person_id));
			} else {
				$this->data = $data_backup;
				$this->Session->setFlash('Výročí se nepodařilo uložit, opravte chyby ve forumláři a opakujte prosím akci');
			}
		}
	}
	
	function user_edit($id = null) {
		if (!$id) {
			$this->Session->setFlash('Není určeno výročí, které chcete upravovat');
			$this->redirect(array('controller' => 'users', 'action' => 'index'));
		}
		
		$anniversary = $this->Anniversary->find('first', array(
			'conditions' => array('Anniversary.id' => $id, 'ContactPerson.active' => true),
			'contain' => array(
				'ContactPerson' => array(
					'BusinessPartner'
				)
			)
		));

		if (empty($anniversary)) {
			$this->Session->setFlash('Zvolené výročí neexistuje');
			$this->redirect(array('controller' => 'contact_people', 'action' => 'index'));
		}
		
		$contact_person_id = $anniversary['Anniversary']['contact_person_id'];
		if (!$this->Anniversary->checkUser($this->user, $anniversary['ContactPerson']['BusinessPartner']['user_id'])) {
			$this->Session->setFlash('Neoprávněný přístup. Nemáte právo upravovat výročí této kontaktní osoby.');
			$this->redirect(array('controller' => 'contact_people', 'action' => 'index'));
		}
		$this->set('contact_person_id', $contact_person_id);
						
		$this->set('monthNames', $this->monthNames);
		
		$this->set('contact_person', $anniversary);
		$this->left_menu_list = array('contact_people', 'contact_person_detailed');
		$this->set('active_tab', 'contact_people');
		
		if (isset($this->data)) {
			$data_backup = $this->data;
			$this->data['Anniversary']['date'] = cal2db_date($this->data['Anniversary']['date']);
			if ($this->Anniversary->save($this->data)) {
				$this->Session->setFlash('Výročí bylo uloženo');
				$this->redirect(array('controller' => 'contact_people', 'action' => 'view', $contact_person_id));
			} else {
				$this->Session->setFlash('Výročí se nepodařilo uložit, opravte chyby ve formuláři a opakujte prosím akci');
				$this->data = $data_backup;
			}
		} else {
			$this->data = $anniversary;
			$this->data['Anniversary']['date'] = db2cal_date($anniversary['Anniversary']['date']);
		}
	}
	
	function user_delete($id = null) {
		if (!$id) {
			$this->Session->setFlash('Není určeno výročí, které chcete smazat');
			$this->redirect(array('controller' => 'users', 'action' => 'index'));
		}
		
		$anniversary = $this->Anniversary->find('first', array(
			'conditions' => array('Anniversary.id' => $id, 'ContactPerson.active' => true),
			'contain' => array(
				'ContactPerson' => array(
					'BusinessPartner'
				)
			)
		));
		
		if (empty($anniversary)) {
			$this->Session->setFlash('Zvolené výročí neexistuje');
			$this->redirect(array('controller' => 'contact_people', 'action' => 'index'));
		}
		
		if (!$this->Anniversary->checkUser($this->user, $anniversary['ContactPerson']['BusinessPartner']['user_id'])) {
			$this->Session->setFlash('Neoprávněný přístup. Nemáte právo mazat výročí u této kontaktní osoby.');
			$this->redirect(array('controller' => 'contact_people', 'action' => 'index'));
		}
		
		if ($this->Anniversary->delete($id)) {
			$this->Session->setFlash('Výročí bylo odstraněno');
		} else {
			$this->Session->setFlash('Výročí se nepodařilo odstranit, opakujte prosím akci');
		}
		
		$this->redirect(array('controller' => 'contact_people', 'action' => 'view', $anniversary['Anniversary']['contact_person_id']));
	}
}