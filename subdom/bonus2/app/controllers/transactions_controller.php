<?php
class TransactionsController extends AppController {
	var $name = 'Transactions';
	
	function user_csv($customer_id) {
		// pokud chci data nejak serazena
		$order = array('date' => 'desc');
		if (isset($this->params['named']['sort'])) {
			$order = array($this->params['named']['sort'] => 'asc');
			if (isset($this->params['named']['direction']) && $this->params['named']['direction'] == 'desc') {
				$order = array($this->params['named']['sort'] => 'desc');
			}
		}

		$transactions = $this->Transaction->find('all', array(
			'conditions' => array('customer_id' => $customer_id),
			'order' => $order
		));
		
		$this->Transaction->create_csv($transactions);
		$this->redirect('/' . $this->Transaction->export_file);
		

	}
	
}
?>