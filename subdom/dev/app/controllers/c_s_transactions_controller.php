<?php 
class CSTransactionsController extends AppController {
	var $name = 'CSTransactions';
	
	var $left_menu_list = array('c_s_transactions');
	
	function beforeFilter() {
		parent::beforeFilter();
		$this->set('left_menu_list', $this->left_menu_list);
		$this->set('active_tab', 'central_storing');
	}
	
	function user_index() {
		if (isset($this->params['named']['reset'])) {
			$this->Session->delete('Search.CSTransactionForm');
			$this->redirect(array('controller' => $this->params['controller'], 'action' => 'index'));
		}
		
		$conditions = array();
		// pokud chci vysledky vyhledavani
		if (isset($this->data['CSTransaction']['search_form']) && $this->data['CSTransaction']['search_form'] == 1){
			$this->Session->write('Search.CSTransactionForm', $this->data);
			$conditions = $this->CSTransaction->do_form_search($conditions, $this->data);
		} elseif ($this->Session->check('Search.CSTransactionForm')) {
			$this->data = $this->Session->read('Search.CSTransactionForm');
			$conditions = $this->CSTransaction->do_form_search($conditions, $this->data);
		}
		
		$this->paginate = array(
			'conditions' => $conditions,
			'limit' => 30,
			'contain' => array(),
			'fields' => array('*'),
			'order' => array('CSTransaction.date_of_issue' => 'desc')
		);
		$transactions = $this->paginate();
		$this->set('transactions', $transactions);
		
		$this->set('find', $this->paginate);
		
		$export_fields = $this->CSTransaction->export_fields();
		$this->set('export_fields', $export_fields);
		
		// seznam uzivatelu pro select ve filtru
		$users_conditions = array();
		if ($this->user['User']['user_type_id'] == 3) {
			$users_conditions = array('User.id' => $this->user['User']['id']);
		}
		App::import('Model', 'User');
		$this->CSTransaction->User = &new User;
		$users = $this->CSTransaction->User->find('all', array(
			'conditions' => $users_conditions,
			'contain' => array(),
			'fields' => array('User.id', 'User.first_name', 'User.last_name')
		));
		$users = Set::combine($users, '{n}.User.id', array('{0} {1}', '{n}.User.first_name', '{n}.User.last_name'));
		$this->set('users', $users);
	}
}
?>