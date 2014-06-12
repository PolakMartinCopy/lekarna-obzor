<?php
class PayOutsController extends AppController {
	var $name = 'PayOuts';
	
	var $paginate = array(
			'limit' => 25,
			'order' => array('date' => 'desc')
	);
	
	function beforeRender() {
		parent::beforeRender();
		if (!isset($this->viewVars['active_tab'])) {
			$this->set('active_tab', 'pay_outs');
		}
	}
	
	function user_index() {
		$conditions = array();
		
		if (isset($this->params['named']['reset']) && $this->params['named']['reset'] == 'pay_outs') {
			$this->Session->delete('Search.PayOutForm');
			$passedArgs = $this->passedArgs;
			unset($passedArgs['reset']);
			$this->redirect(array('controller' => 'pay_outs', 'action' => 'index') + $passedArgs);
		}
		
		// pokud chci vysledky vyhledavani
		if ( isset($this->data['PayOutForm']['PayOut']['search_form']) && $this->data['PayOutForm']['PayOut']['search_form'] == 1 ){
			$this->Session->write('Search.PayOutForm', $this->data['PayOutForm']);
			$conditions = array_merge($conditions, $this->PayOut->Customer->do_form_search($this->data['PayOutForm']));
		} elseif ($this->Session->check('Search.PayOutForm')) {
			$this->data['PayOutForm'] = $this->Session->read('Search.PayOutForm');
			$conditions = array_merge($conditions, $this->PayOut->Customer->do_form_search($this->data['PayOutForm']));
		}

		$this->paginate['conditions'] = $conditions;
		$this->paginate['contain'] = array('User');

		$this->paginate['fields'] = array(
			'PayOut.id',
			'PayOut.date',
			'Customer.id',
			'Customer.number',
			'Customer.last_name',
			'Customer.first_name',
			'Customer.street',
			'Customer.zip',
			'Customer.city',
			'RecommendingCustomer.number',
			'RecommendingCustomer.name',
			'Tariff.name',
			'PayOut.amount',
			'Customer.account',
			'User.last_name'
		);
		
   		$this->paginate['joins'] = array(
   			array(
   				'table' => 'customers',
   				'alias' => 'Customer',
   				'type' => 'LEFT',
   				'conditions' => array('Customer.id = PayOut.customer_id')	
   			),
			array(
				'table' => 'tariffs',
				'alias' => 'Tariff',
				'type' => 'LEFT',
				'conditions' => array('Customer.tariff_id = Tariff.id')
			),
   			array(
   				'table' => 'customers',
   				'alias' => 'RecommendingCustomer',
   				'type' => 'LEFT',
   				'conditions' => array('RecommendingCustomer.id = Customer.recommending_customer_id')
   			)
 		);
		App::import('Model', 'Tariff');
		$this->PayOut->Tariff = &new Tariff;
		App::import('Model', 'Customer');
		$this->PayOut->RecommendingCustomer = &new Customer;
		$pay_outs = $this->paginate();

		$this->set('pay_outs', $pay_outs);
		
		// data pro form pro vlozeni noveho prodeje
		if ($this->Session->check('validationErrors.PayOut')) {
			$this->PayOut->validationErrors = $this->Session->read('validationErrors.PayOut');
			$this->Session->delete('validationErrors.PayOut');
				
			$this->data['PayOut'] = $this->Session->read('data.PayOut');
			$this->Session->delete('data.PayOut');
		}

		if (!isset($this->data['PayOut']['date'])) {
			$this->data['PayOut']['date'] = date('d.m.Y');
		}
		// autocomplete na jmenu a cislu zakaznika
		$this->PayOut->Customer->virtualFields = array('identification' => 'CONCAT(name, " ", number)');
		$customers = $this->PayOut->Customer->find('all', array(
			'conditions' => array('active' => true),
			'contain' => array(),
			'fields' => array('id', 'identification')	
		));
		
		foreach ($customers as &$customer) {
			$customer = array('label' => $customer['Customer']['identification'], 'value' => $customer['Customer']['id']);
		}
		$this->set('customers', $customers);
		
		// pokud mam definovano v url, kteremu uzivateli chci vlozit vyplatu, musim naplnit data
		if (isset($this->params['named']['customer_id'])) {
			$customer = $this->PayOut->Customer->find('first', array(
				'conditions' => array('Customer.id' => $this->params['named']['customer_id']),
				'contain' => array(),
				'fields' => array('Customer.id', 'Customer.identification')
			));
				
			if (empty($customer)) {
				$this->Session->setFlash('Uživatel, kterému chcete zadat výplatu, neexistuje');
			} else {
				$this->data['PayOut']['customer_id'] = $customer['Customer']['id'];
				$this->data['PayOut']['customer_name'] = $customer['Customer']['identification'];
			}
		}
	}
	
	// export vysledku na dotaz do csv
	function user_csv() {
		$conditions = array();
		// natahnu si ze sesny ulozene podminky vyhledavani

		if ($this->Session->check('Search.PayOutForm')) {
			$conditions = array_merge($conditions, $this->PayOut->Customer->do_form_search($this->Session->read('Search.PayOutForm')));
		}
		// pokud chci data nejak serazena
		$order = array('PayOut.date' => 'desc');
		if (isset($this->params['named']['sort'])) {
			$order = array($this->params['named']['sort'] => 'asc');
			if (isset($this->params['named']['direction']) && $this->params['named']['direction'] == 'desc') {
				$order = array($this->params['named']['sort'] => 'desc');
			}
		}

		$pay_outs = $this->PayOut->find('all', array(
			'conditions' => $conditions,
			'contain' => array('User'),
			'fields' => array(
				'PayOut.id',
				'PayOut.date',
				'Customer.id',
				'Customer.number',
				'Customer.last_name',
				'Customer.first_name',
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
				'PayOut.amount',
				'Customer.account',
				'User.last_name'
			),
			'joins' => array(
	   			array(
	   				'table' => 'customers',
	   				'alias' => 'Customer',
	   				'type' => 'LEFT',
	   				'conditions' => array('Customer.id = PayOut.customer_id')	
	   			),
				array(
					'table' => 'tariffs',
					'alias' => 'Tariff',
					'type' => 'LEFT',
					'conditions' => array('Customer.tariff_id = Tariff.id')
				),
	   			array(
	   				'table' => 'customers',
	   				'alias' => 'RecommendingCustomer',
	   				'type' => 'LEFT',
	   				'conditions' => array('RecommendingCustomer.id = Customer.recommending_customer_id')
	   			)
			),
			'order' => $order
		));
	
		$this->PayOut->create_csv($pay_outs);
		$this->redirect('/' . $this->PayOut->export_file);
	}
	
	function user_view_pdf($id = null) {
		if (!$id) {
			$this->Session->setFlash('Není zadána výplata, k níž chcete zobrazit doklad.');
			$this->redirect(array('controller' => 'pay_outs', 'action' => 'index'));
		}
		
		$pay_out = $this->PayOut->find('first', array(
			'conditions' => array('PayOut.id' => $id),
			'contain' => array('Customer', 'User'),
			'fields' => array(
				'PayOut.amount',
				'Customer.first_name',
				'Customer.last_name',
				'Customer.street',
				'Customer.zip',
				'Customer.city',
				'Customer.birth_certificate_number',
				'PayOut.date',
				'User.first_name',
				'User.last_name',
			)
		));

		if (empty($pay_out)) {
			$this->Session->setFlash('Výplata, ke které chcete zobrazit doklad, neexistuje.');
			$this->redirect(array('controller' => 'pay_outs', 'action' => 'index'));
		}

		if ($this->PayOut->create_pdf($pay_out)) {
			$this->redirect('/' . $this->PayOut->document_file);
		} else {
			$this->Session->setFlash('Dokument se nepodařilo vytvořit.');
			$this->redirect(array('controller' => 'pay_outs', 'action' => 'index') + $this->passedArgs);
		}
	}
	
	function user_add() {
		$redirect = array('controller' => 'pay_outs', 'action' => 'index');
		if (isset($this->params['named']['back_controller']) && isset($this->params['named']['back_action'])) {
			$redirect = array('controller' => $this->params['named']['back_controller'], 'action' => $this->params['named']['back_action']);
		}
		
		if (isset($this->data)) {
			$user = $this->Auth->user();
			$this->data['PayOut']['user_id'] = $user['User']['id'];
			if ($this->PayOut->save($this->data)) {
				$this->Session->setFlash('Výplata byla uložena.');
			} else {
				$this->Session->setFlash('Výplatu se nepodařilo uložit.');
				// predam si vysledky validace do sesny, abych k nim mel pristup i po redirectu
				if ($this->Session->check('validationErrors.PayOut')) {
					$this->Session->delete('validationErrors.PayOut');
				}
				$this->Session->write('validationErrors.PayOut', $this->PayOut->validationErrors);
				if ($this->Session->check('data.PayOut')) {
					$this->Session->delete('data.PayOut');
				}
				$this->Session->write('data.PayOut', $this->data['PayOut']);
			}
		}
		$this->redirect($redirect + $this->passedArgs);
	}
	
	function user_edit($id = null) {
		$passedArgs = $this->passedArgs;
		if (!$id) {
			$this->Session->setFlash('Není zadána výplata, kterou chcete upravit.');
			$this->redirect(array('controller' => 'pay_outs', 'action' => 'index') + $passedArgs);
		}
	
		// z argumentu odstranim ten s informaci o id mazaneho zaznamu
		unset($passedArgs[0]);
	
		$this->PayOut->Customer->virtualFields = array('identification' => 'CONCAT(name, " ", number)');
		$pay_out = $this->PayOut->find('first', array(
			'conditions' => array('PayOut.id' => $id),
			'contain' => array(
				'Customer' => array(
					'fields' => array('id', 'identification')
				)
			)
		));
	
		if (empty($pay_out)) {
			$this->Session->setFlash('Výplata, kterou chcete editovat, neexistuje.');
			$this->redirect(array('controller' => 'pay_outs', 'action' => 'index') + $passedArgs);
		}
	
		// autocomplete na jmenu a cislu zakaznika
		$customers = $this->PayOut->Customer->find('all', array(
				'conditions' => array('active' => true),
				'contain' => array(),
				'fields' => array('id', 'identification')
		));
	
		foreach ($customers as &$customer) {
			$customer = array('label' => $customer['Customer']['identification'], 'value' => $customer['Customer']['id']);
		}
		$this->set('customers', $customers);
	
		if (isset($this->data)) {
			$user = $this->Auth->user();
			$this->data['PayOut']['user_id'] = $user['User']['id'];
			$this->PayOut->set($this->data);
			if ($this->PayOut->validates()) {
				// smazu stary zaznam o vyplace, tim se mi pricte hodnota na ucet osoby
				if (!$this->PayOut->delete($id)) {
					$this->Session->setFlash('Původní záznam o výplatě se nepodařilo odstranit.');
					$this->redirect(array('controller' => 'pay_outs', 'action' => 'index'));
				}
				// vytvorim novy zaznam o vyplate, tim se mi odecte hodnota z uctu osoby
				unset($this->data['PayOut']['id']);
				$this->PayOut->create();
				if (!$this->PayOut->save($this->data)) {
					$this->Session->setFlash('Nepodařilo se vytvořit nový záznam o výplatě.');
					$this->redirect(array('controller' => 'pay_outs', 'action' => 'index'));
				}
				$this->Session->setFlash('Záznam o výplatě byl upraven.');
				$this->redirect(array('controller' => 'pay_outs', 'action' => 'index') + $this->passedArgs);
			} else {
				$this->Session->setFlash('Záznam o výplatě se nepodařilo uložit, opakujte prosím akci.');
			}
		} else {
			$this->data = $pay_out;
			$this->data['PayOut']['date'] = cz_date($this->data['PayOut']['date']);
			$this->data['PayOut']['customer_name'] = $pay_out['Customer']['identification'];
		}
	
	}
	
	function user_delete($id = null) {
		$passedArgs = $this->passedArgs;
		if (!$id) {
			$this->Session->setFlash('Není zadána výplata, kterou chcete odstranit.');
			$this->redirect(array('controller' => 'pay_outs', 'action' => 'index') + $passedArgs);
		}
	
		// z argumentu odstranim ten s informaci o id mazaneho zaznamu
		unset($passedArgs[0]);
	
		if ($this->PayOut->delete($id)) {
			$this->Session->setFlash('Záznam o výplatě byl odstraněn.');
		} else {
			$this->Session->setFlash('Záznam o výplatě se neporařilo odstranit, opakujte prosím akci.');
		}
		$this->redirect(array('controller' => 'pay_outs', 'action' => 'index') + $passedArgs);
	}
}