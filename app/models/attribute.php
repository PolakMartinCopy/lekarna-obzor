<?php
class Attribute extends AppModel {
	var $name = 'Attribute';
	
	var $actsAs = array('Containable');

	var $validate = array(
		'option_id' => array(
			'rule' => array('numeric'),
			'allowEmpty' => false,
			'message' => 'Pole nesmí zůstat prázdné!'
		)
	); // definuje pole, ktera musi byt validni

	var $belongsTo = array('Option');
	
	var $hasMany = array('AttributesSubproduct', 'OrderedProductsAttribute'); 
}
?>