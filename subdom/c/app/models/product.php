<?php
class Product extends AppModel {
	var $name = 'Product';
	
	var $actsAs = array('Containable');
	
	var $belongsTo = array('Unit');
	
	var $hasMany = array(
		'ProductsTransaction',
		'StoreItem'
	);
	
	var $virtualFields = array(
		'info' => 'CONCAT(Product.vzp_code, " ", Product.name)'	
	);
	
	var $validate = array(
		'name' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Zadejte název zboží'
			)
		),
		'price' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Zadejte cenu zboží'
			)
		)
	);
	
	var $export_file = 'files/products.csv';
	
	function beforeSave() {
		if (isset($this->data['Product']['price'])) {
			$this->data['Product']['price'] = str_replace(',', '.', $this->data['Product']['price']);
		}
		if (isset($this->data['Product']['margin'])) {
			$this->data['Product']['margin'] = str_replace(',', '.', $this->data['Product']['margin']);
		}
		
		return true;
	}
	
	// metoda pro smazani produktu - NEMAZE ale DEAKTIVUJE
	function delete($id = null) {
		if (!$id) {
			return false;
		}
		
		if ($this->hasAny(array('Product.id' => $id))) {
			$product = array(
				'Product' => array(
					'id' => $id,
					'active' => false
				)	
			);
			return $this->save($product);
		} else {
			return false;
		}
	}
	
	function do_form_search($conditions, $data) {
		if (!empty($data['Product']['vzp_code'])) {
			$conditions[] = 'Product.vzp_code LIKE \'%%' . $data['Product']['vzp_code'] . '%%\'';
		}
		if (!empty($data['Product']['group_code'])) {
			$conditions[] = 'Product.group_code LIKE \'%%' . $data['Product']['group_code'] . '%%\'';
		}
		if (!empty($data['Product']['name'])) {
			$conditions[] = 'Product.name LIKE \'%%' . $data['Product']['name'] . '%%\'';
		}
	
		return $conditions;
	}
	
	function autocomplete_list($term = null) {
		$conditions = array('Product.active' => true);
		if ($term) {
			$conditions['CONCAT(Product.vzp_code, " ", Product.name) LIKE'] = '%' . $term . '%';
		}
		
		$products = $this->find('all', array(
			'conditions' => $conditions,
			'contain' => array(),
			'fields' => array('Product.id', 'Product.info')
		));
		
		$autocomplete_list = array();
		foreach ($products as $product) {
			$autocomplete_list[] = array(
				'label' => $product['Product']['info'],
				'value' => $product['Product']['id']
			);
		}
		return json_encode($autocomplete_list);
	}
}