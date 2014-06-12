<?php
class CustomersController extends AppController {
	var $name = 'Customers';
	
	var $paginate = array(
		'limit' => 25,
		'order' => array('number' => 'asc'),
		'Transaction' => array(
			'limit' => 25,
			'order' => array('date' => 'desc')
		)
	);
	
	function beforeRender() {
		parent::beforeRender();
		if (!isset($this->viewVars['active_tab'])) {
			$this->set('active_tab', 'customers');
		}
	}
	
	function user_index() {
		$conditions = array('Customer.active' => true);
		//$conditions = array();
		
		// customer_number mam nastaveno, pokud ze seznamu vyplat/prodeju jdu pres prijmeni zakaznika -> chci mit jako vysledek vyhledavani
		// toho daneho zakaznika (nastavim do vyhledavani jeho customer_number
		if (isset($this->params['named']['customer_number'])) {
			$this->Session->delete('Search.CustomerForm');
			$this->Session->write('Search.CustomerForm.Customer.number', $this->params['named']['customer_number']);
		}
		
		if (isset($this->params['named']['reset']) && $this->params['named']['reset'] == 'customers') {
			$this->Session->delete('Search.CustomerForm');
			$passedArgs = $this->passedArgs;
			unset($passedArgs['reset']);
			unset($passedArgs['customer_number']);
			$this->redirect(array('controller' => 'customers', 'action' => 'index') + $passedArgs);
		}
		
		// pokud chci vysledky vyhledavani
		if ( isset($this->data['CustomerForm']['Customer']['search_form']) && $this->data['CustomerForm']['Customer']['search_form'] == 1 ){
			$this->Session->write('Search.CustomerForm', $this->data['CustomerForm']);
			$conditions = array_merge($conditions, $this->Customer->do_form_search($this->data['CustomerForm']));
		} elseif ($this->Session->check('Search.CustomerForm')) {
			$this->data['CustomerForm'] = $this->Session->read('Search.CustomerForm');
			$conditions = array_merge($conditions, $this->Customer->do_form_search($this->data['CustomerForm']));
		}

		$this->paginate['contain'] = array(
			'RecommendingCustomer' => array(
				'fields' => array('id', 'name')
			),
			'Tariff' => array(
				'fields' => array('id', 'name')
			)
		);
		$this->paginate['conditions'] = $conditions;
		
		$customers = $this->paginate();
		
		$this->set('customers', $customers);
	}
	
	// export vysledku na dotaz do csv
	function user_csv() {
		$conditions = array('Customer.active' => true);
		// natahnu si ze sesny ulozene podminky vyhledavani
		if ($this->Session->check('Search.CustomerForm')) {
			$conditions = array_merge($conditions, $this->Customer->do_form_search($this->Session->read('Search.CustomerForm')));
		}
		// pokud chci data nejak serazena
		$order = array('Customer.number' => 'asc');
		if (isset($this->params['named']['sort'])) {
			$order = array($this->params['named']['sort'] => 'asc');
			if (isset($this->params['named']['direction']) && $this->params['named']['direction'] == 'desc') {
				$order = array($this->params['named']['sort'] => 'desc');
			}
		}
	
		$customers = $this->Customer->find('all', array(
				'conditions' => $conditions,
				'contain' => array(
						'RecommendingCustomer' => array(
								'fields' => array('id', 'name')
						),
						'Tariff' => array(
								'fields' => array('id', 'name')
						)
				),
				'order' => $order
		));
	
		$this->Customer->create_csv($customers);
		$this->redirect('/' . $this->Customer->export_file);
	}
	
	function user_view($id = null) {
		if (!$id) {
			$this->Session->setFlash('Není zadán zákazník, jehož transakce chcete zobrazit.');
			$this->redirect(array('controller' => 'customers', 'action' => 'index'));
		}
		
		$customer = $this->Customer->find('first', array(
			'conditions' => array('Customer.id' => $id, 'Customer.active' => true),
			'contain' => array(),
		));
		
		if (empty($customer)) {
			$this->Session->setFlash('Zákazník, jehož transakce chcete vypsat, neexistuje.');
			$this->redirect(array('controller' => 'customers', 'action' => 'index'));
		}
		
		// data pro form pro vlozeni noveho prodeje
		if ($this->Session->check('validationErrors.Sale')) {
			$this->Customer->Sale->validationErrors = $this->Session->read('validationErrors.Sale');
			$this->Session->delete('validationErrors.Sale');
		
			$this->data['Sale'] = $this->Session->read('data.Sale');
			$this->Session->delete('data.Sale');
		}
		if (!isset($this->data['Sale']['date'])) {
			$this->data['Sale']['date'] = date('d.m.Y');
		}
		
		// data pro form pro vlozeni noveho prodeje
		if ($this->Session->check('validationErrors.PayOut')) {
			$this->Customer->PayOut->validationErrors = $this->Session->read('validationErrors.PayOut');
			$this->Session->delete('validationErrors.PayOut');
		
			$this->data['PayOut'] = $this->Session->read('data.PayOut');
			$this->Session->delete('data.PayOut');
		}
		
		if (!isset($this->data['PayOut']['date'])) {
			$this->data['PayOut']['date'] = date('d.m.Y');
		}
		
		$this->set('customer', $customer);
		
		App::import('Model', 'Transaction');
		$this->Customer->Transaction = &new Transaction;
		
		$this->paginate['Transaction']['customer_id'] = $id;

		$transactions = $this->paginate('Transaction');
		$this->set('transactions', $transactions);
	}
	
	function user_add() {
		$this->set('active_tab', 'customers_add');
		
		$tariffs = $this->Customer->Tariff->find('list');
		
		if (empty($tariffs)) {
			$this->Session->setFlash('Nového zákazníka nelze vložit. Nejprve zadejte bonusové tarify!');
			$user = $this->Auth->user();
			if ($user['User']['is_admin']) {
				$this->redirect(array('controller' => 'users', 'action' => 'index'));
			}
			$this->redirect(array('controller' => 'customers', 'action' => 'index'));
		}
		
		$this->set('tariffs', $tariffs);
		
		$this->Customer->RecommendingCustomer->virtualFields = array('address' => 'CONCAT(street, ", ", zip, " ", city)');

		$recommending_customers = $this->Customer->RecommendingCustomer->find('all', array(
			'conditions' => array('active' => true),
			'contain' => array(),
			'fields' => array('id', 'name', 'number', 'address')
		));

		foreach ($recommending_customers as &$recommending_customer) {
			$label = $recommending_customer['RecommendingCustomer']['name'] . ' ' . $recommending_customer['RecommendingCustomer']['number'] . ' ' . $recommending_customer['RecommendingCustomer']['address'];
			$recommending_customer = array('label' => $label, 'value' => $recommending_customer['RecommendingCustomer']['id']);
		}
		
		$this->set('recommending_customers', $recommending_customers);
		
		if (isset($this->data)) {
			if ($this->Customer->save($this->data)) {
				$this->Session->setFlash('Zákazník byl vytvořen.');
				$this->redirect(array('controller' => 'customers', 'action' => 'index'));
			} else {
				$this->Session->setFlash('Zákazníka se nepodařilo uložit. Opravte chyby ve formuláři a opakujte prosím akci.');
			}
		}
	}
	
	function user_edit($id = null) {
		if (!$id) {
			$this->Session->setFlash('Není zadán zákazník, kterého chcete editovat.');
			$this->redirect(array('controller' => 'customers', 'action' => 'index'));
		}
		
		$customer = $this->Customer->find('first', array(
			'conditions' => array('Customer.id' => $id),
			'contain' => array(
				'RecommendingCustomer' => array(
					'fields' => array('id', 'name')
				),
				'Sale' => array(
					'limit' => 1
				)
			),
		));

		if (empty($customer)) {
			$this->Session->setFlash('Zákazník, kterého chcete editovat, neexistuje.');
			$this->redirect(array('controller' => 'customers', 'action' => 'index'));
		}
		
		$this->set('customer', $customer);
		
		$recommending_customers = array();
		if (empty($customer['Sale'])) {
			$this->Customer->RecommendingCustomer->virtualFields = array('address' => 'CONCAT(street, ", ", zip, " ", city)');
			
			$recommending_customers = $this->Customer->RecommendingCustomer->find('all', array(
				'contain' => array(),
				'fields' => array('id', 'name', 'number', 'address')
			));
			
			foreach ($recommending_customers as &$recommending_customer) {
				$label = $recommending_customer['RecommendingCustomer']['name'] . ' ' . $recommending_customer['RecommendingCustomer']['number'] . ' ' . $recommending_customer['RecommendingCustomer']['address'];
				$recommending_customer = array('label' => $label, 'value' => $recommending_customer['RecommendingCustomer']['id']);
			}
		}
		$this->set('recommending_customers', $recommending_customers);
		
		$tariffs = $this->Customer->Tariff->find('list');
		$this->set('tariffs', $tariffs);
		
		// domluvili jsme se, ze nepujde v editaci zmenit doporucujici osobu, takze to tady neresim
		if (isset($this->data)) {
			if ($this->Customer->save($this->data)) {
				$this->Session->setFlash('Zákazník byl upraven.');
				$this->redirect(array('controller' => 'customers', 'action' => 'index'));
			} else {
				$this->Session->setFlash('Zákazníka se nepodařilo upravit. Opravte chyby ve formuláři a opakujte prosím akci.');
			}
		} else {
			$this->data = $customer;
			if (!empty($customer['RecommendingCustomer']['id'])) {
				$this->data['Customer']['recommending_customer_name'] = $customer['RecommendingCustomer']['name'];
				$this->data['Customer']['recommending_customer_id'] = $customer['RecommendingCustomer']['id'];
			}
		}
		
	}
	
	function user_delete($id = null) {
		if (!$id) {
			$this->Session->setFlash('Není zadán zákazník, kterého chcete smazat.');
			$this->redirect(array('controller' => 'customers', 'action' => 'index'));
		}
		
		if ($this->Customer->delete($id)) {
			$this->Session->setFlash('Zákazník byl odstraněn.');
		} else {
			$this->Session->setFlash('Odstranění zákazníka se nezdařilo, opakujte prosím akci.');
		}
		$this->redirect(array('controller' => 'customers', 'action' => 'index'));
	}
}