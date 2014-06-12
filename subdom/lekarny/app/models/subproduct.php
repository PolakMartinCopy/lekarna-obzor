<?php
class Subproduct extends AppModel {
	var $name = 'Subproduct';

	var $actsAs = array('Containable');

	var $belongsTo = array('Product', 'Availability');
	
	var $hasMany = array('AttributesSubproduct', 'CartsProduct');

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

	function price($pairs, $product_id){
		$price = 0;
		if ( !empty($pairs) ){
			// najdu si idecka vsech atributu, ktere ma produkt
			$attributes = $this->Attribute->find('all', array(
				'conditions' => array(
					'OR' => $pairs
				),
				'contain' => array()
			));
			
			$attributes = Set::extract('/Attribute/id', $attributes);
	
			// najdu si definice subproduktu, ktere maji dane idecka atributu
			$subs = $this->find('all', array(
				'conditions' => array(
					'attribute_id' => $attributes,
					'product_id' => $product_id
				),
				'contain' => array()
			));
			
			foreach ( $subs as $s ){
				if ( $s['Subproduct']['price'] > $price ){
					$price = $s['Subproduct']['price'];
				}
			}
		}
		return $price;
	}
}
?>