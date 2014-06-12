<?php
class SalesController extends AppController {
	var $name = 'Sales';
	
	var $paginate = array(
		'limit' => 25,
		'order' => array('Sale.date' => 'desc', 'Sale.modified' => 'desc')
	);
	
	function beforeRender() {
		parent::beforeRender();
		if (!isset($this->viewVars['active_tab'])) {
			$this->set('active_tab', 'sales');
		}
	}
	
	function user_index() {
		$conditions = array();
		
		if (isset($this->params['named']['reset']) && $this->params['named']['reset'] == 'sales') {
			$this->Session->delete('Search.SaleForm');
			$passedArgs = $this->passedArgs;
			unset($passedArgs['reset']);
			$this->redirect(array('controller' => 'sales', 'action' => 'index') + $passedArgs);
		}
		
		// pokud chci vysledky vyhledavani
		if (isset($this->data['SaleForm']['Sale']['search_form']) && $this->data['SaleForm']['Sale']['search_form'] == 1){
			$this->Session->write('Search.SaleForm', $this->data['SaleForm']);
			$conditions = array_merge($conditions, $this->Sale->Customer->do_form_search($this->data['SaleForm']));
		} elseif ($this->Session->check('Search.SaleForm')) {
			$this->data['SaleForm'] = $this->Session->read('Search.SaleForm');
			$conditions = array_merge($conditions, $this->Sale->Customer->do_form_search($this->data['SaleForm']));
		}

		$this->paginate['conditions'] = $conditions;
		$this->paginate['contain'] = array(
			'RecommendingCustomer',
			'User'
		);

		$this->paginate['fields'] = array(
			'Sale.id',
			'Sale.date',
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
			'Sale.price',
			'Sale.customer_bonus',
			'Sale.recommending_customer_bonus',
			'Customer.account',
			'User.last_name'
		);
		
   		$this->paginate['joins'] = array(
   			array(
   				'table' => 'customers',
   				'alias' => 'Customer',
   				'type' => 'LEFT',
   				'conditions' => array('Customer.id = Sale.customer_id')	
   			),
			array(
				'table' => 'tariffs',
				'alias' => 'Tariff',
				'type' => 'LEFT',
				'conditions' => array('Customer.tariff_id = Tariff.id')
			)
 		);
		App::import('Model', 'Tariff');
		$this->Sale->Tariff = &new Tariff;
		$sales = $this->paginate();

		$this->set('sales', $sales);
		
		// data pro form pro vlozeni noveho prodeje
		if ($this->Session->check('validationErrors.Sale')) {
			$this->Sale->validationErrors = $this->Session->read('validationErrors.Sale');
			$this->Session->delete('validationErrors.Sale');
				
			$this->data['Sale'] = $this->Session->read('data.Sale');
			$this->Session->delete('data.Sale');
		}
		if (!isset($this->data['Sale']['date'])) {
			$this->data['Sale']['date'] = date('d.m.Y');
		}
		// autocomplete na jmenu a cislu zakaznika
		$this->Sale->Customer->virtualFields = array('identification' => 'CONCAT(name, " ", number)');
		$customers = $this->Sale->Customer->find('all', array(
			'conditions' => array('active' => true),
			'contain' => array(),
			'fields' => array('Customer.id', 'Customer.identification')	
		));
		
		foreach ($customers as &$customer) {
			$customer = array('label' => $customer['Customer']['identification'], 'value' => $customer['Customer']['id']);
		}
		$this->set('customers', $customers);
		
		// pokud mam definovano v url, kteremu uzivateli chci vlozit prodej, musim naplnit data
		if (isset($this->params['named']['customer_id'])) {
			$customer = $this->Sale->Customer->find('first', array(
				'conditions' => array('Customer.id' => $this->params['named']['customer_id']),
				'contain' => array(),
				'fields' => array('Customer.id', 'Customer.identification')
			));
			
			if (empty($customer)) {
				$this->Session->setFlash('Uživatel, kterému chcete zadat prodej, neexistuje');
			} else {
				$this->data['Sale']['customer_id'] = $customer['Customer']['id'];
				$this->data['Sale']['customer_name'] = $customer['Customer']['identification'];
			} 
		}
	}
	
	// export vysledku na dotaz do csv
	function user_csv() {
		$conditions = array();
		// natahnu si ze sesny ulozene podminky vyhledavani
	
		if ($this->Session->check('Search.SaleForm')) {
			$conditions = array_merge($conditions, $this->Sale->Customer->do_form_search($this->Session->read('Search.SaleForm')));
		}
		// pokud chci data nejak serazena
		$order = array('Sale.date' => 'desc');
		if (isset($this->params['named']['sort'])) {
			$order = array($this->params['named']['sort'] => 'asc');
			if (isset($this->params['named']['direction']) && $this->params['named']['direction'] == 'desc') {
				$order = array($this->params['named']['sort'] => 'desc');
			}
		}
	
		$sales = $this->Sale->find('all', array(
			'conditions' => $conditions,
			'contain' => array(
				'RecommendingCustomer',
				'User'
			),
			'fields' => array(
				'Sale.id',
				'Sale.date',
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
				'Sale.price',
				'Sale.customer_bonus',
				'Sale.recommending_customer_bonus',
				'Sale.description',
				'Customer.account',
				'User.last_name'
			),
			'joins' => array(
				array(
					'table' => 'customers',
					'alias' => 'Customer',
					'type' => 'LEFT',
					'conditions' => array('Customer.id = Sale.customer_id')
				),
				array(
					'table' => 'tariffs',
					'alias' => 'Tariff',
					'type' => 'LEFT',
					'conditions' => array('Customer.tariff_id = Tariff.id')
				)
			),
			'order' => $order
		));

		$this->Sale->create_csv($sales);
		$this->redirect('/' . $this->Sale->export_file);
	}
	
	function user_add() {
		$redirect = array('controller' => 'sales', 'action' => 'index');
		if (isset($this->params['named']['back_controller']) && isset($this->params['named']['back_action'])) {
			$redirect = array('controller' => $this->params['named']['back_controller'], 'action' => $this->params['named']['back_action']);
		}
		if (isset($this->data)) {
			$user = $this->Auth->user();
			$this->data['Sale']['user_id'] = $user['User']['id'];

			if ($this->Sale->save($this->data)) {
				$this->Session->setFlash('Prodej byl uložen.');
				//$this->redirect($redirect + $this->passedArgs + array(0 => '#search'));
				$this->redirect(array('controller' => 'customers', 'action' => 'index'));
			} else {
				$this->Sale->set($this->data);
				if ($this->Sale->validates()) {
					// musim ocekovat, jestli se jedna o chybu ve validaci nebo neni overeny zakaznik
					$customer = $this->Sale->Customer->find('first', array(
						'conditions' => array('Customer.id' => $this->data['Sale']['customer_id']),
						'contain' => array(
							'RecommendingCustomer' => array(
								'fields' => array('RecommendingCustomer.id', 'RecommendingCustomer.name')
							)
						),
						'fields' => array('Customer.confirmed', 'Customer.recommending_customer_id')
					));
					// jestlize neni overeny zakaznik, musim zobrazit overeni zakaznika
					if ($customer['Customer']['recommending_customer_id'] && !$customer['Customer']['confirmed']) {
						// zviditelnim cast stranky s potvrzenim, kde bud nastavim confirmed na true nebo zrusim prideleni doporucujici osoby
						$this->set('customer', $customer);
					} else {
						$this->Session->setFlash('Prodej se nepodarilo ulozit, i kdyz data jsou validni.');
						$this->redirect($redirect + $this->passedArgs);
					}
				// jinak se jedna o chybu pri validaci vkladanych dat, ulozim errors do sesny atd.
				} else {
					$this->Session->setFlash('Prodej se nepodařilo uložit.');
					// predam si vysledky validace do sesny, abych k nim mel pristup i po redirectu
					if ($this->Session->check('validationErrors.Sale')) {
						$this->Session->delete('validationErrors.Sale');
					}
					$this->Session->write('validationErrors.Sale', $this->Sale->validationErrors);
					if ($this->Session->check('data.Sale')) {
						$this->Session->delete('data.Sale');
					}
					$this->Session->write('data.Sale', $this->data['Sale']);
					$this->redirect($redirect + $this->passedArgs);
				}
			}
		} else {
			$this->redirect($redirect + $this->passedArgs);
		}
	}
	
	function user_edit($id = null) {
		$passedArgs = $this->passedArgs;
		if (!$id) {
			$this->Session->setFlash('Není zadán prodej, který chcete upravit.');
			$this->redirect(array('controller' => 'sales', 'action' => 'index') + $passedArgs);
		}
		
		// z argumentu odstranim ten s informaci o id mazaneho zaznamu
		unset($passedArgs[0]);
		
		$this->Sale->Customer->virtualFields = array('identification' => 'CONCAT(name, " ", number)');
		$sale = $this->Sale->find('first', array(
			'conditions' => array('Sale.id' => $id),
			'contain' => array(
				'Customer' => array(
					'fields' => array('id', 'identification')
				)
			)
		));

		if (empty($sale)) {
			$this->Session->setFlash('Prodej, který chcete editovat, neexistuje.');
			$this->redirect(array('controller' => 'sales', 'action' => 'index') + $passedArgs);
		}
		
		// autocomplete na jmenu a cislu zakaznika
		$customers = $this->Sale->Customer->find('all', array(
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
			$this->data['Sale']['user_id'] = $user['User']['id'];
			$this->Sale->set($this->data);
			if ($this->Sale->validates()) {
				// smazu stary zaznam o prodeji, tim se mi odectou hodnoty z uctu puvodne uvedenych osob
				if (!$this->Sale->delete($id)) {
					$this->Session->setFlash('Původní záznam o prodeji se nepodařilo odstranit.');
					$this->redirect(array('controller' => 'sales', 'action' => 'index'));
				}
				// vytvorim novy zaznam o prodeji (tim se mi prictou hodnoty na ucty nove uvedenych osob)
				unset($this->data['Sale']['id']);
				$this->Sale->create();
				if (!$this->Sale->save($this->data)) {
					$this->Session->setFlash('Nepodařilo se vytvořit nový záznam o prodeji.');
					$this->redirect(array('controller' => 'sales', 'action' => 'index'));
				}
				$this->Session->setFlash('Záznam o prodeji byl upraven.');
				$this->redirect(array('controller' => 'sales', 'action' => 'index') + $this->passedArgs);
			} else {
				$this->Session->setFlash('Záznam o prodeji se nepodařilo uložit, opakujte prosím akci.');
			}
		} else {
			$this->data = $sale;
			$this->data['Sale']['date'] = cz_date($this->data['Sale']['date']);
			$this->data['Sale']['customer_name'] = $sale['Customer']['identification'];
		}
				
	}
	
	function user_delete($id = null) {
		$passedArgs = $this->passedArgs;
		if (!$id) {
			$this->Session->setFlash('Není zadán prodej, který chcete odstranit.');
			$this->redirect(array('controller' => 'sales', 'action' => 'index') + $passedArgs);
		}
		
		// z argumentu odstranim ten s informaci o id mazaneho zaznamu
		unset($passedArgs[0]);
		
		if ($this->Sale->delete($id)) {
			$this->Session->setFlash('Záznam o prodeji byl odstraněn.');
		} else {
			$this->Session->setFlash('Záznam o prodeji se neporařilo odstranit, opakujte prosím akci.');
		}
		$this->redirect(array('controller' => 'sales', 'action' => 'index') + $passedArgs);
	}
	
	function test() {
		$customer_id = 68;
		
		$this->Sale->virtualFields = array('type' => 'sale');
		$this->Sale->Customer->PayOut->virtualFields = array('type' => 'pay_out');
		
		$sales_select = '
		SELECT *
		FROM (
			SELECT 
				Sale.customer_bonus AS price,
				Sale.date AS date,
				"sale" AS type
			FROM sales AS Sale
			WHERE
				Sale.customer_id = ' . $customer_id . '
			UNION
			SELECT 
				Sale.recommending_customer_bonus AS price,
				Sale.date AS date,
				"rec_sale" AS type
			FROM sales AS Sale
			WHERE	
				Sale.recommending_customer_id = ' . $customer_id . '
			UNION
			SELECT
				-(PayOut.amount) AS price,
				PayOut.date AS date,
				"pay_out" AS type
			FROM pay_outs AS PayOut
			WHERE
				PayOut.customer_id = ' . $customer_id . '
		) AS Transaction
		ORDER BY Transaction.date DESC
		';
		
/* 		$sales_select = '
			SELECT
				Sale.price AS price,
				Sale.date AS date,
				"sale" AS Sale__type
			FROM sales AS Sale
			WHERE
				Sale.customer_id = ' . $customer_id . ' OR
				Sale.recommending_customer_id = ' . $customer_id . '
		'; */
		debug($sales_select);
		
		debug($this->Sale->query($sales_select)); die();
	}
}