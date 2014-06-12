<?php
class StoreItem extends AppModel {
	var $name = 'StoreItem';
	
	var $actsAs = array('Containable');
	
	var $belongsTo = array(
		'BusinessPartner',
		'ProductVariant'
	);
	
	var $virtualFields = array(
		'total_quantity' => 'SUM(StoreItem.quantity)',
		'total_price' => 'SUM(ProductVariant.meavita_price * StoreItem.quantity)',
		'item_total_price' => 'ProductVariant.meavita_price * StoreItem.quantity'
	);
	
	var $export_file = 'files/store_items.csv';
	
/* 	function beforeSave() {
		if (isset($this->data['StoreItem']['id']) && $this->data['StoreItem']['quantity'] == 0) {
			$this->delete($this->data['StoreItem']['id']);
			return false;
		}
		return true;
	} */
	
	function do_form_search($conditions = array(), $data) {
		if (!empty($data['BusinessPartner']['name'])) {
			$conditions[] = 'BusinessPartner.name LIKE \'%%' . $data['BusinessPartner']['name'] . '%%\'';
		}
		if ( !empty($data['Address']['city']) ){
			$conditions[] = 'Address.city LIKE \'%%' . $data['Address']['city'] . '%%\'';
		}
		if ( !empty($data['Address']['region']) ){
			$conditions[] = 'Address.region LIKE \'%%' . $data['Address']['region'] . '%%\'';
		}
		if (!empty($data['Product']['vzp_code'])) {
			$conditions[] = 'Product.vzp_code LIKE \'%%' . $data['Product']['vzp_code'] . '%%\'';
		}
		if (!empty($data['Product']['name'])) {
			$conditions[] = 'Product.name LIKE \'%%' . $data['Product']['name'] . '%%\'';
		}
		if (!empty($data['Product']['group_code'])) {
			$conditions[] = 'Product.group_code LIKE \'%%' . $data['Product']['group_code'] . '%%\'';
		}
	
		return $conditions;
	}
	
	function export_fields() {
		return array(
			array('field' => 'StoreItem.id', 'position' => '["StoreItem"]["id"]', 'alias' => 'StoreItem.id'),
			array('field' => 'Product.id', 'position' => '["Product"]["id"]', 'alias' => 'Product.id'),
			array('field' => 'Product.vzp_code', 'position' => '["Product"]["vzp_code"]', 'alias' => 'Product.vzp_code'),
			array('field' => 'Product.group_code', 'position' => '["Product"]["group_code"]', 'alias' => 'Product.group_code'),
			array('field' => 'Product.referential_number', 'position' => '["Product"]["referential_number"]', 'alias' => 'Product.referential_number'),
			array('field' => 'Product.name', 'position' => '["Product"]["name"]', 'alias' => 'Product.name'),
			array('field' => 'StoreItem.quantity', 'position' => '["StoreItem"]["quantity"]', 'alias' => 'StoreItem.quantity'),
			array('field' => 'Unit.shortcut', 'position' => '["Unit"]["shortcut"]', 'alias' => 'Unit.shortcut'),
			array('field' => 'ProductVariant.lot', 'position' => '["ProductVariant"]["lot"]', 'alias' => 'ProductVariant.lot'),
			array('field' => 'ProductVariant.exp', 'position' => '["ProductVariant"]["exp"]', 'alias' => 'ProductVariant.exp'),
			array('field' => 'ProductVariant.meavita_price', 'position' => '["ProductVariant"]["meavita_price"]', 'alias' => 'ProductVariant.meavita_price'),
			array('field' => 'StoreItem.item_total_price', 'position' => '["StoreItem"]["item_total_price"]', 'alias' => 'StoreItem.item_total_price'),
		);
	}
}