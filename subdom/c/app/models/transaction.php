<?php
class Transaction extends AppModel {
	var $name = 'Transaction';
	
	var $actsAs = array('Containable');
	
	var $belongsTo = array(
		'BusinessPartner',
		'TransactionType',
		'User'
	);
	
	var $hasMany = array(
		'ProductsTransaction' => array(
			'dependent' => true
		)	
	);
	
	var $validate = array(
		'business_partner_id' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Vyberte odběratele'
			)
		),
		'code' => array(
			'isUnique' => array(
				'rule' => 'isUnique',
				'message' => 'Transakce s uvedeným číslem dokladu již existuje'
			)
		),
		'user_id' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Není zadán uživatel, který vkládá transakci'
			)
		),
		'date' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Zadejte datum'
			)
		)
	);
	
	var $virtualFields = array(
		'quantity' => '`ProductsTransaction`.`quantity`',
		'abs_quantity' => 'ABS(`ProductsTransaction`.`quantity`)',
		'total_price' => '`ProductsTransaction`.`unit_price` * `ProductsTransaction`.`quantity`',
		'abs_total_price' => 'ABS(`ProductsTransaction`.`unit_price` * `ProductsTransaction`.`quantity`)',
		'margin' => 'ROUND((`ProductsTransaction`.`product_margin` * `ProductsTransaction`.`unit_price` * `ProductsTransaction`.`quantity`) / 100, 2)',
		'abs_margin' => 'ABS(ROUND((`ProductsTransaction`.`product_margin` * `ProductsTransaction`.`unit_price` * `ProductsTransaction`.`quantity`) / 100, 2))'
	);
	
	var $export_file = 'files/transactions.csv';

	function afterFind($results, $primary) {
		if ($this->alias == 'Transaction') {
			foreach ($results as &$result) {
				if (isset($result['TransactionType']['subtract']) && $result['TransactionType']['subtract']) {
					if (isset($result['ProductsTransaction']['quantity'])) {
						$result['ProductsTransaction']['quantity'] = -$result['ProductsTransaction']['quantity'];
					}
					if (isset($result['ProductsTransaction']['total_price'])) {
						$result['ProductsTransaction']['total_price'] = -$result['ProductsTransaction']['total_price'];
					}
				}
			}
		}
		return $results;
	}
	
	function beforeSave() {
		// uprava tvaru data z dd.mm.YYYY na YYYY-mm-dd
		if (isset($this->data[$this->alias]['date']) && preg_match('/\d{2}\.\d{2}\.\d{4}/', $this->data[$this->alias]['date'])) {
			$date = explode('.', $this->data[$this->alias]['date']);

			if (!isset($date[2]) || !isset($date[1]) || !isset($date[0])) {
				return false;
			}
			$this->data[$this->alias]['date'] = $date[2] . '-' . $date[1] . '-' . $date[0];
		}
		// odnastavim id odberatele, pokud nemam nastaveno jmeno odberatele
		if (isset($this->data[$this->alias]['business_partner_id']) && isset($this->data[$this->alias]['business_partner_name']) && empty($this->data[$this->alias]['business_partner_name'])) {
			$this->data[$this->alias]['business_partner_id'] = null;
		}

		return true;
	}
	
	function afterSave($created) {
		if ($created) {
			$prefix = 'd';
			if ($this->alias == 'Sale') {
				$prefix = 'p';
			}
			
			// vygeneruju cislo dokladu
			$transaction = array(
				$this->alias => array(
					'id' => $this->id,
					'code' => $prefix . $this->id
				)
			);

			$this->create();
			$this->save($transaction);
		}
		
		return true;
	}
	
	function afterDelete() {
		// smazu taky pdf soubor z disku
		if (file_exists(DL_FOLDER . $this->id . '.pdf')) {
			return unlink(DL_FOLDER . $this->id . '.pdf');
		}
		
		return true;
	}
	
	function do_form_search($conditions = array(), $data) {
		if (!empty($data['BusinessPartner']['name'])) {
			$conditions[] = 'BusinessPartner.name LIKE \'%%' . $data['BusinessPartner']['name'] . '%%\'';
		}
		if (!empty($data['BusinessPartner']['ico'])) {
			$conditions[] = 'BusinessPartner.ico LIKE \'%%' . $data['BusinessPartner']['ico'] . '%%\'';
		}
		if (!empty($data['BusinessPartner']['dic'])) {
			$conditions[] = 'BusinessPartner.dic LIKE \'%%' . $data['BusinessPartner']['dic'] . '%%\'';
		}
		if ( !empty($data['Address']['street']) ){
			$conditions[] = 'Address.street LIKE \'%%' . $data['Address']['street'] . '%%\'';
		}
		if ( !empty($data['Address']['city']) ){
			$conditions[] = 'Address.city LIKE \'%%' . $data['Address']['city'] . '%%\'';
		}
		if ( !empty($data['Address']['region']) ){
			$conditions[] = 'Address.region LIKE \'%%' . $data['Address']['region'] . '%%\'';
		}
		if (!empty($data[$this->alias]['date_from'])) {
			$date_from = explode('.', $data[$this->alias]['date_from']);
			$date_from = $date_from[2] . '-' . $date_from[1] . '-' . $date_from[0];
			$conditions[$this->alias . '.date >='] = $date_from;
		}
		if (!empty($data[$this->alias]['date_to'])) {
			$date_to = explode('.', $data[$this->alias]['date_to']);
			$date_to = $date_to[2] . '-' . $date_to[1] . '-' . $date_to;
			$conditions[$this->alias . '.date <='] = $date_to;
		}
		if (!empty($data[$this->alias]['code'])) {
			$conditions[] = $this->alias . '.code LIKE \'%%' . $data[$this->alias]['code'];
		}
		if (!empty($data[$this->alias]['user_id'])) {
			$conditions[$this->alias . '.user_id'] = $data[$this->alias]['user_id'];
		}
		if (!empty($data['Product']['group_code'])) {
			$conditions[] = 'Product.group_code LIKE \'%%' . $data['Product']['group_code'] . '%%\'';
		}
		if (!empty($data['Product']['vzp_code'])) {
			$conditions[] = 'Product.vzp_code LIKE \'%%' . $data['Product']['vzp_code'] . '%%\'';
		}

		return $conditions;
	}
	
	function export_fields() {
		$export_fields = array(
			array('field' => 'ProductsTransaction.id', 'position' => '["ProductsTransaction"]["id"]', 'alias' => 'ProductsTransaction.id'),
			array('field' => $this->alias . '.date', 'position' => '["' . $this->alias . '"]["date"]', 'alias' => $this->alias . '.date'),
			array('field' => $this->alias . '.code', 'position' => '["' . $this->alias . '"]["code"]', 'alias' => $this->alias . '.code'),
			array('field' => 'TransactionType.name', 'position' => '["TransactionType"]["name"]', 'alias' => 'TransactionType.name'),
			array('field' => 'BusinessPartner.id', 'position' => '["BusinessPartner"]["id"]', 'alias' => 'BusinessPartner.id'),
			array('field' => 'BusinessPartner.name', 'position' => '["BusinessPartner"]["name"]', 'alias' => 'BusinessPartner.name'),
			array('field' => 'BusinessPartner.ico', 'position' => '["BusinessPartner"]["ico"]', 'alias' => 'BusinessPartner.ico'),
			array('field' => 'Address.street', 'position' => '["Address"]["street"]', 'alias' => 'Address.street'),
			array('field' => 'Address.number', 'position' => '["Address"]["number"]', 'alias' => 'Address.number'),
			array('field' => 'Address.city', 'position' => '["Address"]["city"]', 'alias' => 'Address.city'),
			array('field' => 'Address.zip', 'position' => '["Address"]["zip"]', 'alias' => 'Address.zip'),
			array('field' => 'Address.region', 'position' => '["Address"]["region"]', 'alias' => 'Address.region'),
			array('field' => 'Product.id', 'position' => '["Product"]["id"]', 'alias' => 'Product.id'),
			array('field' => 'Product.name', 'position' => '["Product"]["name"]', 'alias' => 'Product.name'),
			array('field' => 'Product.vzp_code', 'position' => '["Product"]["vzp_code"]', 'alias' => 'Product.vzp_code'),
			array('field' => 'Product.group_code', 'position' => '["Product"]["group_code"]', 'alias' => 'Product.group_code'),
			array('field' => 'ProductsTransaction.quantity', 'position' => '["ProductsTransaction"]["quantity"]', 'alias' => 'ProductsTransaction.quantity'),
			array('field' => 'ProductsTransaction.unit_price', 'position' => '["ProductsTransaction"]["unit_price"]', 'alias' => 'ProductsTransaction.unit_price'),
			array('field' => 'ProductsTransaction.product_margin', 'position' => '["ProductsTransaction"]["product_margin"]', 'alias' => 'ProductsTransaction.product_margin'),
			array('field' => '`ProductsTransaction`.`unit_price` * `ProductsTransaction`.`quantity` AS `ProductsTransaction__total_price`', 'position' => '["ProductsTransaction"]["total_price"]', 'alias' => 'ProductsTransaction.total_price'),
			array('field' => 'ROUND((`ProductsTransaction`.`product_margin` * `ProductsTransaction`.`unit_price` * `ProductsTransaction`.`quantity`) / 100, 2) AS Transaction__margin', 'position' => '["Transaction"]["margin"]', 'alias' => 'Transaction.margin'),
			array('field' => 'Unit.shortcut', 'position' => '["Unit"]["shortcut"]', 'alias' => 'Unit.shortcut'),
			array('field' => 'User.id', 'position' => '["User"]["id"]', 'alias' => 'User.id'),
			array('field' => 'TransactionType.id', 'position' => '["TransactionType"]["id"]', 'alias' => 'TransactionType.id'),
			array('field' => 'TransactionType.name', 'position' => '["TransactionType"]["name"]', 'alias' => 'TransactionType.name'),
			array('field' => 'User.last_name', 'position' => '["User"]["last_name"]', 'alias' => 'User.last_name')
		);
		
		return $export_fields;
	}
}