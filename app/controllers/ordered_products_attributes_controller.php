<?php
class OrderedProductsAttributesController extends AppController {
	var $name = 'OrderedProductsAttributes';

	var $scaffold;

	
	function repair() {
		$opas = $this->OrderedProductsAttribute->find('all', array(
			'conditions' => array('attribute_id' => 0),
			'contain' => array()
		));
		
		foreach ($opas as $opa) {
			debug($opa);
			// musim najit Value a Option s danym value_name a option_name, protoze value_id a option_id nejsou vyplnene
			$attribute = $this->OrderedProductsAttribute->Attribute->find('first', array(
				'conditions' => array('Option.name' => $opa['OrderedProductsAttribute']['option_name'], 'Attribute.value' => $opa['OrderedProductsAttribute']['value_name']),
				'contain' => array('Option')
			));

			if (empty($attribute)) {
				debug($value); debug($option);
			} else {
				$opa['OrderedProductsAttribute']['attribute_id'] = $attribute['Attribute']['id'];
				$this->OrderedProductsAttribute->save($opa);
			}
		}
		die('hotovo');
	}
}
?>