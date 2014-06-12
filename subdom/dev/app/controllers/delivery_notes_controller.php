<?php
App::import('Controller', 'Transactions');
class DeliveryNotesController extends TransactionsController {
	var $name = 'DeliveryNotes';
	
	var $left_menu_list = array('delivery_notes');
	
	function beforeFilter() {
		parent::beforeFilter();
		$this->set('active_tab', 'delivery_notes');
		$this->set('left_menu_list', $this->left_menu_list);
		
		$this->Auth->allow('view_pdf');
	}
	
	function user_add() {
		if (isset($this->data)) {
			if (isset($this->data['ProductsTransaction'])) {
				foreach ($this->data['ProductsTransaction'] as $index => $products_transaction) {
					if (empty($products_transaction['product_id']) && empty($products_transaction['quantity'])) {
						unset($this->data['ProductsTransaction'][$index]);
					} else {
						$this->data['ProductsTransaction'][$index]['business_partner_id'] = $this->data['DeliveryNote']['business_partner_id'];
					}
				}
				if (empty($this->data['ProductsTransaction'])) {
					$this->Session->setFlash('Dodací list neobsahuje žádné produkty a nelze jej proto uložit');
				} else {
					if ($this->DeliveryNote->saveAll($this->data, array('validate' => 'only'))) {
						// pred vlozenim DL musim smazat vsechny transakce, ktere po tomto nasleduji, pak ulozit tuto transakci a nasledne znovu vlozit vsechny smazane transakce
						// plati pro transakce daneho uzivatele
						App::import('Model', 'Transaction');
						$this->Transaction = new Transaction;
						// podivam se, jestli mam v systemu pro daneho uzivatele transakce, ktere vlozeny s datem PO datu vlozeni teto transakce, tyto transakce si zapamatuju v property modelu
						$date = $this->data['DeliveryNote']['date'];
						$date = explode('.', $date);
						$date = $date[2] . '-' . $date[1] . '-' . $date[0];
						$date_time = $date . ' ' . $this->data['DeliveryNote']['time']['hour'] . ':' . $this->data['DeliveryNote']['time']['min'] . ':00';
						$future_transactions = $this->Transaction->find('all', array(
							'conditions' => array(
								'CONCAT(Transaction.date, " ", Transaction.time) >' => $date_time,
								'Transaction.business_partner_id' => $this->data['DeliveryNote']['business_partner_id']
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
						if ($this->DeliveryNote->saveAll($this->data)) {
							
							// vytvorim pdf dodaciho listu
							$this->DeliveryNote->pdf_generate($this->DeliveryNote->id);
							
							foreach ($future_transactions as $future_transaction) {
								if ($this->Transaction->saveAll($future_transaction)) {
									if ($future_transaction['Transaction']['transaction_type_id'] == 1) {
										// vytvorim pdf dodaciho listu
										$this->DeliveryNote->pdf_generate($future_transaction['Transaction']['id']);
									}
								}
							}
							
							$this->Session->setFlash('Dodací list byl uložen.');
							if (isset($this->params['named']['business_partner_id'])) {
								$this->redirect(array('controller' => 'business_partners', 'action' => 'view', $this->params['named']['business_partner_id'], 'tab' => 10));
							} else {
								$this->redirect(array('action' => 'index'));
							}
						}
					} else {
						$this->Session->setFlash('Dodací list se nepodařilo uložit, opravte chyby ve formuláři a opakujte prosím akci');
					}
				}
			} else {
				$this->Session->setFlash('Dodací list neobsahuje žádné produkty a nelze jej proto uložit');
			}
		} else {
			$this->data['DeliveryNote']['date'] = date('d.m.Y');
		}
		// pokud jsem na form pro pridani prisel z detailu obchodniho partnera, predvyplnim pole
		if (isset($this->params['named']['business_partner_id'])) {
			$business_partner = $this->DeliveryNote->BusinessPartner->find('first', array(
				'conditions' => array('BusinessPartner.id' => $this->params['named']['business_partner_id']),
				'contain' => array(),
				'fields' => array('BusinessPartner.id', 'BusinessPartner.name')
			));
			$this->set('business_partner', $business_partner);
			$this->data['DeliveryNote']['business_partner_name'] = $business_partner['BusinessPartner']['name'];
			$this->data['DeliveryNote']['business_partner_id'] = $business_partner['BusinessPartner']['id'];
		}
		
		$this->set('user', $this->user);
	}
	
	function view_pdf($id = null) {
		if (!$id) {
			$this->Session->setFlash('Není zadán dodaci list, který chcete zobrazit');
			$this->redirect(array('action' => 'index'));
		}
		
		if (!$this->DeliveryNote->hasAny(array('DeliveryNote.id' => $id))) {
			$this->Session->setFlash('Dodací list, který chcete zobrazit, neexistuje');
			$this->redirect(array('action' => 'index'));
		}
		
		$delivery_note = $this->DeliveryNote->find('first', array(
			'conditions' => array(
				'DeliveryNote.id' => $id,
				'Address.address_type_id' => 1
			),
			'contain' => array(
				'ProductsTransaction' => array(
					'fields' => array('ProductsTransaction.id', 'ProductsTransaction.quantity'),
					'Product' => array(
						'fields' => array('Product.id', 'Product.name', 'Product.vzp_code'),
						'Unit' => array(
							'fields' => array('Unit.id', 'Unit.shortcut')
						)
					)
				),
				'User' => array(
					'fields' => array('User.id', 'User.first_name', 'User.last_name')
				)
			),
			'joins' => array(
				array(
					'table' => 'business_partners',
					'alias' => 'BusinessPartner',
					'type' => 'left',
					'conditions' => array('DeliveryNote.business_partner_id = BusinessPartner.id')	
				),
				array(
					'table' => 'addresses',
					'alias' => 'Address',
					'type' => 'left',
					'conditions' => array('BusinessPartner.id = Address.business_partner_id')
				)
			),
			'fields' => array(
				'DeliveryNote.id', 'DeliveryNote.date', 'DeliveryNote.time', 'DeliveryNote.code',
				'BusinessPartner.id', 'BusinessPartner.name', 'BusinessPartner.ico',
				'Address.id', 'Address.street', 'Address.number', 'Address.o_number', 'Address.city', 'Address.zip'
			)
		));
		$this->set('delivery_note', $delivery_note);

		// aktualni stav skladu odberatele
		$store_items = $this->DeliveryNote->BusinessPartner->StoreItem->find('all', array(
			'conditions' => array(
				'StoreItem.business_partner_id' => $delivery_note['BusinessPartner']['id'],
				'StoreItem.quantity >' => 0
			),
			'contain' => array(),
			'joins' => array(
				array(
					'table' => 'products',
					'alias' => 'Product',
					'type' => 'LEFT',
					'conditions' => array('StoreItem.product_id = Product.id')
				),
				array(
					'table' => 'units',
					'alias' => 'Unit',
					'type' => 'left',
					'conditions' => array('Product.unit_id = Unit.id')
				)
			),
			'fields' => array('StoreItem.id', 'StoreItem.quantity', 'Product.id', 'Product.name', 'Product.vzp_code', 'Unit.id', 'Unit.shortcut')
		));
		$this->set('store_items', $store_items);
		
		// zbozi vydane dopredu - zbozi na sklade z minusovym stavem mnozstvi
		$forward_products = $this->DeliveryNote->BusinessPartner->StoreItem->find('all', array(
			'conditions' => array(
				'StoreItem.business_partner_id' => $delivery_note['BusinessPartner']['id'],
				'StoreItem.quantity <' => 0
			),
			'contain' => array(),
			'joins' => array(
				array(
					'table' => 'products',
					'alias' => 'Product',
					'type' => 'LEFT',
					'conditions' => array('StoreItem.product_id = Product.id')
				),
				array(
					'table' => 'units',
					'alias' => 'Unit',
					'type' => 'left',
					'conditions' => array('Product.unit_id = Unit.id')
				)
			),
			'fields' => array('StoreItem.id', 'StoreItem.quantity', 'Product.id', 'Product.name', 'Product.vzp_code', 'Unit.id', 'Unit.shortcut')
		));
		$this->set('forward_products', $forward_products);

		// ids produktu na tomto dodacim listi
		$delivery_note_product_ids = Set::extract('/product_id', $delivery_note['ProductsTransaction']);
		
		// zbozi objednane v minulosti danym odberatelem
		// mam id odberatele, datum vystaveni tohoto dodaciho listu a seznam produktu, ktere jsou soucasti tohoto dodaciho listu
		// chci 20 produktu, ktere tento odberatel objednal driv, nez byl vystaven tento dodaci list a zaroven aby v seznamu nebyly produkty, ktere jsou soucasti tohoto dodaciho listu
		$products_history = $this->DeliveryNote->find('all', array(
			'conditions' => array(
				'DeliveryNote.business_partner_id' => $delivery_note['BusinessPartner']['id'],
				'DeliveryNote.date <=' => $delivery_note['DeliveryNote']['date'],
				'DeliveryNote.time <=' => $delivery_note['DeliveryNote']['time'],
				'Product.id NOT IN (' . implode(',', $delivery_note_product_ids) . ')'
			),
			'contain' => array(),
			'joins' => array(
				array(
					'table' => 'products_transactions',
					'alias' => 'ProductsTransaction',
					'type' => 'LEFT',
					'conditions' => array('DeliveryNote.id = ProductsTransaction.transaction_id')
				),
				array(
					'table' => 'products',
					'alias' => 'Product',
					'type' => 'LEFT',
					'conditions' => array('Product.id = ProductsTransaction.product_id')
				)
			),
			'fields' => array('DISTINCT Product.id', 'Product.vzp_code', 'Product.name'),
			'limit' => 20
		));
		$this->set('products_history', $products_history);
		
		$this->layout = 'pdf'; //this will use the pdf.ctp layout
	}
}