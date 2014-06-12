<?php
class ProductVariantsTransaction extends AppModel {
	var $name = 'ProductVariantsTransaction';
	
	var $actsAs = array('Containable');
	
	var $belongsTo = array(
		'ProductVariant',
		'Transaction',
		'Sale' => array(
			'foreignKey' => 'transaction_id'
		),
		'DeliveryNote' => array(
			'foreignKey' => 'transaction_id'
		)
	);
	
	var $virtualFields = array(
		'abs_quantity' => 'ABS(`ProductVariantsTransaction`.`quantity`)',
		'total_price' => '`ProductVariantsTransaction`.`unit_price` * `ProductVariantsTransaction`.`quantity`',
		'abs_total_price' => 'ABS(`ProductVariantsTransaction`.`unit_price` * `ProductVariantsTransaction`.`quantity`)',
	);
	
	var $validate = array(
		'quantity' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Zadejte množství zboží'
			),
			'notZero' => array(
				'rule' => array('comparison', 'not equal', 0),
				'message' => 'Počet zboží nesmí být 0'
			)
		),
		'product_variant_id' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Vyberte zboží'
			)
		)
	);
	
	var $active = array();
	
	var $deleted = null;
	
	function afterSave($created) {
		die('ProductVariantsTransaction->afterSave()');
		$data = $this->data;
		// pokud vkladam novou polozku
		if ($created) {
			// najdu si produkt, ke kteremu se vztahuje
			$product = $this->Product->find('first', array(
				'conditions' => array('Product.id' => $data['ProductsTransaction']['product_id']),
				'contain' => array(),
				'fields' => array('Product.price', 'Product.margin')
			));

			if (empty($product)) {
				return false;
			} else {
				// vyplnim si cenu a marzi produktu v dobe vytvoreni polozky
				$this->data['ProductsTransaction']['unit_price'] = $product['Product']['price'];
				$this->data['ProductsTransaction']['product_margin'] = $product['Product']['margin'];
				$this->save($this->data);
			}
			
			// musim upravit stav polozek ve skladu odberatele
			$store_item = $this->Transaction->BusinessPartner->StoreItem->find('first', array(
				'conditions' => array(
					'StoreItem.product_id' => $data['ProductsTransaction']['product_id'],
					'StoreItem.business_partner_id' => $data['ProductsTransaction']['business_partner_id']
				),
				'contain' => array(),
				'fields' => array('StoreItem.id', 'StoreItem.quantity')
			));
			
			if (empty($store_item)) {
				$store_item = array(
					'StoreItem' => array(
						'product_id' => $data['ProductsTransaction']['product_id'],
						'quantity' => $data['ProductsTransaction']['quantity'],
						'business_partner_id' => $data['ProductsTransaction']['business_partner_id']
					)
				);
			} else {
				$store_item['StoreItem']['quantity'] += $data['ProductsTransaction']['quantity'];					
			}
			
			$this->Transaction->BusinessPartner->StoreItem->create();
			$this->Transaction->BusinessPartner->StoreItem->save($store_item);
			
			$this->active[] = $this->id;
		}

		return true;
	}
	
	// musim si zapamatovat, co mazu, abych to mohl po smazani odecist ze skladu odberatele
	function beforeDelete() {
		die('ProductVariantsTransaction->beforeDelete()');
		$this->deleted = $this->find('first', array(
			'conditions' => array('ProductsTransaction.id' => $this->id),
			'contain' => array(
				'Transaction' => array(
					'fields' => array('Transaction.id', 'Transaction.business_partner_id'),
					'TransactionType' => array(
						'fields' => array('TransactionType.subtract')
					)
				)
			)
		));
		
		return true;
	}
	
	function afterDelete() {
		die('ProductVariantsTransaction->afterDelete()');
		// ze skladu odberatele odectu, co jsem smazal z transakce
		$store_item = $this->Transaction->BusinessPartner->StoreItem->find('first', array(
			'conditions' => array(
				'StoreItem.business_partner_id' => $this->deleted['Transaction']['business_partner_id'],
				'StoreItem.product_id' => $this->deleted['ProductsTransaction']['product_id']
			),
			'contain' => array(),
			'fields' => array('StoreItem.id', 'StoreItem.quantity')
		));
		
		if (empty($store_item)) {
			$this->Transaction->BusinessPartner->StoreItem->create();
			$store_item = array(
				'StoreItem' => array(
					'business_partner_id' => $this->deleted['Transaction']['business_partner_id'],
					'product_id' => $this->deleted['ProductsTransaction']['product_id'],
					'quantity' => 0
				)
			);
		}
	
		$store_item['StoreItem']['quantity'] -= $this->deleted['ProductsTransaction']['quantity'];
		$this->Transaction->BusinessPartner->StoreItem->save($store_item);
	}

}