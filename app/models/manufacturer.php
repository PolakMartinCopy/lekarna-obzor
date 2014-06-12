<?php
class Manufacturer extends AppModel {
	var $name = 'Manufacturer';

	var $validate = array(
		'name' => array(
			'rule' => array('minLength', 1),
			'required' => true,
			'message' => 'Pole pro název výrobce nesmí zůstat prázdné!'
		),
		'www_address' => array(
			'rule' => array('url'),
			'allowEmpty' => true,
			'message' => 'Uveďte www adresu ve správném formátu, nebo nechte pole prázdné.'
		)
	);
	
	var $hasMany = array('Product');
	
	function listing() {
		$manufacturers = $this->find('list',
			array('order' => array('name' => 'asc')
		));
		return array('manufacturers' => $manufacturers);
	}
}
?>