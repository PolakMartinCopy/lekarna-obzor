<?php
class Product extends AppModel {
	var $name = 'Product';
	
	var $actsAs = array('Containable');
	
	var $belongsTo = array('Unit', 'TaxClass');
	
	var $hasMany = array(
		'ProductVariant'
	);
	
	var $validate = array(
		'name' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Zadejte název zboží'
			)
		),
		'name' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Zadejte anglický název zboží'
			)
		)
	);
	
	var $export_file = 'files/products.csv';
	
	function afterFind($results) {
		foreach ($results as &$result) {
			if (isset($result['Product']) && is_array($result['Product']) && array_key_exists('name', $result['Product']) && !isset($result['Product']['en_name'])) {
				$result['Product']['en_name'] = $result['Product']['name'];
			}
		}
		return $results;
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