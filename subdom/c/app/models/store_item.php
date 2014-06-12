<?php
class StoreItem extends AppModel {
	var $name = 'StoreItem';
	
	var $actsAs = array('Containable');
	
	var $belongsTo = array(
		'BusinessPartner',
		'Product'
	);
	
	var $virtualFields = array(
		'total_quantity' => 'SUM(StoreItem.quantity)',
		'total_price' => 'SUM(Product.price * StoreItem.quantity)',
		'item_total_price' => 'Product.price * StoreItem.quantity'
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
}