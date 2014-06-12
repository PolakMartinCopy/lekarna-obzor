<?php
class Subproduct extends AppModel {
	var $name = 'Subproduct';

	var $actsAs = array('Containable');

	
	var $belongsTo = array(
		'Product',
		'Availability'
	);

	

	var $hasMany = array(
		'AttributesSubproduct' => array(
			'dependent' => true
		),
		'CartsProduct'
	);
	
	function countSortOrder($attribute_id, $product_id){
		$attribute = $this->Attribute->read(null, $attribute_id);
		$bottom_level_left = $attribute['Attribute']['option_id'] * 10;
		$bottom_level_right = ($attribute['Attribute']['option_id'] + 1) * 10;
		$conditions = array(
			'product_id' => $product_id,
			'sort_order' => 'BETWEEN ' . $bottom_level_left . ' AND ' . $bottom_level_right
		);

		$bottom_level = $this->find($conditions, 'MAX(sort_order) as bottom_level');

		// jestlize atribut neni jeste k produktu prirazeny,
		// vraci mi to prazdny zaznam
		if ( empty($bottom_level[0]['bottom_level']) ){
			return $bottom_level_left;
		}
		return $bottom_level[0]['bottom_level'];
	}
	
	function optionFilter($params){
		$filter = '';
		if ( isset($params['option_id']) ){
			$filter = 'option_id:' . $params['option_id'];
		}
		return $filter;
	}
}
?>