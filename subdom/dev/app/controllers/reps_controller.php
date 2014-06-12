<?php 
class RepsController extends AppController {
	var $name = 'Reps';
	
	var $left_menu_list = array('reps');
	
	function beforeFilter() {
		parent::beforeFilter();
		$this->set('active_tab', 'reps');
	}
	
	function beforeRender(){
		parent::beforeRender();
		$this->set('left_menu_list', $this->left_menu_list);
	}
	
	function user_index() {
		if (isset($this->params['named']['reset']) && $this->params['named']['reset'] == 'reps') {
			$this->Session->delete('Search.RepForm');
			$this->redirect(array('controller' => 'reps', 'action' => 'index'));
		}
		
		$conditions = array('Rep.active' => true);
		// pokud chci vysledky vyhledavani
		if ( isset($this->data['RepForm']['Rep']['search_form']) && $this->data['RepForm']['Rep']['search_form'] == 1 ){
			$this->Session->write('Search.RepForm', $this->data['RepForm']);
			$conditions = $this->Rep->do_form_search($conditions, $this->data['RepForm']);
		} elseif ($this->Session->check('Search.RepForm')) {
			$this->data['RepForm'] = $this->Session->read('Search.RepForm');
			$conditions = $this->Rep->do_form_search($conditions, $this->data['RepForm']);
		}
		
		$this->paginate['Rep'] = array(
			'conditions' => $conditions,
			'contain' => array(),
			'limit' => 30
		);
		
		$reps = $this->paginate('Rep');
		$this->set('reps', $reps);
		
		$find = $this->paginate['Rep'];
		unset($find['limit']);
		$this->set('find', $find);
		
		$this->set('export_fields', $this->Rep->export_fields());
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
		
		if (!$id) {
			$this->Session->setFlash('Není zadáno, kterého repa chcete zobrazit');
			$this->redirect(array('controller' => 'reps', 'action' => 'index'));
		}
	
		$this->Rep->virtualFields['name'] = $this->Rep->name_field;
		$rep = $this->Rep->find('first', array(
			'conditions' => array('Rep.id' => $id),
			'contain' => array()
		));
		unset($this->Rep->virtualFields['name']);
		
		if (empty($rep)) {
			$this->Session->setFlash('Rep neexistuje');
			$this->redirect(array('controller' => 'reps', 'action' => 'index'));
		}
		
		$this->left_menu_list[] = 'rep_detailed';
		
		$this->set('rep', $rep);
		
		if (isset($this->data['Rep']['edit_rep_form'])) {
			if ($this->Rep->save($this->data)) {
				$this->Session->setFlash('Rep byl upraven');
				$this->redirect(array('controller' => 'reps', 'action' => 'view', $id, 'tab' => 1));
			} else {
				$this->Session->setFlash('Data se nepodařilo uložit. Opravte chyby ve formuláři a uložte jej znovu');
				unset($this->data['Rep']['password']);
			}
		} else {
			$this->data['Rep'] = $rep['Rep'];
			unset($this->data['Rep']['password']);
		}
		
		// TRANSAKCE S PENEZENKOU REPA
		// CS NASKLADNENI
		$wallet_transactions_paging = array();
		$wallet_transactions_find = array();
		$wallet_transactions_export_fields = array();
		$wallet_transactions = array();
		if ($this->Acl->check(array('model' => 'User', 'foreign_key' => $this->Session->read('Auth.User.id')), 'controllers/WalletTransactions/index')) {
			$wallet_transactions_conditions = array('WalletTransaction.rep_id' => $id);
				
			if (isset($this->params['named']['reset']) && $this->params['named']['reset'] == 'wallet_transactions') {
				$this->Session->delete('Search.WalletTransactionForm');
				$this->redirect(array('controller' => 'reps', 'action' => 'view', $id, 'tab' => 2));
			}
				
			// pokud chci vysledky vyhledavani
			if ( isset($this->data['WalletTransaction']['search_form']) && $this->data['WalletTransaction']['search_form'] == 1 ){
				$this->Session->write('Search.WalletTransactionForm', $this->data);
				$wallet_transactions_conditions = $this->Rep->WalletTransaction->do_form_search($wallet_transactions_conditions, $this->data);
			} elseif ($this->Session->check('Search.WalletTransactionForm')) {
				$this->data = $this->Session->read('Search.WalletTransactionForm');
				$wallet_transactions_conditions = $this->Rep->WalletTransaction->do_form_search($wallet_transactions_conditions, $this->data);
			}
				
			unset($this->passedArgs['sort']);
			unset($this->passedArgs['direction']);
			if (isset($this->params['named']['tab']) && $this->params['named']['tab'] == 2) {
				$this->passedArgs['sort'] = $sort_field;
				$this->passedArgs['direction'] = $sort_direction;
			}
				
			$this->paginate['WalletTransaction'] = array(
				'conditions' => $wallet_transactions_conditions,
				'contain' => array(),
				'limit' => 40,
				'joins' => array(
					array(
						'table' => 'users',
						'alias' => 'Rep',
						'type' => 'LEFT',
						'conditions' => array('Rep.user_type_id = 4 AND Rep.id = WalletTransaction.rep_id')
					),
					array(
						'table' => 'users',
						'alias' => 'User',
						'type' => 'LEFT',
						'conditions' => array('WalletTransaction.user_id = User.id')
					)
				),
				'fields' => array(
					'WalletTransaction.id',
					'WalletTransaction.created',
					'WalletTransaction.amount',
					'WalletTransaction.amount_after',
			
					'Rep.id',
					'Rep.first_name',
					'Rep.last_name',
					'Rep.wallet',
			
					'User.last_name'
				)
			);
			$wallet_transactions = $this->paginate('WalletTransaction');

			$wallet_transactions_paging = $this->params['paging'];
			$wallet_transactions_find = $this->paginate['WalletTransaction'];
			unset($wallet_transactions_find['limit']);
			unset($wallet_transactions_find['fields']);
				
			$wallet_transactions_export_fields = $this->Rep->WalletTransaction->export_fields();
				
		}
		$this->set('wallet_transactions', $wallet_transactions);
		$this->set('wallet_transactions_paging', $wallet_transactions_paging);
		$this->set('wallet_transactions_find', $wallet_transactions_find);
		$this->set('wallet_transactions_export_fields', $wallet_transactions_export_fields);
	}
	
	function user_add() {
		if (isset($this->data)) {
			if ($this->Rep->saveAll($this->data)) {
				$this->Session->setFlash('Rep byl vytvořen.');
				$this->redirect(array('controller' => 'reps', 'action' => 'index'));
			} else {
				$this->Session->setFlash('Repa se nepodařilo uložit, opravte chyby ve formuláři a opakujte prosím akci.');
				unset($this->data['Rep']['password']);
			}
		}
	}
	
	function user_edit($id = null) {
		if (!$id) {
			$this->Session->setFlash('Není zvolen rep, kterého chcete upravovat.');
			$this->redirect(array('controller' => 'reps', 'action' => 'index'));
		}
		
		$rep = $this->Rep->find('first', array(
			'conditions' => array('Rep.id' => $id),
			'contain' => array('RepAttribute')
		));
		
		if (empty($rep)) {
			$this->Session->setFlash('Požadovaný rep neexistuje.');
			$this->redirect(array('controller' => 'reps', 'action' => 'index'));
		}
		
		if (isset($this->data)) {
			if (empty($this->data['Rep']['password'])) {
				unset($this->data['Rep']['password']);
			}

			if ($this->Rep->saveAll($this->data)) {
				$this->Session->setFlash('Rep byl upraven.');
				$this->redirect(array('controller' => 'reps', 'action' => 'index'));
			} else {
				$this->Session->setFlash('Repa se nepodařilo upravit, opravte chyby ve formuláři a opakujte prosím akci.');
				unset($this->data['Rep']['password']);
			}
		} else {
			$this->data = $rep;
			unset($this->data['Rep']['password']);
		}
	}
	
	function user_delete($id = null) {
		if (!$id) {
			$this->Session->setFlash('Není zvolen Rep, kterého chcete smazat.');
			$this->redirect(array('controller' => 'reps', 'action' => 'index'));
		}
		
		$rep = $this->Rep->find('first', array(
			'conditions' => array('Rep.id' => $id),
			'contain' => array()
		));
		
		if (empty($rep)) {
			$this->Session->setFlash('Požadovaný rep neexistuje.');
			$this->redirect(array('controller' => 'reps', 'action' => 'index'));
		}
		
		if ($this->Rep->delete($id)) {
			$this->Session->setFlash('Rep byl odstraněn.');
		} else {
			$this->Session->setFlash('Rep se nepodařilo odstranit, opakujte prosím akci.');
		}
		$this->redirect(array('controller' => 'reps', 'action' => 'index'));
	}
	
	function user_autocomplete_list() {
		$term = null;
		if ($_GET['term']) {
			$term = $_GET['term'];
		}
	
		echo $this->Rep->autocomplete_list($this->user, $term);
		die();
	}
}
?>