<?php
class ProductsTransactionsController extends AppController {
	var $name = 'ProductsTransactions';
	
	function  user_delete($id = null) {
		if (!$id) {
			$this->Session->setFlash('Není zadáno, kterou položku transakce chcete smazat');
			$this->redirect(array('controller' => 'transactions', 'action' => 'index'));
		}
		
		$conditions = array('ProductsTransaction.id' => $id);
		if ($this->user['User']['user_type_id'] == 3) {
			$conditions['Transaction.user_id'] = $this->user['User']['id'];
		}
		
		$products_transaction = $this->ProductsTransaction->find('first', array(
			'conditions' => $conditions,
			'contain' => array('Transaction'),
			'fields' => array('ProductsTransaction.id', 'Transaction.id', 'Transaction.transaction_type_id', 'Transaction.date', 'Transaction.time', 'Transaction.business_partner_id')
		));

		if (empty($products_transaction)) {
			$this->Session->setFlash('Položka transakce, kterou chcete smazat, neexistuje');
			$this->redirect(array('controller' => 'transactions', 'action' => 'index'));
		}
		
		$products_transaction_count = $this->ProductsTransaction->find('count', array(
			'conditions' => array('ProductsTransaction.transaction_id' => $products_transaction['Transaction']['id'])
		));
		
		// pred smazanim polozky musim smazat vsechny transakce, ktere nasleduji po transakci, ke ktere polozka patri pak ulozit tuto transakci a nasledne znovu vlozit vsechny smazane transakce
		// plati pro transakce daneho uzivatele
		// podivam se, jestli mam v systemu pro daneho uzivatele transakce, ktere vlozeny s datem PO datu vlozeni teto transakce, tyto transakce si zapamatuju
		$date_time = $products_transaction['Transaction']['date'] . ' ' . $products_transaction['Transaction']['time'];
		$future_transactions = $this->ProductsTransaction->Transaction->find('all', array(
			'conditions' => array(
				'CONCAT(Transaction.date, " ", Transaction.time) >' => $date_time,
				'Transaction.business_partner_id' => $products_transaction['Transaction']['business_partner_id'],
				'Transaction.id !=' => $products_transaction['Transaction']['id']
			),
			'contain' => array(
				'ProductsTransaction' => array(
					'fields' => array('ProductsTransaction.id', 'ProductsTransaction.created', 'ProductsTransaction.product_id', 'ProductsTransaction.transaction_id', 'ProductsTransaction.quantity', 'ProductsTransaction.unit_price', 'ProductsTransaction.product_margin')
				)
			),
			'fields' => array('Transaction.id', 'Transaction.created', 'Transaction.code', 'Transaction.business_partner_id', 'Transaction.date', 'Transaction.time', 'Transaction.transaction_type_id', 'Transaction.user_id'),
			'order' => array(
				'Transaction.date' => 'asc',
				'Transaction.time' => 'asc'
			)
		));
			
		foreach ($future_transactions as &$transaction) {
			foreach ($transaction['ProductsTransaction'] as &$a_products_transaction) {
				$a_products_transaction['business_partner_id'] = $transaction['Transaction']['business_partner_id'];
			}
		}

		// smazu transakce po teto transakci, tim se mi prepocita sklad odberatele
		foreach ($future_transactions as $future_transaction) {
			$this->ProductsTransaction->Transaction->delete($future_transaction['Transaction']['id']);
		}
		
		// pokud nema transakce zadne dalsi polozky, smazu ji a s tim se smaze i polozka transakce		
		if ($products_transaction_count == 1) {
			if ($this->ProductsTransaction->Transaction->delete($products_transaction['Transaction']['id'])) {
				$this->Session->setFlash('Transakce byla odstraněna');
			} else {
				$this->Session->setFlash('Transakci se nepodařilo odstranit');
			}
			if (isset($this->params['named']['business_partner_id'])) {
				$redirect = array('controller' => 'business_partners', 'action' => 'view', $this->params['named']['business_partner_id']);
			} else {
				$controller = 'transactions';
				if ($products_transaction['Transaction']['transaction_type_id'] == 1) {
					$controller = 'delivery_notes';
				} elseif ($products_transaction['Transaction']['transaction_type_id'] == 3) {
					$controller = 'sales';
				}
				$redirect = array('controller' => $controller, 'action' => 'index');
			}
		} else {
			if ($this->ProductsTransaction->delete($id)) {
				// pokud jsem updatoval dodaci list
				if ($products_transaction['Transaction']['transaction_type_id'] == 1) {
					// pregeneruju pdf dodaciho listu
					$this->ProductsTransaction->DeliveryNote->pdf_generate($products_transaction['Transaction']['id']);
				}
				
				$this->Session->setFlash('Položka transakce byla odstraněna');
			} else {
				$this->Session->setFlash('Položku transakce se nepodařilo odstranit');
			}
			if (isset($this->params['named']['business_partner_id'])) {
				$redirect = array('controller' => 'business_partners', 'action' => 'view', $this->params['named']['business_partner_id']);
			} else {
				$controller = 'transactions';
				if ($products_transaction['Transaction']['transaction_type_id'] == 1) {
					$controller = 'delivery_notes';
				} elseif ($products_transaction['Transaction']['transaction_type_id'] == 3) {
					$controller = 'sales';
				}
				$redirect = array('controller' => $controller, 'action' => 'index');
			}
		}

		foreach ($future_transactions as $future_transaction) {
			if ($this->ProductsTransaction->Transaction->saveAll($future_transaction)) {
				if ($future_transaction['Transaction']['transaction_type_id'] == 1) {
					// vytvorim pdf dodaciho listu
					$this->ProductsTransaction->DeliveryNote->pdf_generate($future_transaction['Transaction']['id']);
				}
			}
		}
		
		$this->redirect($redirect);
	}
}