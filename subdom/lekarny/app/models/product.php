<?
class Product extends AppModel{
	var $name = 'Product';
	
	var $actsAs = array('Containable');
	
	var $validate = array(
		'name' => array(
			'rule' => 'notEmpty',
			'message' => 'Název produktu nesmí být prázdný.'
		),
		'vzp_code' => array(
			'rule' => 'notEmpty',
			'message' => 'Kód VZP nesmí být prázdný.'
		),
		'price' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Pole pro cenu produktu nesmí být prázné.'
			)
		)
	);
	
	var $hasAndBelongsToMany = array('Category');
	
	var $hasMany = array('Subproduct', 'Image', 'ProductDocument');
	
	var $hasOne = array('CategoriesProduct');
	
	var $belongsTo = array('TaxClass', 'Manufacturer', 'Availability', 'Cart'); // 

	/**
	 * z atributu produktu tvori vsechny jejich mozne kombinace 
	 *
	 * @param pole atributu $array
	 * @return pole vsech moznych kombinaci vstupnich atributu
	 */
	function combine($array) {
		if (empty($array)) {
			return array();
		}
		$res = array();
		$first = current($array);
		array_shift($array);
		$tail = $array;
		if (empty($tail)) {
			foreach ($first as $item) {
				$res[] = array($item);
			}
		} else {
			foreach ($first as $item) {
				foreach ($this->combine($tail) as $j) {
					$res[] = array_merge(array($item), $j);
				}
			}
		}
		return $res;
	}

	function get_subproducts($id){
		$options = $this->Subproduct->AttributesSubproduct->Attribute->Option->find('list');
		
		// projdu si existujici atributy a k nim si priradim subprodukty
		$subs = array();
		$hasAttributes = false;
		$subproducts = $this->Subproduct->find('list', array(
			'conditions' => array('product_id' => $id),
			'contain' => array()
		));
		//
		foreach ( $options as $option => $value ){
			$attributes = $this->Subproduct->AttributesSubproduct->find('all',
				array(
					'conditions' => array(
						'AttributesSubproduct.subproduct_id' => $subproducts,
						'Attribute.option_id' => $option
					),
					'contain' => array(
						'Attribute'
					),
					'order' => array('Attribute.sort_order' => 'asc'),
					'fields' => array('DISTINCT Attribute.id', 'Attribute.value')
				)
			);
			if ( $this->Subproduct->AttributesSubproduct->getNumRows() > 0 ){
				$hasAttributes = true;
			}
			$subs[$option] = array('Option' => array('name' => $options[$option], 'id' => $option));
			foreach ( $attributes as $attribute ){
				$subs[$option]['Value'][] = array(
					'id' => $attribute['Attribute']['id'],
					'name' => $options[$option],
					'value' => $attribute['Attribute']['value'],
				);
			}
		}
		if ( !$hasAttributes ){
			$subs = null;
		}
		
		return $subs;
	}
}
?>