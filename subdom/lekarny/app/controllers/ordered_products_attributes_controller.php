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
			// musim najit Value a Option s danym value_name a option_name, protoze value_id a option_id nejsou vyplnene
			$query = "
				SELECT *
				FROM `values` AS `Value`
				WHERE Value.name LIKE '" . $opa['OrderedProductsAttribute']['value_name'] . "'
				LIMIT 1
			";
			
			$value = $this->OrderedProductsAttribute->query($query);
			// tady to skonci, pokud pro danou variantu objednaneho produktu neni value v databazi
			if (!isset($value[0])) {
				debug($value);
				debug($opa['OrderedProductsAttribute']['value_name']); die();
			}
			$value = $value[0];
			
			$option = $this->OrderedProductsAttribute->Attribute->Option->find('first', array(
				'conditions' => array('name' => $opa['OrderedProductsAttribute']['option_name']),
				'contain' => array()
			));
			
			$attribute = $this->OrderedProductsAttribute->Attribute->find('first', array(
				'conditions' => array('option_id' => $option['Option']['id'], 'value LIKE' => $value['Value']['name']),
				'contain' => array()
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