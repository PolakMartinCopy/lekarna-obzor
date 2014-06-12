<?php 
App::import('Controller', 'Transactions');
class SalesController extends TransactionsController {
	var $name = 'Sales';
	
	var $left_menu_list = array('sales');
	
	function beforeFilter() {
		parent::beforeFilter();
		$this->set('active_tab', 'sales');
		$this->set('left_menu_list', $this->left_menu_list);
	}
	
	function user_add() {
		if (isset($this->data)) {
			if (isset($this->data['ProductsTransaction'])) {
				// odstranim z formu prazdne radky pro vlozeni produktu
				foreach ($this->data['ProductsTransaction'] as $index => &$products_transaction) {
					if (empty($products_transaction['product_id']) && empty($products_transaction['quantity'])) {
						unset($this->data['ProductsTransaction'][$index]);
					} else {
						// k produktum si zapamatuju id odberatele
						$products_transaction['business_partner_id'] = $this->data['Sale']['business_partner_id'];
						$products_transaction['quantity'] =  -$products_transaction['quantity'];
					}
				}
				// pokud nemam zadne radky s produkty, neulozim
				if (empty($this->data['ProductsTransaction'])) {
					$this->Session->setFlash('Prodej neobsahuje žádné produkty a nelze jej proto uložit');
				} else {
					if ($this->Sale->saveAll($this->data, array('validate' => 'only'))) {
						// pred vlozenim musim smazat vsechny transakce, ktere po tomto nasleduji, pak ulozit tuto transakci a nasledne znovu vlozit vsechny smazane transakce
						// plati pro transakce daneho uzivatele
						App::import('Model', 'Transaction');
						$this->Transaction = new Transaction;
						// podivam se, jestli mam v systemu pro daneho uzivatele transakce, ktere vlozeny s datem PO datu vlozeni teto transakce, tyto transakce si zapamatuju v property modelu
						$date = $this->data['Sale']['date'];
						$date = explode('.', $date);
						$date = $date[2] . '-' . $date[1] . '-' . $date[0];
						$date_time = $date . ' ' . $this->data['Sale']['time']['hour'] . ':' . $this->data['Sale']['time']['min'] . ':00';
						$future_transactions = $this->Transaction->find('all', array(
							'conditions' => array(
								'CONCAT(Transaction.date, " ", Transaction.time) >' => $date_time,
								'Transaction.business_partner_id' => $this->data['Sale']['business_partner_id']
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
							foreach ($transaction['ProductsTransaction'] as &$products_transaction) {
								$products_transaction['business_partner_id'] = $transaction['Transaction']['business_partner_id'];
							}
						}
						
						// smazu transakce po teto transakci, tim se mi prepocita sklad odberatele
						foreach ($future_transactions as $future_transaction) {
							$this->Transaction->delete($future_transaction['Transaction']['id']);
						}
						if ($this->Sale->saveAll($this->data)) {
							// natahnu si model DeliveryNote
							App::import('Model', 'DeliveryNote');
							$this->DeliveryNote = new DeliveryNote;
							// pokud jsem vytvarel zaroven dodaci list
							if ($this->Sale->delivery_note_created) {
								// vytvorim pdf dodaciho listu
								$this->DeliveryNote->pdf_generate($this->Sale->delivery_note_created);
							}
							
							foreach ($future_transactions as $future_transaction) {
								if ($this->Transaction->saveAll($future_transaction)) {
									if ($future_transaction['Transaction']['transaction_type_id'] == 1) {
										// vytvorim pdf dodaciho listu
										$this->DeliveryNote->pdf_generate($future_transaction['Transaction']['id']);
									}
								}
							}
							
							$this->Session->setFlash('Prodej byl uložen.');
							if (isset($this->params['named']['business_partner_id'])) {
								$this->redirect(array('controller' => 'business_partners', 'action' => 'view', $this->params['named']['business_partner_id'], 'tab' => 11));
							} else {
								$this->redirect(array('action' => 'index'));
							}
						}
					} else {
						foreach ($this->data['ProductsTransaction'] as &$products_transaction) {
							$products_transaction['quantity'] =  -$products_transaction['quantity'];
						}
						$this->Session->setFlash('Prodej se nepodařilo uložit, opravte chyby ve formuláři a opakujte prosím akci');
					}
				}
			} else {
				$this->Session->setFlash('Prodej neobsahuje žádné produkty a nelze jej proto uložit');
			}
		} else {
			$this->data['Sale']['date'] = date('d.m.Y');
		}
		// pokud jsem na form pro pridani prisel z detailu obchodniho partnera, predvyplnim pole
		if (isset($this->params['named']['business_partner_id'])) {
			$business_partner = $this->Sale->BusinessPartner->find('first', array(
				'conditions' => array('BusinessPartner.id' => $this->params['named']['business_partner_id']),
				'contain' => array(),
				'fields' => array('BusinessPartner.id', 'BusinessPartner.name')
			));
			$this->set('business_partner', $business_partner);
			$this->data['Sale']['business_partner_name'] = $business_partner['BusinessPartner']['name'];
			$this->data['Sale']['business_partner_id'] = $business_partner['BusinessPartner']['id'];
		}
		
		$this->set('user', $this->user);
	}
}
?>