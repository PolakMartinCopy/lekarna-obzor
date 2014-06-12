<?php 
class ProductVariant extends AppModel {
	var $name = 'ProductVariant';
	
	var $actsAs = array('Containable');
	
	var $belongsTo = array('Product');
	
	var $hasMany = array(
		'StoreItem',
		'ProductVariantsTransaction',
		'CSTransactionItem'
	);
	
	var $export_file = 'files/product_variants.csv';
	
	var $field_name = 'CONCAT(Product.vzp_code, " ", Product.name, " ", COALESCE(Product.referential_number, ""), " ", ProductVariant.exp, " ", ProductVariant.lot, " ", ProductVariant.meavita_price)';
	var $info = 'CONCAT(Product.name, ", LOT: ", ProductVariant.lot, ", EXP: ", ProductVariant.exp)';
	
	function beforeSave() {
		if (isset($this->data['ProductVariant']['price'])) {
			$this->data['ProductVariant']['price'] = str_replace(',', '.', $this->data['ProductVariant']['price']);
		}
		if (isset($this->data['Product']['margin'])) {
			$this->data['ProductVariant']['margin'] = str_replace(',', '.', $this->data['ProductVariant']['margin']);
		}
	
		return true;
	}
	
	// metoda pro smazani produktu - NEMAZE ale DEAKTIVUJE
	function delete($id = null) {
		if (!$id) {
			return false;
		}
	
		if ($this->hasAny(array('ProductVariant.id' => $id))) {
			$product_variant = array(
				'ProductVariant' => array(
					'id' => $id,
					'active' => false
				)
			);
			return $this->save($product_variant);
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
		if (!empty($data['Product']['referential_number'])) {
			$conditions[] = 'Product.referential_number LIKE \'%%' . $data['Product']['referential_number'] . '%%\'';
		}
		if (!empty($data['Product']['name'])) {
			$conditions[] = 'Product.name LIKE \'%%' . $data['Product']['name'] . '%%\'';
		}
		if (!empty($data['ProductVariant']['lot'])) {
			$conditions[] = 'ProductVariant.lot LIKE \'%%' . $data['ProductVariant']['lot'] . '%%\'';
		}
		if (!empty($data['ProductVariant']['exp'])) {
			$conditions[] = 'ProductVariant.exp LIKE \'%%' . $data['ProductVariant']['exp'] . '%%\'';
		}
	
		return $conditions;
	}
	
	function export_fields() {
		return array(
			array('field' => 'Product.id', 'position' => '["Product"]["id"]', 'alias' => 'Product.id'),
			array('field' => 'Product.name', 'position' => '["Product"]["name"]', 'alias' => 'Product.name'),
			array('field' => 'Product.en_name', 'position' => '["Product"]["en_name"]', 'alias' => 'Product.en_name'),
			array('field' => 'Product.vzp_code', 'position' => '["Product"]["vzp_code"]', 'alias' => 'Product.vzp_code'),
			array('field' => 'Product.group_code', 'position' => '["Product"]["group_code"]', 'alias' => 'Product.group_code'),
			array('field' => 'Product.referential_number', 'position' => '["Product"]["referential_number"]', 'alias' => 'Product.referential_number'),
			array('field' => 'Unit.name', 'position' => '["Unit"]["name"]', 'alias' => 'Unit.name'),
			array('field' => 'ProductVariant.meavita_price', 'position' => '["ProductVariant"]["meavita_price"]', 'alias' => 'ProductVariant.meavita_price'),
			array('field' => 'ProductVariant.meavita_quantity', 'position' => '["ProductVariant"]["meavita_quantity"]', 'alias' => 'ProductVariant.meavita_quantity'),
			array('field' => 'ProductVariant.meavita_margin', 'position' => '["ProductVariant"]["meavita_margin"]', 'alias' => 'ProductVariant.meavita_margin'),
			array('field' => 'ProductVariant.lot', 'position' => '["ProductVariant"]["lot"]', 'alias' => 'ProductVariant.lot'),
			array('field' => 'ProductVariant.exp', 'position' => '["ProductVariant"]["exp"]', 'alias' => 'ProductVariant.exp'),
		);
	}
	
	function autocomplete_list($term = null, $language_id) {
		$conditions = array('ProductVariant.active' => true);
		// pokud chci data v cestine
		$field_name = $this->field_name;
		
		if ($term) {
			$conditions[$field_name . ' LIKE'] = '%' . $term . '%';
		}

		$this->virtualFields['name'] = $field_name;
		$this->virtualFields['info'] = $this->info;
		$product_variants = $this->find('all', array(
			'conditions' => $conditions,
			'contain' => array(
				'Product' => array(
					'TaxClass'
				)
			)
		));
		unset($this->virtualFields['name']);
		unset($this->virtualFields['info']);

		$autocomplete_list = array();
		foreach ($product_variants as $product_variant) {
			$autocomplete_list[] = array(
				'label' => trim($product_variant['ProductVariant']['name']),
				'value' => $product_variant['ProductVariant']['id'],
				'name' => $product_variant['ProductVariant']['info'],
				'vat' => $product_variant['Product']['TaxClass']['value']
			);
		}
		return json_encode($autocomplete_list);
	}
}
?>